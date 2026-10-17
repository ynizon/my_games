package fr.gameandme.games.ui.games

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableIntStateOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.SavedStateHandle
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import fr.gameandme.games.GamesApplication
import fr.gameandme.games.data.Card
import fr.gameandme.games.data.Catalog
import fr.gameandme.games.game.BrainstormGame
import fr.gameandme.games.game.GameSlug
import fr.gameandme.games.game.Phase
import fr.gameandme.games.game.PictionaryGame
import fr.gameandme.games.game.RoundsGame
import fr.gameandme.games.game.TabooGame
import fr.gameandme.games.game.TimesUpGame
import fr.gameandme.games.game.TurnEntry
import fr.gameandme.games.game.Werewolf
import fr.gameandme.games.game.WerewolfDeal
import fr.gameandme.games.game.leastPlayedFirst
import fr.gameandme.games.game.pickCards
import fr.gameandme.games.ui.LAST_SECONDS
import fr.gameandme.games.ui.Sound
import fr.gameandme.games.ui.Sounds
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

/** Base des parties : chargement du catalogue local et chrono avec pause. */
abstract class GameViewModel(protected val app: GamesApplication) : ViewModel() {
    var error by mutableStateOf<String?>(null)
        protected set
    var timeLeft by mutableIntStateOf(0)
        private set
    var paused by mutableStateOf(false)
        private set

    private var timer: Job? = null

    /** A appeler a la fin de l'init des sous-classes (leurs parametres sont alors lus). */
    protected fun load() {
        viewModelScope.launch {
            app.catalog.loadLocal()
            val catalog = app.catalog.catalog.value
            if (catalog == null) {
                error = "Les cartes ne sont pas chargées. Connectez-vous à internet puis revenez à l'accueil."
            } else {
                setup(catalog)
            }
        }
    }

    protected abstract fun setup(catalog: Catalog)

    fun restart() {
        stopTimer()
        app.catalog.catalog.value?.let { setup(it) }
    }

    /** Cartes jamais jouees d'abord, puis les plus anciennement jouees (historique lu a chaque appel). */
    protected fun byLeastPlayed(cards: List<Card>): List<Card> = leastPlayedFirst(cards, app.catalog.lastPlayed())

    protected fun cardsOf(catalog: Catalog, slug: String): List<Card> {
        val id = catalog.games.firstOrNull { it.slug == slug }?.id ?: return emptyList()
        return catalog.cardsFor(id)
    }

    protected fun startTimer(seconds: Int, onEnd: () -> Unit) {
        timer?.cancel()
        timeLeft = seconds
        paused = false
        timer = viewModelScope.launch {
            while (timeLeft > 0) {
                delay(1000)
                if (!paused) {
                    timeLeft--
                    if (timeLeft in 1..LAST_SECONDS) Sounds.play(Sound.Tick)
                }
            }
            timer = null
            onEnd()
        }
    }

    protected fun stopTimer() {
        timer?.cancel()
        timer = null
        paused = false
    }

    fun togglePause() {
        if (timer == null) return
        paused = !paused
        Sounds.play(Sound.Pause)
    }
}

// ---------------------------------------------------------------- Time's up

data class TimesUpUi(
    val phase: Phase,
    val step: Int,
    val team: Int,
    val lastTeam: Int,
    val nbTeams: Int,
    val current: String?,
    val remaining: Int,
    val canPass: Boolean,
    /** scores[manche - 1][equipe - 1] */
    val scores: List<List<Int>>,
    val totals: List<Int>,
    val turn: List<Pair<String, Boolean>>,
    val winners: List<Int>,
)

class TimesUpViewModel(app: GamesApplication, state: SavedStateHandle) : GameViewModel(app) {
    private val nbTeams: Int = state["teams"] ?: 2
    private var game: TimesUpGame? = null
    var ui by mutableStateOf<TimesUpUi?>(null)
        private set

    init {
        load()
    }

    override fun setup(catalog: Catalog) {
        val cards = pickCards(cardsOf(catalog, GameSlug.TIMES_UP), nbTeams * TimesUpGame.CARDS_PER_TEAM, app.catalog.lastPlayed())
        if (cards == null) {
            error = "Il n'y a pas assez de cartes pour ce nombre d'équipes."
            return
        }
        app.catalog.markPlayed(cards.map { it.id })
        game = TimesUpGame(cards, nbTeams)
        refresh()
    }

    fun startTurn() = act {
        startTurn()
        if (phase == Phase.Playing) {
            startTimer(TimesUpGame.TIME_LIMIT) { act { timeUp(); Sounds.play(Sound.Beep) } }
        }
    }

    fun answer(found: Boolean) {
        if (paused) return
        act {
            if (!found && !canPass) return@act
            answer(found)
            Sounds.play(if (found) Sound.Ok else Sound.Error)
            if (phase == Phase.TurnEnd) {
                stopTimer()
                Sounds.play(Sound.Beep)
            }
        }
    }

    fun toggle(index: Int) = act { toggle(index); Sounds.play(Sound.Pass) }
    fun nextSet() = act { nextSet() }

    private fun act(block: TimesUpGame.() -> Unit) {
        val game = game ?: return
        val before = game.phase
        game.block()
        if (game.phase == Phase.Finished && before != Phase.Finished) Sounds.play(Sound.Fanfare)
        refresh()
    }

    private fun refresh() {
        val game = game ?: return
        ui = TimesUpUi(
            phase = game.phase,
            step = game.step,
            team = game.team,
            lastTeam = game.lastTeam,
            nbTeams = nbTeams,
            current = game.current?.name,
            remaining = game.remaining,
            canPass = game.canPass,
            scores = (1..TimesUpGame.STEPS).map { s -> (1..nbTeams).map { game.score(s, it) } },
            totals = (1..nbTeams).map { game.total(it) },
            turn = game.turn.map { it.card.name to it.found },
            winners = game.winners(),
        )
    }
}

// ---------------------------------------------------------------- Brainstorm

data class BrainstormUi(
    val phase: Phase,
    val round: Int,
    val nbRounds: Int,
    val team: Int,
    val nbTeams: Int,
    val theme: String?,
    val words: List<String>,
    val checked: Set<Int>,
    val remaining: Int,
    val roundOver: Boolean,
    val lastTheme: String?,
    val lastScore: Int,
    val lastWords: Int,
    val totals: List<Int>,
    val winners: List<Int>,
)

class BrainstormViewModel(app: GamesApplication, state: SavedStateHandle) : GameViewModel(app) {
    private val nbTeams: Int = state["teams"] ?: 2
    private val nbRounds: Int = state["rounds"] ?: 2
    private var game: BrainstormGame? = null
    var ui by mutableStateOf<BrainstormUi?>(null)
        private set

    init {
        load()
    }

    override fun setup(catalog: Catalog) {
        val playable = cardsOf(catalog, GameSlug.BRAINSTORM).filter { it.words.isNotEmpty() }
        val cards = pickCards(playable, nbTeams * nbRounds, app.catalog.lastPlayed())
        if (cards == null) {
            error = "Il n'y a pas assez de cartes pour ce nombre d'équipes et de manches."
            return
        }
        app.catalog.markPlayed(cards.map { it.id })
        game = BrainstormGame(playable, cards, nbTeams, nbRounds, order = ::byLeastPlayed)
        refresh()
    }

    fun startTurn() = act {
        startTurn()
        if (phase == Phase.Countdown) {
            // Decompte avant d'afficher les mots, puis le tour chronometre
            startTimer(BrainstormGame.COUNTDOWN) {
                act {
                    countdownDone()
                    Sounds.play(Sound.Go)
                    startTimer(BrainstormGame.TIME_LIMIT) { this@BrainstormViewModel.validate(timeUp = true) }
                }
            }
        }
    }

    fun toggleWord(index: Int) {
        if (!paused) act {
            toggleWord(index)
            if (index in checked) Sounds.play(Sound.Sparkle)
        }
    }

    fun renewCard() {
        if (paused) return
        act { renewCard()?.let { app.catalog.markPlayed(listOf(it.id)) } }
    }

    /** Bouton OK ou fin du temps : verification des reponses avant de compter les points. */
    fun validate(timeUp: Boolean = false) {
        if (paused && !timeUp) return
        act {
            if (phase != Phase.Playing) return@act
            stopTimer()
            review()
            Sounds.play(if (timeUp) Sound.Finish else Sound.Beep)
        }
    }

    /** Bouton "Valider" de la verification. */
    fun confirmReview() = act {
        if (phase != Phase.Review) return@act
        validate()
        Sounds.play(Sound.Ok)
    }

    fun nextSet() = act { nextSet() }

    private fun act(block: BrainstormGame.() -> Unit) {
        val game = game ?: return
        val before = game.phase
        game.block()
        if (game.phase == Phase.Finished && before != Phase.Finished) Sounds.play(Sound.Fanfare)
        refresh()
    }

    private fun refresh() {
        val game = game ?: return
        ui = BrainstormUi(
            phase = game.phase,
            round = game.round,
            nbRounds = nbRounds,
            team = game.team,
            nbTeams = nbTeams,
            theme = game.current?.name,
            words = game.current?.words.orEmpty(),
            checked = game.checked.toSet(),
            remaining = game.remaining,
            roundOver = game.roundOver,
            lastTheme = game.lastCard?.name,
            lastScore = game.lastScore,
            lastWords = game.lastCard?.words?.size ?: 0,
            totals = (1..nbTeams).map { game.total(it) },
            winners = game.winners(),
        )
    }
}

// ---------------------------------------------------------------- Taboo / Pictionary

data class RoundsUi(
    val phase: Phase,
    val round: Int,
    val nbSets: Int,
    val team: Int,
    val nbTeams: Int,
    val current: String?,
    val taboo: List<String>,
    val remaining: Int?,
    val roundOver: Boolean,
    val totals: List<Int>,
    val turn: List<Pair<String, Boolean>>,
    val winners: List<Int>,
)

class RoundsViewModel(app: GamesApplication, state: SavedStateHandle) : GameViewModel(app) {
    val slug: String = state["slug"] ?: GameSlug.TABOO
    private val nbTeams: Int = state["teams"] ?: 2
    private val nbSets: Int = state["sets"] ?: 1
    private var game: RoundsGame? = null
    var ui by mutableStateOf<RoundsUi?>(null)
        private set

    val timeLimit = if (slug == GameSlug.PICTIONARY) PictionaryGame.TIME_LIMIT else TabooGame.TIME_LIMIT

    init {
        load()
    }

    override fun setup(catalog: Catalog) {
        val cards = cardsOf(catalog, slug)
        if (cards.isEmpty()) {
            error = "Aucune carte pour ce jeu."
            return
        }
        game = if (slug == GameSlug.PICTIONARY) {
            PictionaryGame(cards, nbTeams, nbSets, order = ::byLeastPlayed)
        } else {
            TabooGame(cards, nbTeams, nbSets, order = ::byLeastPlayed)
        }
        refresh()
    }

    fun startTurn() = act {
        startTurn()
        if (phase == Phase.Playing) {
            startTimer(timeLimit) { act { timeUp(); Sounds.play(Sound.Beep) } }
        } else if (phase == Phase.TurnEnd) {
            Sounds.play(Sound.Beep)
        }
    }

    fun answer(found: Boolean) {
        if (paused) return
        act {
            answer(found)
            Sounds.play(if (found) Sound.Ok else Sound.Error)
            if (phase == Phase.TurnEnd) {
                stopTimer()
                Sounds.play(Sound.Beep)
            }
        }
    }

    fun toggle(index: Int) = act { toggle(index); Sounds.play(Sound.Pass) }
    fun nextSet() = act { nextSet() }

    private fun act(block: RoundsGame.() -> Unit) {
        val game = game ?: return
        val before = game.phase
        val shown = game.current
        game.block()
        // Chaque carte affichee est memorisee comme jouee
        game.current?.takeIf { it !== shown }?.let { app.catalog.markPlayed(listOf(it.id)) }
        if (game.phase == Phase.Finished && before != Phase.Finished) Sounds.play(Sound.Fanfare)
        refresh()
    }

    private fun refresh() {
        val game = game ?: return
        ui = RoundsUi(
            phase = game.phase,
            round = game.round,
            nbSets = nbSets,
            team = game.team,
            nbTeams = nbTeams,
            current = game.current?.name,
            taboo = if (slug == GameSlug.TABOO) game.current?.words.orEmpty() else emptyList(),
            remaining = (game as? TabooGame)?.remaining,
            roundOver = game.roundOver,
            totals = (1..nbTeams).map { game.total(it) },
            turn = game.turn.map(TurnEntry::toPair),
            winners = game.winners(),
        )
    }
}

private fun TurnEntry.toPair() = card.name to found

// ---------------------------------------------------------------- Loup-Garou

sealed interface WerewolfStage {
    data object Intro : WerewolfStage
    /** Le telephone passe au joueur [player] (1..n) ; [revealed] : role affiche. */
    data class Player(val player: Int, val revealed: Boolean) : WerewolfStage
    data object Night : WerewolfStage
}

class WerewolfViewModel(app: GamesApplication, state: SavedStateHandle) : GameViewModel(app) {
    private val nbPlayers: Int = state["players"] ?: 8
    private val nbWolves: Int = state["wolves"] ?: 2
    private val selectedIds: Set<Int> =
        state.get<String>("cards").orEmpty().split(',').mapNotNull { it.toIntOrNull() }.toSet()

    var deal by mutableStateOf<WerewolfDeal?>(null)
        private set
    var stage by mutableStateOf<WerewolfStage>(WerewolfStage.Intro)
        private set

    init {
        load()
    }

    override fun setup(catalog: Catalog) {
        deal = Werewolf.deal(catalog.werewolfCards, nbPlayers, nbWolves, selectedIds)
        if (deal == null) error = "Trop de cartes spéciales par rapport au nombre de joueurs."
        stage = WerewolfStage.Intro
    }

    fun next() {
        val deal = deal ?: return
        stage = when (val current = stage) {
            WerewolfStage.Intro -> WerewolfStage.Player(1, revealed = false)
            is WerewolfStage.Player -> when {
                !current.revealed -> current.copy(revealed = true).also { Sounds.play(Sound.Flip) }
                current.player < deal.deck.size ->
                    WerewolfStage.Player(current.player + 1, revealed = false).also { Sounds.play(Sound.Ok) }
                else -> WerewolfStage.Night.also { Sounds.play(Sound.Howl) }
            }
            WerewolfStage.Night -> WerewolfStage.Night
        }
    }
}
