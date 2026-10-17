package fr.gameandme.games.game

import fr.gameandme.games.data.Card
import kotlin.random.Random

/** Roles distribues aux joueurs du Loup-Garou (meme regle que WerewolfDeck::build cote serveur). */
data class WerewolfDeal(
    /** Une carte par joueur, dans l'ordre des joueurs. */
    val deck: List<Card>,
    val nbWolvesTotal: Int,
    val hasThief: Boolean,
    /** Joueur (1..n) designe capitaine. */
    val captain: Int,
)

object Werewolf {
    const val ROLE_VILLAGER = "villager"
    const val ROLE_WOLF = "wolf"
    const val ROLE_SPECIAL = "special"
    private const val THIEF = "Voleur"

    /** Nombre de joueurs occupes par les roles speciaux choisis (Freres et Soeurs = 5). */
    fun specialSeats(cards: List<Card>, selectedIds: Set<Int>) =
        cards.filter { it.role == ROLE_SPECIAL && it.id in selectedIds }.sumOf { it.copies }

    /** Renvoie null s'il y a plus de roles que de joueurs (ou s'il manque les cartes de base). */
    fun deal(
        cards: List<Card>,
        nbPlayers: Int,
        nbWolves: Int,
        selectedIds: Set<Int>,
        random: Random = Random,
    ): WerewolfDeal? {
        val villager = cards.firstOrNull { it.role == ROLE_VILLAGER } ?: return null
        val wolf = cards.firstOrNull { it.role == ROLE_WOLF } ?: return null
        val specials = cards.filter { it.role == ROLE_SPECIAL && it.id in selectedIds }

        val deck = specials.flatMap { card -> List(card.copies) { card } }.toMutableList()
        if (deck.size + nbWolves > nbPlayers) return null
        repeat(nbWolves) { deck.add(wolf) }
        while (deck.size < nbPlayers) deck.add(villager)

        return WerewolfDeal(
            deck = deck.shuffled(random),
            nbWolvesTotal = nbWolves + specials.count { it.isWolf },
            hasThief = specials.any { it.name == THIEF },
            captain = random.nextInt(nbPlayers) + 1,
        )
    }

    /** Nom de l'image embarquee (assets/werewolf) a partir de l'URL renvoyee par l'API. */
    fun imageAsset(card: Card): String? = card.image?.substringAfterLast('/')?.let { "werewolf/$it" }
}
