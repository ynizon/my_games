<?php

namespace Tests\Feature\Api;

use App\Models\Card;
use App\Models\Game;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Jeux crees par les migrations ; les cartes importees des XML sont remplacees par un jeu d'essai
        DB::table('gam_games')->update(['status' => 1]);
        DB::table('gam_games')->whereIn('name', ['Test', 'Petits meurtres et faits divers'])->update(['status' => 0]);
        DB::table('gam_cards')->delete();
        foreach (["Villageois", "Loup Garou", "Voyante", "Voleur"] as $name) {
            $this->card(4, $name, json_encode(['word1' => 'Standard', 'description' => '', 'persons' => '']));
        }
    }

    private function card(int $gameId, string $name, string $description = '[]', array $attributes = []): Card
    {
        $card = new Card();
        $card->forceFill(array_merge(['game_id' => $gameId, 'name' => $name, 'lang' => 'fr', 'description' => $description, 'status' => 1], $attributes));
        $card->save();

        return $card;
    }

    private function user(string $role = 'User', int $status = 1): User
    {
        $user = User::create(['name' => 'Joueur', 'email' => uniqid().'@test.fr', 'password' => Hash::make('secret')]);
        $user->status = $status;
        $user->save();
        $user->attachRole(Role::where('name', $role)->firstOrFail());

        return $user;
    }

    public function test_games_lists_only_active_playable_games_with_settings(): void
    {
        $response = $this->getJson('/api/v1/games')->assertOk();

        $this->assertEqualsCanonicalizing(
            ['lestroismanches', 'checklist', 'motsinterdits', 'lesloupsduvillage', 'croquisexpress'],
            array_column($response->json('data'), 'slug')
        );
        $taboo = collect($response->json('data'))->firstWhere('slug', 'motsinterdits');
        $this->assertSame(['min' => 2, 'max' => 4], $taboo['settings']['nbteams']);
        $this->assertSame(5, $taboo['nb_words']);
    }

    public function test_cards_returns_validated_cards_with_decoded_words_and_version(): void
    {
        $this->card(3, 'Abribus', json_encode(['word1' => 'Car', 'word2' => 'Arrêt', 'word3' => '', 'description' => '']));
        $this->card(3, 'En attente', '[]', ['status' => 0]);

        $response = $this->getJson('/api/v1/cards?lang=fr&game_id=3')->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertSame(['Car', 'Arrêt'], $response->json('data.0.words'));
        $this->assertNotEmpty($response->json('meta.version'));
        $this->assertSame($response->json('meta.version'), $this->getJson('/api/v1/cards/version?lang=fr')->json('version'));
    }

    public function test_cards_rejects_unknown_lang(): void
    {
        $this->get('/api/v1/cards?lang=xx')->assertStatus(422)->assertJsonValidationErrors('lang');
    }

    public function test_werewolf_cards_expose_dealing_rules(): void
    {
        $this->card(4, 'Loup Garou Blanc', json_encode(['word1' => 'Standard']));
        $this->card(4, 'Frères et Soeurs', json_encode(['word1' => 'Personnages']));

        $cards = collect($this->getJson('/api/v1/werewolf/cards')->assertOk()->json('data'))->keyBy('name');

        $this->assertSame('villager', $cards['Villageois']['role']);
        $this->assertSame('wolf', $cards['Loup Garou']['role']);
        $this->assertTrue($cards['Loup Garou']['is_wolf']);
        $this->assertSame('special', $cards['Voyante']['role']);
        $this->assertFalse($cards['Voyante']['is_wolf']);
        $this->assertTrue($cards['Loup Garou Blanc']['is_wolf']);
        $this->assertSame(5, $cards['Frères et Soeurs']['copies']);
        $this->assertSame('Personnages', $cards['Frères et Soeurs']['extension']);
    }

    public function test_werewolf_deck_deals_one_card_per_player(): void
    {
        $seer = Card::where('name', 'Voyante')->first();

        $response = $this->postJson('/api/v1/werewolf/deck', ['nbplayers' => 8, 'nbwolfs' => 2, 'cards' => [$seer->id]])->assertOk();

        $names = array_count_values(array_column($response->json('deck'), 'name'));
        ksort($names);
        $this->assertSame(['Loup Garou' => 2, 'Villageois' => 5, 'Voyante' => 1], $names);
        $this->assertSame(2, $response->json('nbWolvesTotal'));
        $this->assertFalse($response->json('hasThief'));
    }

    public function test_werewolf_deck_validates_bounds(): void
    {
        $this->postJson('/api/v1/werewolf/deck', ['nbplayers' => 3, 'nbwolfs' => 2])
            ->assertStatus(422)->assertJsonValidationErrors('nbplayers');
    }

    public function test_register_login_me_and_logout(): void
    {
        $this->postJson('/api/v1/auth/register', ['name' => 'Alice', 'email' => 'alice@test.fr', 'password' => 'secret', 'device_name' => 'pixel'])
            ->assertCreated()
            ->assertJsonPath('user.email', 'alice@test.fr')
            ->assertJsonPath('user.permissions.card_propose', true)
            ->assertJsonPath('user.permissions.card_edit', false);

        $token = $this->postJson('/api/v1/auth/login', ['email' => 'alice@test.fr', 'password' => 'secret', 'device_name' => 'pixel'])
            ->assertOk()->json('token');

        $this->withToken($token)->getJson('/api/v1/me')->assertOk()->assertJsonPath('data.name', 'Alice');
        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();
        // Seul le jeton de la connexion est revoque, celui de l'inscription reste valide
        [$id] = explode('|', $token);
        $this->assertFalse(DB::table('personal_access_tokens')->where('id', $id)->exists());
        $this->assertSame(1, DB::table('personal_access_tokens')->count());
    }

    public function test_login_rejects_bad_password_and_suspended_account(): void
    {
        $user = $this->user();
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'wrong', 'device_name' => 'pixel'])
            ->assertStatus(422);

        $suspended = $this->user('User', 0);
        $this->postJson('/api/v1/auth/login', ['email' => $suspended->email, 'password' => 'secret', 'device_name' => 'pixel'])
            ->assertStatus(422);
    }

    public function test_suspended_user_token_is_rejected_and_revoked(): void
    {
        $user = $this->user();
        $token = $user->createToken('pixel')->plainTextToken;
        $user->status = 0;
        $user->save();

        $this->withToken($token)->getJson('/api/v1/me')->assertForbidden();
        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }

    public function test_protected_routes_require_a_token(): void
    {
        $this->get('/api/v1/me')->assertUnauthorized();
        $this->get('/api/v1/my/cards')->assertUnauthorized();
    }

    public function test_user_proposes_a_card_pending_validation(): void
    {
        $user = $this->user();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/my/cards', [
            'name' => 'Plage', 'game_id' => 3, 'lang' => 'fr', 'status' => 1, 'words' => ['Sable', 'Mer', 'Soleil', 'Serviette', 'Vague'],
        ])->assertCreated();

        $card = Card::findOrFail($response->json('data.id'));
        $this->assertSame($user->id, (int) $card->user_id);
        $this->assertSame(0, (int) $card->status);
        $this->assertSame(['Sable', 'Mer', 'Soleil', 'Serviette', 'Vague'], $response->json('data.words'));
        $this->assertCount(1, $this->getJson('/api/v1/my/cards')->json('data'));
    }

    public function test_user_cannot_touch_cards_of_others(): void
    {
        $other = $this->card(3, 'Autre', '[]', ['user_id' => 999]);
        Sanctum::actingAs($this->user());

        $this->getJson('/api/v1/my/cards/'.$other->id)->assertForbidden();
        $this->putJson('/api/v1/my/cards/'.$other->id, ['name' => 'X', 'game_id' => 3, 'lang' => 'fr'])->assertForbidden();
        $this->deleteJson('/api/v1/my/cards/'.$other->id)->assertForbidden();
        $this->assertNotNull(Card::find($other->id));
    }

    public function test_admin_can_update_any_card_and_keep_it_validated(): void
    {
        $card = $this->card(3, 'Ancien', json_encode(['word1' => 'a', 'description' => 'note']), ['user_id' => 999]);
        Sanctum::actingAs($this->user('Admin'));

        $this->putJson('/api/v1/my/cards/'.$card->id, ['name' => 'Nouveau', 'game_id' => 3, 'lang' => 'fr', 'status' => 1, 'words' => ['b']])
            ->assertOk()->assertJsonPath('data.name', 'Nouveau')->assertJsonPath('data.status', 1);

        $description = json_decode(Card::find($card->id)->description, true);
        $this->assertSame('b', $description['word1']);
        $this->assertSame('note', $description['description']);
    }

    public function test_profile_update(): void
    {
        Sanctum::actingAs($this->user());

        $this->putJson('/api/v1/me', ['name' => 'Bob', 'email' => 'bob@test.fr', 'role' => 'Admin'])
            ->assertOk()->assertJsonPath('data.name', 'Bob')->assertJsonPath('data.roles', ['User']);
    }
    public function test_delete_account_requires_the_password(): void
    {
        $user = $this->user();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/me', ['password' => 'wrong'])->assertStatus(422)->assertJsonValidationErrors('password');
        $this->deleteJson('/api/v1/me')->assertStatus(422)->assertJsonValidationErrors('password');
        $this->assertDatabaseHas('gam_users', ['id' => $user->id]);
    }

    public function test_delete_account_removes_user_data_and_anonymizes_validated_cards(): void
    {
        $user = $this->user();
        $token = $user->createToken('pixel')->plainTextToken;
        $validated = $this->card(3, 'Validee', '[]', ['user_id' => $user->id, 'created_by' => 'Joueur']);
        $pending = $this->card(3, 'En attente', '[]', ['user_id' => $user->id, 'status' => 0]);

        $this->withToken($token)->deleteJson('/api/v1/me', ['password' => 'secret'])->assertNoContent();

        $this->assertDatabaseMissing('gam_users', ['id' => $user->id]);
        $this->assertDatabaseMissing('gam_role_user', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
        $this->assertDatabaseMissing('gam_cards', ['id' => $pending->id]);
        $this->assertDatabaseHas('gam_cards', ['id' => $validated->id, 'user_id' => null, 'created_by' => '']);

        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_web_profile_deletion_and_public_legal_pages(): void
    {
        $this->withoutVite();
        $user = $this->user();

        $this->actingAs($user)->get('/profile')->assertOk()->assertSee('Supprimer mon compte');
        $this->actingAs($user)->delete('/profile', ['delete_password' => 'wrong'])->assertRedirect('/profile');
        $this->assertDatabaseHas('gam_users', ['id' => $user->id]);

        $this->actingAs($user)->delete('/profile', ['delete_password' => 'secret'])->assertRedirect('/suppression-compte');
        $this->assertDatabaseMissing('gam_users', ['id' => $user->id]);
        $this->assertGuest();

        $this->get('/confidentialite')->assertOk()->assertSee('Règles de confidentialité');
        $this->get('/suppression-compte')->assertOk()->assertSee('Supprimer mon compte');
    }

    public function test_deploy_endpoint_requires_the_token(): void
    {
        $this->postJson('/api/deploy')->assertNotFound();

        config(['app.deploy_token' => 'jeton-ci']);
        $this->postJson('/api/deploy', [], ['X-Deploy-Token' => 'faux'])->assertNotFound();
        $this->postJson('/api/deploy', [], ['X-Deploy-Token' => 'jeton-ci'])->assertOk()->assertJsonPath('status', 'ok');
    }
}
