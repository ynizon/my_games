package fr.gameandme.games.ui.games

import android.graphics.BitmapFactory
import androidx.compose.animation.AnimatedContent
import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.core.FastOutSlowInEasing
import androidx.compose.animation.core.RepeatMode
import androidx.compose.animation.core.animateFloat
import androidx.compose.animation.core.infiniteRepeatable
import androidx.compose.animation.core.rememberInfiniteTransition
import androidx.compose.animation.core.tween
import androidx.compose.animation.animateColorAsState
import androidx.compose.animation.core.Spring
import androidx.compose.animation.core.animateFloatAsState
import androidx.compose.animation.core.spring
import androidx.compose.animation.scaleIn
import androidx.compose.animation.slideInHorizontally
import androidx.compose.animation.slideOutHorizontally
import androidx.compose.animation.fadeIn
import androidx.compose.animation.fadeOut
import androidx.compose.animation.togetherWith
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.heightIn
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.OpenInNew
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.Checkbox
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.key
import androidx.compose.runtime.remember
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.scale
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.platform.LocalDensity
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalUriHandler
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import fr.gameandme.games.data.Card
import fr.gameandme.games.game.BrainstormGame
import fr.gameandme.games.game.GameName
import fr.gameandme.games.game.GameSlug
import fr.gameandme.games.game.Phase
import fr.gameandme.games.game.Werewolf
import fr.gameandme.games.ui.Ambiance
import fr.gameandme.games.ui.AnswerBar
import fr.gameandme.games.ui.BigButton
import fr.gameandme.games.ui.EndButtons
import fr.gameandme.games.ui.GameScaffold
import fr.gameandme.games.ui.Panel
import fr.gameandme.games.ui.Party
import fr.gameandme.games.ui.ScoreTable
import fr.gameandme.games.ui.Success
import fr.gameandme.games.ui.TeamBadge
import fr.gameandme.games.ui.TimerRing
import fr.gameandme.games.ui.Title
import fr.gameandme.games.ui.TurnEntryUi
import fr.gameandme.games.ui.TurnSummary
import fr.gameandme.games.ui.WinnerText
import fr.gameandme.games.ui.teamGo
import java.net.URLEncoder

@Composable
private fun ErrorPanel(message: String, onHome: () -> Unit) {
    Panel {
        Text(message, textAlign = TextAlign.Center)
        BigButton("Retour", onHome)
    }
}

/** Nom de la carte en grand, avec un fondu a chaque changement. */
@Composable
private fun CardName(name: String?) {
    AnimatedContent(
        targetState = name.orEmpty(),
        transitionSpec = {
            (slideInHorizontally { it } + scaleIn(initialScale = 0.6f, animationSpec = spring(dampingRatio = Spring.DampingRatioMediumBouncy)) + fadeIn())
                .togetherWith(slideOutHorizontally { -it } + fadeOut())
        },
        label = "card",
    ) {
        Text(
            it,
            modifier = Modifier.fillMaxWidth().heightIn(min = 80.dp).padding(vertical = 8.dp),
            fontSize = 32.sp,
            lineHeight = 38.sp,
            fontWeight = FontWeight.Bold,
            textAlign = TextAlign.Center,
        )
    }
}

/** Feu d'artifice en fin de partie, confettis en fin de tour reussi. */
private fun party(phase: Phase?, turnSucceeded: Boolean) = when {
    phase == Phase.Finished -> Party.Fireworks
    phase == Phase.TurnEnd && turnSucceeded -> Party.Confetti
    else -> Party.None
}

/** Mots du theme a cocher (pendant le tour et a la verification). */
@Composable
private fun WordChecklist(words: List<String>, checkedWords: Set<Int>, onToggle: (Int) -> Unit) {
    Column(modifier = Modifier.fillMaxWidth()) {
        words.forEachIndexed { index, word ->
            val checked = index in checkedWords
            val background by animateColorAsState(
                if (checked) Success.copy(alpha = 0.2f) else Color.Transparent, label = "word",
            )
            val bump by animateFloatAsState(
                if (checked) 1.04f else 1f, spring(dampingRatio = Spring.DampingRatioHighBouncy), label = "bump",
            )
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .scale(bump)
                    .background(background, RoundedCornerShape(8.dp))
                    .clickable { onToggle(index) }
                    .padding(vertical = 2.dp),
                verticalAlignment = Alignment.CenterVertically,
            ) {
                Checkbox(checked = checked, onCheckedChange = { onToggle(index) })
                Text(word, style = MaterialTheme.typography.bodyLarge, modifier = Modifier.weight(1f))
                if (checked) Text("✨", modifier = Modifier.padding(end = 8.dp))
            }
        }
    }
}

@Composable
private fun Rules(title: String, text: String) {
    Title(title)
    Text(text, textAlign = TextAlign.Center, style = MaterialTheme.typography.bodyLarge)
}

// ---------------------------------------------------------------- Time's up

private val TIMESUP_RULES = listOf(
    "Décrivez !" to "Vous devez faire deviner les mots. Vous ne pouvez pas prononcer des mots à sonorité proche " +
        "ou qui ont la « même racine ». Vous ne pouvez pas traduire les mots ni les épeler. Vous ne pouvez pas passer.",
    "Un seul mot !" to "Vous ne devez prononcer qu'un seul mot, et votre équipe ne peut faire qu'une seule proposition. " +
        "Cependant, vous pouvez passer si la carte ne vous plaît pas.",
    "Mimez !" to "Vous devez mimer les mots. Vous pouvez aussi fredonner ou faire des onomatopées (voire des bruitages), " +
        "mais vous ne pouvez pas parler.",
)

@Composable
fun TimesUpScreen(vm: TimesUpViewModel, onHome: () -> Unit) {
    val uriHandler = LocalUriHandler.current
    GameScaffold(GameName.TIMES_UP, onQuit = onHome, ambiance = Ambiance.TimesUp, party = party(vm.ui?.phase, vm.ui?.turn?.any { it.second } == true)) {
        vm.error?.let { ErrorPanel(it, onHome); return@GameScaffold }
        val ui = vm.ui ?: return@GameScaffold
        val scoreRows = {
            ui.scores.mapIndexed { index, row -> "Manche ${index + 1}" to row } + ("Total" to ui.totals)
        }
        when (ui.phase) {
            Phase.Intro -> Panel {
                val (title, text) = TIMESUP_RULES[ui.step - 1]
                Text("Manche ${ui.step}/3", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold)
                Rules(title, text)
                BigButton(teamGo(ui.team), vm::startTurn)
            }
            Phase.Playing, Phase.Countdown, Phase.Review -> Panel {
                TeamBadge(ui.team)
                TimerRing(vm.timeLeft, fr.gameandme.games.game.TimesUpGame.TIME_LIMIT)
                Text("Cartes restantes : ${ui.remaining}")
                CardName(ui.current)
                AnswerBar(
                    paused = vm.paused,
                    onMiss = if (ui.canPass) ({ vm.answer(false) }) else null,
                    onPause = vm::togglePause,
                    onFound = { vm.answer(true) },
                    missLabel = "PASSER",
                )
            }
            Phase.TurnEnd -> Panel {
                Title("Manche ${ui.step}")
                val found = ui.turn.count { it.second }
                Text("Équipe ${ui.lastTeam} : $found/${ui.turn.size}", style = MaterialTheme.typography.titleMedium)
                BigButton(if (ui.remaining == 0) "Fin de manche" else teamGo(ui.team), vm::startTurn)
                TurnSummary(
                    ui.turn.map { (name, ok) ->
                        TurnEntryUi(name, ok) {
                            IconButton(onClick = { uriHandler.openUri(wikipedia(name)) }) {
                                Icon(Icons.AutoMirrored.Filled.OpenInNew, contentDescription = "Wikipédia")
                            }
                        }
                    },
                    onToggle = vm::toggle,
                )
            }
            Phase.SetEnd -> Panel {
                Title("Fin de la manche ${ui.step}")
                ScoreTable(ui.nbTeams, scoreRows())
                BigButton("Manche suivante", vm::nextSet)
            }
            Phase.Finished -> Panel {
                Title("Fin de la partie")
                WinnerText(ui.winners)
                ScoreTable(ui.nbTeams, scoreRows())
                EndButtons(onReplay = vm::restart, onHome = onHome)
            }
        }
    }
}

private fun wikipedia(name: String) =
    "https://fr.wikipedia.org/wiki/" + URLEncoder.encode(name.replace(' ', '_'), "UTF-8").replace("+", "_")

// ---------------------------------------------------------------- Brainstorm

@Composable
fun BrainstormScreen(vm: BrainstormViewModel, onHome: () -> Unit) {
    GameScaffold(GameName.BRAINSTORM, onQuit = onHome, ambiance = Ambiance.Brainstorm, party = party(vm.ui?.phase, (vm.ui?.lastScore ?: 0) > 0)) {
        vm.error?.let { ErrorPanel(it, onHome); return@GameScaffold }
        val ui = vm.ui ?: return@GameScaffold
        val totals = listOf("Total" to ui.totals)
        when (ui.phase) {
            Phase.Intro -> Panel {
                Text("Manche ${ui.round}/${ui.nbRounds}", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold)
                Rules(
                    "Cherchez !",
                    "Vous devez trouver le plus de références dans le temps imparti. " +
                        "Le lecteur coche les références trouvées par son équipe.",
                )
                BigButton(teamGo(ui.team), vm::startTurn)
            }
            Phase.Countdown -> Panel {
                TeamBadge(ui.team)
                CardName(ui.theme)
                Text("Préparez-vous…", style = MaterialTheme.typography.titleMedium)
                Text("${vm.timeLeft}", fontSize = 72.sp, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary)
            }
            Phase.Playing -> Panel {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    TeamBadge(ui.team)
                    Spacer(Modifier.width(12.dp))
                    TextButton(onClick = vm::renewCard) {
                        Icon(Icons.Default.Refresh, contentDescription = null)
                        Spacer(Modifier.width(4.dp))
                        Text("Changer de thème")
                    }
                }
                TimerRing(vm.timeLeft, BrainstormGame.TIME_LIMIT)
                CardName(ui.theme)
                Text("${ui.checked.size} trouvé(s)", fontWeight = FontWeight.Bold)
                WordChecklist(ui.words, ui.checked, vm::toggleWord)
                AnswerBar(paused = vm.paused, onMiss = null, onPause = vm::togglePause, onFound = { vm.validate() })
            }
            Phase.Review -> Panel {
                TeamBadge(ui.team)
                Title("Vérification des réponses")
                Text(
                    "Cochez les réponses que votre équipe a trouvées sans que vous ayez eu le temps de les cocher.",
                    textAlign = TextAlign.Center,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
                Text(ui.theme.orEmpty(), fontSize = 22.sp, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary,
                    textAlign = TextAlign.Center)
                WordChecklist(ui.words, ui.checked, vm::toggleWord)
                BigButton("Valider : ${ui.checked.size} point(s)", vm::confirmReview)
            }
            Phase.TurnEnd -> Panel {
                Title(ui.lastTheme.orEmpty())
                Text("${ui.lastScore}/${ui.lastWords} référence(s) trouvée(s)", style = MaterialTheme.typography.titleMedium)
                ScoreTable(ui.nbTeams, totals)
                BigButton(if (ui.roundOver) "Fin de manche" else teamGo(ui.team), vm::startTurn)
            }
            Phase.SetEnd -> Panel {
                Title("Fin de la manche ${ui.round - 1}")
                ScoreTable(ui.nbTeams, totals)
                BigButton("Manche suivante", vm::nextSet)
            }
            Phase.Finished -> Panel {
                Title("Fin de la partie")
                WinnerText(ui.winners)
                ScoreTable(ui.nbTeams, totals)
                EndButtons(onReplay = vm::restart, onHome = onHome)
            }
        }
    }
}

// ---------------------------------------------------------------- Taboo / Pictionary

@Composable
fun RoundsScreen(vm: RoundsViewModel, onHome: () -> Unit) {
    val taboo = vm.slug == GameSlug.TABOO
    GameScaffold(
        if (taboo) GameName.TABOO else GameName.PICTIONARY,
        onQuit = onHome,
        ambiance = if (taboo) Ambiance.Taboo else Ambiance.Pictionary,
        party = party(vm.ui?.phase, vm.ui?.turn?.any { it.second } == true),
    ) {
        vm.error?.let { ErrorPanel(it, onHome); return@GameScaffold }
        val ui = vm.ui ?: return@GameScaffold
        val totals = listOf("Total" to ui.totals)
        when (ui.phase) {
            Phase.Intro -> Panel {
                Text("Manche ${ui.round}/${ui.nbSets}", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold)
                if (taboo) {
                    Rules("Décrivez !", "Vous devez faire trouver le plus de mots dans le temps imparti sans prononcer les mots de la liste en dessous.")
                } else {
                    Rules("Dessinez !", "Vous devez dessiner le plus de mots dans le temps imparti.")
                }
                BigButton(teamGo(ui.team), vm::startTurn)
            }
            Phase.Playing, Phase.Countdown, Phase.Review -> Panel {
                TeamBadge(ui.team)
                TimerRing(vm.timeLeft, vm.timeLimit)
                ui.remaining?.let { Text("Cartes restantes : $it") }
                CardName(ui.current)
                if (ui.taboo.isNotEmpty()) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .background(MaterialTheme.colorScheme.errorContainer, RoundedCornerShape(12.dp))
                            .padding(12.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                    ) {
                        ui.taboo.forEach {
                            Text(it, fontSize = 20.sp, color = MaterialTheme.colorScheme.onErrorContainer, fontWeight = FontWeight.Medium)
                        }
                    }
                }
                AnswerBar(
                    paused = vm.paused,
                    onMiss = { vm.answer(false) },
                    onPause = vm::togglePause,
                    onFound = { vm.answer(true) },
                )
            }
            Phase.TurnEnd -> Panel {
                val found = ui.turn.count { it.second }
                Title("Fin du tour")
                Text("$found/${ui.turn.size} carte(s) trouvée(s)", style = MaterialTheme.typography.titleMedium)
                ScoreTable(ui.nbTeams, totals)
                BigButton(if (ui.roundOver) "Fin de manche" else teamGo(ui.team), vm::startTurn)
                TurnSummary(ui.turn.map { (name, ok) -> TurnEntryUi(name, ok) }, onToggle = vm::toggle)
            }
            Phase.SetEnd -> Panel {
                Title("Fin de la manche ${ui.round - 1}")
                ScoreTable(ui.nbTeams, totals)
                BigButton("Manche suivante", vm::nextSet)
            }
            Phase.Finished -> Panel {
                Title("Fin de la partie")
                WinnerText(ui.winners)
                ScoreTable(ui.nbTeams, totals)
                EndButtons(onReplay = vm::restart, onHome = onHome)
            }
        }
    }
}

// ---------------------------------------------------------------- Loup-Garou

@Composable
fun WerewolfScreen(vm: WerewolfViewModel, onHome: () -> Unit) {
    GameScaffold(GameName.WEREWOLF, onQuit = onHome, ambiance = Ambiance.Werewolf) {
        vm.error?.let { ErrorPanel(it, onHome); return@GameScaffold }
        val deal = vm.deal ?: return@GameScaffold
        when (val stage = vm.stage) {
            WerewolfStage.Intro -> Panel(night = true) {
                Title("Distribution des rôles")
                Text("Il y a ${deal.nbWolvesTotal} loups-garous.", style = MaterialTheme.typography.titleMedium)
                if (deal.hasThief) {
                    Text(
                        "Le Voleur est en jeu : ajoutez 2 cartes Villageois face cachée au centre de la table.",
                        textAlign = TextAlign.Center,
                    )
                }
                Text("Cartes en jeu :", fontWeight = FontWeight.Bold)
                deal.deck.groupingBy { it.name }.eachCount().forEach { (name, count) ->
                    val card = deal.deck.first { it.name == name }
                    Row(modifier = Modifier.fillMaxWidth(), verticalAlignment = Alignment.CenterVertically) {
                        RoleImage(card, 40)
                        Spacer(Modifier.width(12.dp))
                        Text(if (count > 1) "$name × $count" else name)
                    }
                }
                BigButton("Découvrir les rôles", vm::next)
            }
            is WerewolfStage.Player -> Panel(night = true) {
                Title("Joueur ${stage.player}/${deal.deck.size}")
                val card = deal.deck[stage.player - 1]
                Text(
                    if (stage.revealed) "Vous jouez le rôle" else "Passez le téléphone au joueur ${stage.player}, sans regarder l'écran.",
                    textAlign = TextAlign.Center,
                )
                // Une carte neuve par joueur : la carte suivante ne doit jamais apparaitre pendant un retournement
                key(stage.player) {
                    RoleFlipCard(card, stage.revealed, onFlip = vm::next)
                }
                AnimatedVisibility(stage.revealed, enter = fadeIn(tween(400, delayMillis = 500))) {
                    card.help?.takeIf { it.isNotBlank() }?.let { Text(it, textAlign = TextAlign.Center) }
                }
                HorizontalDivider()
                BigButton(
                    when {
                        !stage.revealed -> "Voir mon rôle"
                        stage.player < deal.deck.size -> "J'ai vu, cacher mon rôle"
                        else -> "Terminer"
                    },
                    vm::next,
                )
            }
            WerewolfStage.Night -> Panel(night = true) {
                HowlingWolf()
                Title("C'est la nuit")
                Text("Le maître du jeu prend les commandes.", textAlign = TextAlign.Center)
                Text("Le capitaine est le joueur ${deal.captain}", style = MaterialTheme.typography.titleMedium, color = Success)
                RoleImage(Card(0, 4, "Capitaine", image = "/images/wolf_capitaine.png"), 140)
                EndButtons(onReplay = vm::restart, onHome = onHome)
            }
        }
    }
}

/** Carte de role : dos mysterieux, qui se retourne quand le joueur la decouvre. */
@Composable
private fun RoleFlipCard(card: Card, revealed: Boolean, onFlip: () -> Unit) {
    val rotation by animateFloatAsState(
        if (revealed) 180f else 0f, tween(700, easing = FastOutSlowInEasing), label = "flip",
    )
    val density = LocalDensity.current.density
    val gold = Color(0xFFE0B354)
    Box(
        modifier = Modifier
            .size(230.dp, 310.dp)
            .graphicsLayer {
                rotationY = rotation
                cameraDistance = 14 * density
            }
            .clickable(enabled = !revealed, onClick = onFlip),
        contentAlignment = Alignment.Center,
    ) {
        val shape = RoundedCornerShape(20.dp)
        if (rotation <= 90f) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .background(Brush.verticalGradient(listOf(Color(0xFF2B1F4F), Color(0xFF0E1530))), shape)
                    .border(3.dp, gold, shape)
                    .padding(16.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.SpaceEvenly,
            ) {
                Text("🌕", fontSize = 40.sp)
                Text("🐺", fontSize = 96.sp)
                Text("Touchez pour découvrir", color = gold, fontWeight = FontWeight.Bold)
            }
        } else {
            // Face retournee une seconde fois pour ne pas etre lue a l'envers
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .graphicsLayer { rotationY = 180f }
                    .background(Color(0xFFFFF8E1), shape)
                    .border(3.dp, gold, shape)
                    .padding(16.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.SpaceEvenly,
            ) {
                RoleImage(card, 180)
                Text(
                    card.name,
                    fontSize = 26.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFF241338),
                    textAlign = TextAlign.Center,
                )
            }
        }
    }
}

/** Loup qui hurle a la lune (se dresse puis retombe en boucle). */
@Composable
private fun HowlingWolf() {
    val transition = rememberInfiniteTransition(label = "howl")
    val lift by transition.animateFloat(
        initialValue = 0f,
        targetValue = 1f,
        animationSpec = infiniteRepeatable(tween(1400, easing = FastOutSlowInEasing), RepeatMode.Reverse),
        label = "lift",
    )
    Text(
        "🐺",
        fontSize = 72.sp,
        modifier = Modifier.graphicsLayer {
            rotationZ = -15f * lift
            translationY = -12 * density * lift
        },
    )
}

/** Image du role, embarquee dans l'application (fonctionne sans connexion). */
@Composable
fun RoleImage(card: Card, size: Int) {
    val context = LocalContext.current
    val bitmap = remember(card.image) {
        Werewolf.imageAsset(card)?.let { path ->
            runCatching { context.assets.open(path).use { BitmapFactory.decodeStream(it) } }.getOrNull()
        }
    }
    if (bitmap != null) {
        Image(bitmap.asImageBitmap(), contentDescription = card.name, modifier = Modifier.size(size.dp))
    } else {
        Spacer(Modifier.size(size.dp))
    }
}
