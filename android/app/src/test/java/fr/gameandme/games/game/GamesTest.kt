package fr.gameandme.games.game

import fr.gameandme.games.data.Card
import org.junit.Assert.assertEquals
import org.junit.Assert.assertFalse
import org.junit.Assert.assertNotNull
import org.junit.Assert.assertNull
import org.junit.Assert.assertTrue
import org.junit.Test
import kotlin.random.Random

class GamesTest {
    private fun cards(n: Int, gameId: Int = 1, words: List<String> = emptyList()) =
        (1..n).map { Card(id = it, gameId = gameId, name = "Carte $it", words = words) }

    @Test
    fun pickCards_prefersNeverPlayedThenOldestPlayed() {
        val all = cards(10)
        val lastPlayed = mapOf(1 to 400L, 2 to 300L, 3 to 200L, 4 to 100L)
        val picked = pickCards(all, 8, lastPlayed, random = Random(1))!!
        assertEquals(8, picked.size)
        assertTrue(picked.take(6).all { it.id !in lastPlayed })
        assertEquals(listOf(4, 3), picked.drop(6).map { it.id })
        assertNull(pickCards(all, 11, emptyMap()))
    }

    @Test
    fun taboo_drawsInGivenOrder() {
        val game = TabooGame(cards(5), nbTeams = 2, nbSets = 1, order = { list -> list.sortedByDescending { it.id } })
        game.startTurn()
        assertEquals(5, game.current?.id)
        game.answer(true)
        assertEquals(4, game.current?.id)
    }

    @Test
    fun winners_returnsAllTiedTeams() {
        assertEquals(listOf(1, 3), winners(listOf(4, 2, 4)))
    }

    @Test
    fun timesUp_cannotPassInFirstSet_andSetEndsWhenAllFound() {
        val game = TimesUpGame(cards(3), nbTeams = 2, random = Random(2))
        game.startTurn()
        assertEquals(Phase.Playing, game.phase)
        game.answer(found = false)
        assertEquals(3, game.remaining)

        repeat(3) { game.answer(found = true) }
        assertEquals(Phase.TurnEnd, game.phase)
        assertEquals(3, game.total(1))
        assertEquals(2, game.team)

        game.startTurn()
        assertEquals(Phase.SetEnd, game.phase)
        game.nextSet()
        assertEquals(2, game.step)
        assertEquals(3, game.remaining)
    }

    @Test
    fun timesUp_passedCardGoesBackAndCorrectionAdjustsScore() {
        val game = TimesUpGame(cards(3), nbTeams = 2, random = Random(3))
        game.startTurn()
        repeat(3) { game.answer(found = true) }
        game.startTurn()
        game.nextSet()

        game.startTurn()
        val passed = game.current!!
        game.answer(found = false)
        assertEquals(3, game.remaining)
        game.answer(found = true)
        game.timeUp()
        assertEquals(Phase.TurnEnd, game.phase)
        assertEquals(1, game.score(2, 2))
        assertEquals(2, game.remaining)

        // La carte passee etait finalement trouvee
        val index = game.turn.indexOfFirst { it.card.id == passed.id }
        game.toggle(index)
        assertEquals(2, game.score(2, 2))
        assertEquals(1, game.remaining)
    }

    @Test
    fun timesUp_finishesAfterThreeSets() {
        val game = TimesUpGame(cards(2), nbTeams = 2, random = Random(4))
        repeat(3) {
            game.startTurn()
            game.answer(true)
            game.answer(true)
            game.startTurn()
            game.nextSet()
        }
        assertEquals(Phase.Finished, game.phase)
        assertEquals(listOf(1), game.winners())
    }

    @Test
    fun taboo_scoresAndCorrections() {
        val game = TabooGame(cards(10), nbTeams = 2, nbSets = 1, random = Random(5))
        game.startTurn()
        game.answer(true)
        game.answer(true)
        game.answer(false)
        game.timeUp()
        assertEquals(1, game.total(1))
        game.toggle(2)
        assertEquals(3, game.total(1))

        game.startTurn()
        assertEquals(7, game.remaining)
        game.timeUp()
        assertTrue(game.roundOver)
        game.startTurn()
        assertEquals(Phase.Finished, game.phase)
        assertEquals(listOf(1), game.winners())
    }

    @Test
    fun pictionary_timeUpCountsCurrentCardAsMissedWithoutPenalty() {
        val game = PictionaryGame(cards(2), nbTeams = 2, nbSets = 2, random = Random(6))
        game.startTurn()
        game.answer(false)
        game.timeUp()
        assertEquals(-1, game.total(1))
        assertEquals(2, game.turn.size)
        assertEquals(0, game.turn[1].delta)

        // Correction : la derniere carte (sans penalite) etait trouvee
        game.toggle(1)
        assertEquals(0, game.total(1))
        game.toggle(1)
        assertEquals(-1, game.total(1))

        game.startTurn()
        assertNotNull(game.current)
        game.timeUp()
        game.startTurn()
        assertEquals(Phase.SetEnd, game.phase)
        game.nextSet()
        assertEquals(2, game.round)
    }

    @Test
    fun brainstorm_countsCheckedWordsAndRenewsCard() {
        val all = cards(6, gameId = 2, words = listOf("a", "b", "c"))
        val game = BrainstormGame(all, all.take(4), nbTeams = 2, nbRounds = 2, random = Random(7))
        game.startTurn()
        assertEquals(Phase.Countdown, game.phase)
        game.toggleWord(0)
        assertTrue(game.checked.isEmpty())
        game.countdownDone()
        game.toggleWord(0)
        game.toggleWord(2)
        val renewed = game.renewCard()!!
        assertTrue(renewed.id > 4)
        assertTrue(game.checked.isEmpty())
        game.toggleWord(1)
        // Fin du tour : verification, on peut encore cocher les reponses oubliees
        game.review()
        assertEquals(Phase.Review, game.phase)
        assertEquals(0, game.total(1))
        game.toggleWord(2)
        assertTrue(game.renewCard() == null)
        game.validate()
        assertEquals(2, game.total(1))
        assertEquals(Phase.TurnEnd, game.phase)
        game.toggleWord(0)
        assertEquals(setOf(1, 2), game.checked)

        game.startTurn()
        game.countdownDone()
        game.validate()
        assertTrue(game.roundOver)
        game.startTurn()
        assertEquals(Phase.SetEnd, game.phase)
        game.nextSet()
        repeat(2) {
            game.startTurn()
            game.countdownDone()
            game.validate()
        }
        game.startTurn()
        assertEquals(Phase.Finished, game.phase)
    }

    @Test
    fun werewolf_dealsOneCardPerPlayer() {
        val roles = listOf(
            Card(1, 4, "Villageois", role = "villager"),
            Card(2, 4, "Loup Garou", role = "wolf", isWolf = true),
            Card(3, 4, "Voleur", role = "special"),
            Card(4, 4, "Loup Garou Blanc", role = "special", isWolf = true),
            Card(5, 4, "Frères et Soeurs", role = "special", copies = 5),
        )
        val deal = Werewolf.deal(roles, nbPlayers = 8, nbWolves = 2, selectedIds = setOf(3, 4), random = Random(8))!!
        assertEquals(8, deal.deck.size)
        assertEquals(3, deal.nbWolvesTotal)
        assertTrue(deal.hasThief)
        assertEquals(4, deal.deck.count { it.role == "villager" })
        assertTrue(deal.captain in 1..8)

        assertEquals(7, Werewolf.specialSeats(roles, setOf(3, 4, 5)))
        assertNull(Werewolf.deal(roles, nbPlayers = 8, nbWolves = 2, selectedIds = setOf(3, 4, 5)))
        assertFalse(Werewolf.deal(roles, 8, 2, emptySet())!!.hasThief)
    }
}
