package fr.gameandme.games.game

import fr.gameandme.games.data.Card
import kotlin.random.Random

/**
 * Time's up : 3 manches avec les memes cartes (decrire, un seul mot, mimer).
 * Une manche se termine quand toutes les cartes ont ete trouvees.
 * Manche 1 : on ne peut pas passer ; ensuite une carte passee retourne sous la pioche.
 */
class TimesUpGame(
    private val matchCards: List<Card>,
    val nbTeams: Int,
    private val random: Random = Random,
) {
    var phase = Phase.Intro
        private set
    var step = 1
        private set
    var team = 1
        private set
    var lastTeam = 1
        private set
    var current: Card? = null
        private set

    /** Points par manche puis par equipe : scores[manche - 1][equipe - 1]. */
    private val scores = List(STEPS) { IntArray(nbTeams) }
    private val deck = matchCards.shuffled(random).toMutableList()
    val turn = mutableListOf<TurnEntry>()

    val remaining: Int get() = deck.size
    val canPass: Boolean get() = step > 1

    fun score(step: Int, team: Int) = scores[step - 1][team - 1]
    fun total(team: Int) = scores.sumOf { it[team - 1] }
    fun winners() = winners((1..nbTeams).map { total(it) })

    /** Bouton "Equipe X, allez-y" (intro de manche ou fin de tour). */
    fun startTurn() {
        if (phase != Phase.Intro && phase != Phase.TurnEnd) return
        if (deck.isEmpty()) {
            endSet()
            return
        }
        phase = Phase.Playing
        turn.clear()
        deck.shuffle(random)
        current = deck.first()
    }

    fun answer(found: Boolean) {
        val card = current ?: return
        if (phase != Phase.Playing || (!found && !canPass)) return
        deck.remove(card)
        setTurnResult(card, found)
        if (found) {
            scores[step - 1][team - 1]++
        } else {
            deck.add(card)
        }
        if (deck.isEmpty()) endTurn() else current = deck.first()
    }

    /** Fin du temps : la carte affichee n'est pas jouee, elle reste dans la pioche. */
    fun timeUp() {
        if (phase == Phase.Playing) endTurn()
    }

    /** Correction apres le tour : la carte a finalement ete trouvee (ou non). */
    fun toggle(index: Int) {
        if (phase != Phase.TurnEnd) return
        val entry = turn.getOrNull(index) ?: return
        entry.found = !entry.found
        if (entry.found) {
            deck.remove(entry.card)
            scores[step - 1][lastTeam - 1]++
        } else {
            if (entry.card !in deck) deck.add(entry.card)
            scores[step - 1][lastTeam - 1]--
        }
    }

    /** Bouton "Manche suivante". */
    fun nextSet() {
        if (phase != Phase.SetEnd) return
        step++
        deck.clear()
        deck.addAll(matchCards.shuffled(random))
        phase = Phase.Intro
    }

    private fun setTurnResult(card: Card, found: Boolean) {
        val entry = turn.firstOrNull { it.card.id == card.id }
        if (entry != null) {
            entry.found = found
        } else {
            turn.add(TurnEntry(card, found, if (found) 1 else 0))
        }
    }

    private fun endTurn() {
        phase = Phase.TurnEnd
        current = null
        lastTeam = team
        team = if (team == nbTeams) 1 else team + 1
    }

    private fun endSet() {
        phase = if (step == STEPS) Phase.Finished else Phase.SetEnd
    }

    companion object {
        const val STEPS = 3
        const val CARDS_PER_TEAM = 20
        const val TIME_LIMIT = 30
    }
}
