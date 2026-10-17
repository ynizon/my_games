package fr.gameandme.games.game

import fr.gameandme.games.data.Card
import kotlin.random.Random

/**
 * Jeux en manches ou chaque equipe joue un tour chronometre par manche
 * (Taboo, Pictionary) : +1 par carte trouvee, -1 par carte passee.
 */
abstract class RoundsGame(val nbTeams: Int, val nbSets: Int, protected val random: Random) {
    var phase = Phase.Intro
        private set
    var round = 1
        private set
    var team = 1
        private set
    var lastTeam = 1
        private set
    var current: Card? = null
        private set

    private var setsDone = 0
    private val totals = IntArray(nbTeams)
    val turn = mutableListOf<TurnEntry>()

    /** Le tour qui vient de se terminer etait le dernier de la manche. */
    val roundOver: Boolean get() = setsDone == round

    fun total(team: Int) = totals[team - 1]
    fun winners() = winners(totals.toList())

    protected abstract fun onTurnStart()
    protected abstract fun draw(): Card?
    protected open fun onAnswered(card: Card, found: Boolean) {}
    protected open fun onTimeUp(card: Card?) {}
    protected open fun onToggled(entry: TurnEntry) {}

    fun startTurn() {
        if (phase != Phase.Intro && phase != Phase.TurnEnd) return
        turn.clear()
        current = null
        if (roundOver) {
            endSet()
            return
        }
        phase = Phase.Playing
        onTurnStart()
        current = draw()
        if (current == null) endTurn()
    }

    fun answer(found: Boolean) {
        val card = current ?: return
        if (phase != Phase.Playing) return
        record(card, found, if (found) 1 else -1)
        onAnswered(card, found)
        current = draw()
        if (current == null) endTurn()
    }

    fun timeUp() {
        if (phase != Phase.Playing) return
        onTimeUp(current)
        endTurn()
    }

    /** Correction apres le tour : trouvee <-> ratee, le score suit le changement. */
    fun toggle(index: Int) {
        if (phase != Phase.TurnEnd) return
        val entry = turn.getOrNull(index) ?: return
        entry.found = !entry.found
        val newDelta = if (entry.found) 1 else entry.missDelta
        totals[lastTeam - 1] += newDelta - entry.delta
        entry.delta = newDelta
        onToggled(entry)
    }

    fun nextSet() {
        if (phase == Phase.SetEnd) phase = Phase.Intro
    }

    protected fun record(card: Card, found: Boolean, delta: Int, missDelta: Int = -1) {
        turn.add(TurnEntry(card, found, delta, missDelta))
        totals[team - 1] += delta
    }

    private fun endTurn() {
        phase = Phase.TurnEnd
        current = null
        lastTeam = team
        team++
        if (team > nbTeams) {
            team = 1
            setsDone++
        }
    }

    private fun endSet() {
        phase = if (round == nbSets) Phase.Finished else Phase.SetEnd
        round++
    }
}

/**
 * Taboo : une carte jouee (trouvee ou passee) ne revient plus dans le match.
 * [order] : ordre de la pioche a chaque tour.
 */
class TabooGame(
    cards: List<Card>,
    nbTeams: Int,
    nbSets: Int,
    random: Random = Random,
    private val order: CardOrder = { it.shuffled(random) },
) : RoundsGame(nbTeams, nbSets, random) {

    private val deck = cards.distinctBy { it.id }.toMutableList()
    private val turnDeck = mutableListOf<Card>()

    val remaining: Int get() = turnDeck.size + if (current != null) 1 else 0

    override fun onTurnStart() {
        turnDeck.clear()
        turnDeck.addAll(order(deck))
    }

    override fun draw(): Card? = turnDeck.removeFirstOrNull()

    override fun onAnswered(card: Card, found: Boolean) {
        deck.remove(card)
    }

    companion object {
        const val TIME_LIMIT = 30
    }
}

/**
 * Pictionary : les cartes ratees reviennent quand la pioche est vide.
 * A la fin du temps, la carte en cours compte comme ratee, sans penalite.
 * [order] : ordre de la pioche complete (au debut, et quand toutes les cartes ont ete trouvees).
 */
class PictionaryGame(
    private val cards: List<Card>,
    nbTeams: Int,
    nbSets: Int,
    random: Random = Random,
    private val order: CardOrder = { it.shuffled(random) },
) : RoundsGame(nbTeams, nbSets, random) {

    private val deck = order(cards).toMutableList()
    private val missed = mutableListOf<Card>()

    override fun onTurnStart() {}

    override fun draw(): Card? {
        if (deck.isEmpty()) {
            deck.addAll(if (missed.isNotEmpty()) missed.shuffled(random) else order(cards))
            missed.clear()
        }
        return deck.removeFirstOrNull()
    }

    override fun onAnswered(card: Card, found: Boolean) {
        if (!found) missed.add(card)
    }

    override fun onTimeUp(card: Card?) {
        if (card != null) {
            record(card, found = false, delta = 0, missDelta = 0)
            missed.add(card)
        }
    }

    override fun onToggled(entry: TurnEntry) {
        if (entry.found) {
            missed.remove(entry.card)
            deck.remove(entry.card)
        } else {
            missed.add(entry.card)
        }
    }

    companion object {
        const val TIME_LIMIT = 30
    }
}
