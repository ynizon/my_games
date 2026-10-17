package fr.gameandme.games.ui

import fr.gameandme.games.game.GameSlug

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AccountCircle
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Remove
import androidx.compose.material.icons.filled.Sync
import androidx.compose.material3.Checkbox
import androidx.compose.material3.FilledTonalIconButton
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateMapOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.ViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.lifecycle.viewModelScope
import fr.gameandme.games.GamesApplication
import fr.gameandme.games.data.Catalog
import fr.gameandme.games.data.Game
import fr.gameandme.games.game.Werewolf
import fr.gameandme.games.ui.games.RoleImage
import kotlinx.coroutines.launch

/** Description des jeux (textes du site). */
private val DESCRIPTIONS = mapOf(
    GameSlug.TIMES_UP to "Jeu d'ambiance en 3 manches où vous devez deviner des personnages célèbres : description, un seul mot, puis mime.",
    GameSlug.BRAINSTORM to "Trouvez une dizaine de références à partir d'un thème. Plus vous en trouvez, plus vous gagnez de points.",
    GameSlug.TABOO to "Faites deviner des mots sans jamais dire les mots interdits. Soyez rapides !",
    GameSlug.PICTIONARY to "Faites deviner des mots en les dessinant. Soyez rapides !",
    GameSlug.WEREWOLF to "Jeu de bluff où les villageois doivent démasquer les loups-garous. L'application distribue les rôles.",
)

class HomeViewModel(private val app: GamesApplication) : ViewModel() {
    val catalog = app.catalog.catalog
    var syncing by mutableStateOf(false)
        private set
    var message by mutableStateOf<String?>(null)
        private set

    init {
        sync(force = false)
    }

    fun sync(force: Boolean = true) {
        if (syncing) return
        syncing = true
        viewModelScope.launch {
            app.catalog.sync(force = force)
                .onSuccess { message = null }
                .onFailure {
                    val local = app.catalog.catalog.value
                    message = if (local?.bundled == true) {
                        "Hors connexion : les cartes fournies avec l'application sont utilisées."
                    } else if (local != null) {
                        "Hors connexion : les cartes déjà téléchargées sont utilisées."
                    } else {
                        it.message ?: "Erreur de connexion."
                    }
                }
            syncing = false
        }
    }
}

@Composable
fun HomeScreen(vm: HomeViewModel, onGame: (Game) -> Unit, onAccount: () -> Unit) {
    val catalog by vm.catalog.collectAsStateWithLifecycle()
    ScreenScaffold(
        title = "MyGames",
        actions = {
            IconButton(onClick = { vm.sync() }, enabled = !vm.syncing) {
                Icon(Icons.Default.Sync, contentDescription = "Mettre à jour les cartes")
            }
            IconButton(onClick = onAccount) {
                Icon(Icons.Default.AccountCircle, contentDescription = "Mon compte")
            }
        },
    ) { padding ->
        LazyColumn(
            modifier = Modifier.padding(padding).fillMaxSize(),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            // Emplacement fixe : la liste ne bouge pas pendant la synchronisation
            item {
                if (vm.syncing) {
                    LinearProgressIndicator(modifier = Modifier.fillMaxWidth())
                } else {
                    Spacer(Modifier.height(4.dp))
                }
            }
            vm.message?.let { message ->
                item { Text(message, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodyMedium) }
            }
            val current = catalog
            if (current == null) {
                if (!vm.syncing) {
                    item {
                        Panel {
                            Text("Les cartes doivent être téléchargées une première fois.", textAlign = TextAlign.Center)
                            BigButton("Réessayer", { vm.sync() })
                        }
                    }
                }
            } else {
                items(current.games, key = { it.id }) { game ->
                    GameTile(game, cardCount(current, game)) { onGame(game) }
                }
                item {
                    Text(
                        "Les cartes sont gardées sur le téléphone : vous pouvez jouer sans connexion.",
                        style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.fillMaxWidth(),
                    )
                }
            }
        }
    }
}

private fun cardCount(catalog: Catalog, game: Game) =
    if (game.slug == GameSlug.WEREWOLF) catalog.werewolfCards.size else catalog.cardsFor(game.id).size

@Composable
private fun GameTile(game: Game, cards: Int, onClick: () -> Unit) {
    Panel(modifier = Modifier.clickable(onClick = onClick)) {
        Text(game.name, fontSize = 22.sp, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary)
        DESCRIPTIONS[game.slug]?.let { Text(it, textAlign = TextAlign.Center, style = MaterialTheme.typography.bodyMedium) }
        Text("$cards cartes", style = MaterialTheme.typography.labelLarge, color = MaterialTheme.colorScheme.secondary)
    }
}

// ---------------------------------------------------------------- Reglages

private val SETTING_LABELS = mapOf(
    "nbteams" to "Nombre d'équipes",
    "nbcards" to "Nombre de manches",
    "nbsets" to "Nombre de manches",
    "nbplayers" to "Nombre de joueurs",
    "nbwolfs" to "Nombre de loups-garous",
)

class SetupViewModel(app: GamesApplication) : ViewModel() {
    val catalog = app.catalog.catalog
}

@Composable
fun SetupScreen(
    vm: SetupViewModel,
    slug: String,
    onBack: () -> Unit,
    onPlay: (settings: Map<String, Int>, selectedRoles: Set<Int>) -> Unit,
) {
    val catalog by vm.catalog.collectAsStateWithLifecycle()
    val game = catalog?.games?.firstOrNull { it.slug == slug }
    val values = remember { mutableStateMapOf<String, Int>() }
    val selected = remember { mutableStateMapOf<Int, Boolean>() }
    LaunchedEffect(game) {
        game?.settings?.forEach { (key, bounds) -> values.putIfAbsent(key, bounds.min) }
    }

    ScreenScaffold(title = game?.name ?: "", onBack = onBack) { padding ->
        Column(
            modifier = Modifier
                .padding(padding)
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
        ) {
            if (game == null) return@Column
            Panel {
                game.settings.forEach { (key, bounds) ->
                    Stepper(
                        label = SETTING_LABELS[key] ?: key,
                        value = values[key] ?: bounds.min,
                        min = bounds.min,
                        max = bounds.max,
                        onChange = { values[key] = it },
                    )
                }
            }

            var canPlay = true
            if (slug == GameSlug.WEREWOLF) {
                val roles = catalog?.werewolfCards.orEmpty()
                val selectedIds = selected.filterValues { it }.keys
                val seats = Werewolf.specialSeats(roles, selectedIds) + (values["nbwolfs"] ?: 0)
                val players = values["nbplayers"] ?: 0
                canPlay = seats <= players
                Panel {
                    Text("Rôles spéciaux", fontWeight = FontWeight.Bold, fontSize = 18.sp)
                    Text(
                        "$seats/$players joueurs avec un rôle (loups compris), les autres seront Villageois.",
                        color = if (canPlay) MaterialTheme.colorScheme.onSurface else MaterialTheme.colorScheme.error,
                        textAlign = TextAlign.Center,
                    )
                    roles.filter { it.role == Werewolf.ROLE_SPECIAL }
                        .groupBy { it.extension ?: "Standard" }
                        .forEach { (extension, cards) ->
                            Text(extension, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary,
                                modifier = Modifier.fillMaxWidth())
                            cards.forEach { card ->
                                Row(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .clickable { selected[card.id] = selected[card.id] != true },
                                    verticalAlignment = Alignment.CenterVertically,
                                ) {
                                    Checkbox(checked = selected[card.id] == true, onCheckedChange = { selected[card.id] = it })
                                    RoleImage(card, 36)
                                    Spacer(Modifier.width(8.dp))
                                    Text(if (card.copies > 1) "${card.name} (${card.copies} joueurs)" else card.name)
                                }
                            }
                        }
                }
            }

            BigButton("Jouer", { onPlay(values.toMap(), selected.filterValues { it }.keys) }, enabled = canPlay)
        }
    }
}

@Composable
private fun Stepper(label: String, value: Int, min: Int, max: Int, onChange: (Int) -> Unit) {
    Row(modifier = Modifier.fillMaxWidth(), verticalAlignment = Alignment.CenterVertically) {
        Text(label, modifier = Modifier.weight(1f), style = MaterialTheme.typography.titleMedium)
        FilledTonalIconButton(onClick = { onChange(value - 1) }, enabled = value > min) {
            Icon(Icons.Default.Remove, contentDescription = "Moins")
        }
        Text("$value", modifier = Modifier.width(48.dp), textAlign = TextAlign.Center, fontSize = 22.sp, fontWeight = FontWeight.Bold)
        FilledTonalIconButton(onClick = { onChange(value + 1) }, enabled = value < max) {
            Icon(Icons.Default.Add, contentDescription = "Plus")
        }
    }
}
