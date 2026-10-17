package fr.gameandme.games.game

import fr.gameandme.games.data.Card
import kotlin.random.Random

/**
 * Identifiants des jeux renvoyes par l'API (derives de leur nom sur le site).
 * Les noms d'origine (marques deposees) ont ete remplaces par des noms propres a l'application.
 */
object GameSlug {
    const val TIMES_UP = "lestroismanches"
    const val BRAINSTORM = "checklist"
    const val TABOO = "motsinterdits"
    const val PICTIONARY = "croquisexpress"
    const val WEREWOLF = "lesloupsduvillage"
}

/** Noms affiches des jeux. */
object GameName {
    const val TIMES_UP = "Les Trois Manches"
    const val BRAINSTORM = "Checklist"
    const val TABOO = "Mots Interdits"
    const val PICTIONARY = "Croquis Express"
    const val WEREWOLF = "Les Loups du Village"
}

/** Etapes communes aux jeux par equipes. */
/** [Review] : Checklist uniquement, verification des reponses avant de compter les points. */
enum class Phase { Intro, Countdown, Playing, Review, TurnEnd, SetEnd, Finished }

/** Carte jouee pendant un tour ; [delta] = points comptes, corrigeables a la fin du tour. */
class TurnEntry(val card: Card, found: Boolean, delta: Int, val missDelta: Int = -1) {
    var found = found
        internal set
    var delta = delta
        internal set
}

/** Numeros (1..n) des equipes ayant le meilleur score. */
fun winners(scores: List<Int>): List<Int> {
    val max = scores.maxOrNull() ?: return emptyList()
    return scores.withIndex().filter { it.value == max }.map { it.index + 1 }
}

/** Ordre des cartes a piocher ; par defaut un simple melange. */
typealias CardOrder = (List<Card>) -> List<Card>

/**
 * Cartes melangees : d'abord celles jamais jouees, puis de la plus anciennement jouee a la plus recente.
 * [lastPlayed] : id de carte => date (ms) de sa derniere partie.
 */
fun leastPlayedFirst(cards: List<Card>, lastPlayed: Map<Int, Long>, random: Random = Random): List<Card> =
    // Tri stable : le melange est garde entre cartes jouees au meme moment
    cards.shuffled(random).sortedBy { lastPlayed[it.id] ?: 0L }

/**
 * Tire [count] cartes, en prenant d'abord celles qui ont ete jouees il y a le plus longtemps (ou jamais).
 * Renvoie null s'il n'y a pas assez de cartes.
 */
fun pickCards(all: List<Card>, count: Int, lastPlayed: Map<Int, Long>, random: Random = Random): List<Card>? {
    if (all.size < count) return null
    return leastPlayedFirst(all, lastPlayed, random).take(count)
}
