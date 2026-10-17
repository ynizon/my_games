package fr.gameandme.games.game

import fr.gameandme.games.data.Card
import kotlin.random.Random

/**
 * Brainstorm : a chaque manche, chaque equipe tire un theme et doit trouver
 * le plus de references de la liste. Un point par reference cochee.
 */
class BrainstormGame(
    /** Cartes jouables (avec au moins un mot). */
    private val allCards: List<Card>,
    /** Cartes tirees pour le match (une par equipe et par manche). */
    matchCards: List<Card>,
    val nbTeams: Int,
    val nbRounds: Int,
    private val random: Random = Random,
    /** Ordre de preference des themes proposes par "Changer de theme". */
    private val order: CardOrder = { it.shuffled(random) },
) {
    var phase = Phase.Intro
        private set
    var round = 1
        private set
    var team = 1
        private set
    var current: Card? = null
        private set

    /** Index des mots trouves pendant le tour. */
    val checked = mutableSetOf<Int>()
    var lastScore = 0
        private set
    var lastCard: Card? = null
        private set

    private val scores = IntArray(nbTeams)
    private val deck = matchCards.shuffled(random).toMutableList()
    private val usedIds = matchCards.map { it.id }.toMutableSet()
    private var turnsInRound = 0

    val remaining: Int get() = deck.size + if (current != null) 1 else 0
    val roundOver: Boolean get() = turnsInRound == nbTeams

    fun total(team: Int) = scores[team - 1]
    fun winners() = winners(scores.toList())

    /** Bouton "Equipe X, allez-y" : decompte avant d'afficher les mots. */
    fun startTurn() {
        if (phase != Phase.Intro && phase != Phase.TurnEnd) return
        if (roundOver || deck.isEmpty()) {
            endSet()
            return
        }
        current = deck.removeAt(0)
        checked.clear()
        phase = Phase.Countdown
    }

    fun countdownDone() {
        if (phase == Phase.Countdown) phase = Phase.Playing
    }

    /** Pendant le tour, ou a la verification pour ajouter les reponses qu'on n'a pas eu le temps de cocher. */
    fun toggleWord(index: Int) {
        if (phase != Phase.Playing && phase != Phase.Review) return
        if (!checked.remove(index)) checked.add(index)
    }

    /** Change de theme (le chrono continue). Renvoie la nouvelle carte. */
    fun renewCard(): Card? {
        if (phase != Phase.Playing) return null
        val card = order(allCards.filter { it.id !in usedIds }).firstOrNull() ?: return null
        usedIds.add(card.id)
        current = card
        checked.clear()
        return card
    }

    /** Bouton OK ou fin du temps : verification des reponses (les mots coches restent coches). */
    fun review() {
        if (phase == Phase.Playing) phase = Phase.Review
    }

    /** Fin de la verification : les mots coches sont comptes. */
    fun validate() {
        val card = current ?: return
        if (phase != Phase.Review && phase != Phase.Playing) return
        lastScore = checked.size
        lastCard = card
        scores[team - 1] += lastScore
        current = null
        turnsInRound++
        team = if (team == nbTeams) 1 else team + 1
        phase = Phase.TurnEnd
    }

    fun nextSet() {
        if (phase == Phase.SetEnd) phase = Phase.Intro
    }

    private fun endSet() {
        turnsInRound = 0
        phase = if (round == nbRounds || deck.isEmpty()) Phase.Finished else Phase.SetEnd
        round++
    }

    companion object {
        const val TIME_LIMIT = 60
        const val COUNTDOWN = 5
    }
}
