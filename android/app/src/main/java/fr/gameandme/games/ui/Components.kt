package fr.gameandme.games.ui

import androidx.activity.compose.BackHandler
import androidx.compose.animation.core.Animatable
import androidx.compose.animation.core.FastOutSlowInEasing
import androidx.compose.animation.core.RepeatMode
import androidx.compose.animation.core.Spring
import androidx.compose.animation.core.animateFloat
import androidx.compose.animation.core.animateIntAsState
import androidx.compose.animation.core.infiniteRepeatable
import androidx.compose.animation.core.rememberInfiniteTransition
import androidx.compose.animation.core.spring
import androidx.compose.animation.core.tween
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.ColumnScope
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.Pause
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FilledIconButton
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.IconButtonDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.TopAppBar
import androidx.compose.material3.TopAppBarDefaults
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.rotate
import androidx.compose.ui.draw.scale
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.StrokeCap
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.hapticfeedback.HapticFeedbackType
import androidx.compose.ui.platform.LocalHapticFeedback
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import fr.gameandme.games.game.TurnEntry

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ScreenScaffold(
    title: String,
    onBack: (() -> Unit)? = null,
    actions: @Composable () -> Unit = {},
    floatingActionButton: @Composable () -> Unit = {},
    barColor: Color? = null,
    content: @Composable (PaddingValues) -> Unit,
) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(title, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    if (onBack != null) {
                        IconButton(onClick = onBack) {
                            Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Retour")
                        }
                    }
                },
                actions = { actions() },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = barColor ?: MaterialTheme.colorScheme.primary,
                    titleContentColor = if (barColor != null) Color.White else MaterialTheme.colorScheme.onPrimary,
                    navigationIconContentColor = if (barColor != null) Color.White else MaterialTheme.colorScheme.onPrimary,
                    actionIconContentColor = if (barColor != null) Color.White else MaterialTheme.colorScheme.onPrimary,
                ),
            )
        },
        floatingActionButton = floatingActionButton,
        containerColor = MaterialTheme.colorScheme.background,
        content = content,
    )
}

/** Animation affichee par-dessus une partie (fin de tour, fin de partie). */
enum class Party { None, Confetti, Fireworks }

/**
 * Ecran d'une partie : confirmation avant de quitter.
 * [ambiance] : fond et couleurs du jeu ; [party] : animation de fete par-dessus.
 */
@Composable
fun GameScaffold(
    title: String,
    onQuit: () -> Unit,
    ambiance: Ambiance,
    party: Party = Party.None,
    content: @Composable ColumnScope.() -> Unit,
) {
    var confirm by remember { mutableStateOf(false) }
    BackHandler { confirm = true }
    if (confirm) {
        AlertDialog(
            onDismissRequest = { confirm = false },
            title = { Text("Quitter la partie ?") },
            text = { Text("La partie en cours sera perdue.") },
            confirmButton = { TextButton(onClick = onQuit) { Text("Quitter") } },
            dismissButton = { TextButton(onClick = { confirm = false }) { Text("Continuer") } },
        )
    }
    ScreenScaffold(title = title, onBack = { confirm = true }, barColor = ambiance.bar) { padding ->
        Box(modifier = Modifier.padding(padding).fillMaxSize()) {
            AmbianceBackground(ambiance)
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .padding(16.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.spacedBy(16.dp),
                content = content,
            )
            when (party) {
                Party.None -> Unit
                Party.Confetti -> Celebration(fireworks = false, durationMs = 2500)
                Party.Fireworks -> Celebration(durationMs = 7000)
            }
        }
    }
}

/** Bloc de contenu ; [night] : version sombre translucide (Loup-Garou). */
@Composable
fun Panel(modifier: Modifier = Modifier, night: Boolean = false, content: @Composable ColumnScope.() -> Unit) {
    Card(
        modifier = modifier.fillMaxWidth(),
        shape = RoundedCornerShape(16.dp),
        colors = if (night) {
            CardDefaults.cardColors(containerColor = Color(0xD9161C38), contentColor = Color.White)
        } else {
            CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface)
        },
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
    ) {
        Column(
            modifier = Modifier.padding(20.dp).fillMaxWidth(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.spacedBy(12.dp),
            content = content,
        )
    }
}

@Composable
fun BigButton(text: String, onClick: () -> Unit, modifier: Modifier = Modifier, enabled: Boolean = true) {
    Button(
        onClick = onClick,
        enabled = enabled,
        modifier = modifier.fillMaxWidth().height(56.dp),
        shape = RoundedCornerShape(12.dp),
    ) {
        Text(text, fontSize = 18.sp, fontWeight = FontWeight.Bold)
    }
}

@Composable
fun Title(text: String) {
    Text(text, style = MaterialTheme.typography.headlineSmall, fontWeight = FontWeight.Bold, textAlign = TextAlign.Center)
}

/** Chrono circulaire ; rouge et qui bat sur les dernieres secondes. */
@Composable
fun TimerRing(timeLeft: Int, total: Int, modifier: Modifier = Modifier) {
    val color = if (timeLeft <= 3) MaterialTheme.colorScheme.error else MaterialTheme.colorScheme.primary
    val pulse = remember { Animatable(1f) }
    LaunchedEffect(timeLeft) {
        if (timeLeft in 1..LAST_SECONDS) {
            pulse.snapTo(1.18f)
            pulse.animateTo(1f, spring(dampingRatio = Spring.DampingRatioMediumBouncy))
        }
    }
    Box(modifier = modifier.size(120.dp).scale(pulse.value), contentAlignment = Alignment.Center) {
        CircularProgressIndicator(
            progress = { if (total > 0) timeLeft.coerceAtLeast(0) / total.toFloat() else 0f },
            modifier = Modifier.fillMaxSize(),
            color = color,
            strokeWidth = 10.dp,
            trackColor = MaterialTheme.colorScheme.primaryContainer,
            strokeCap = StrokeCap.Round,
        )
        Text("$timeLeft", fontSize = 40.sp, fontWeight = FontWeight.Bold, color = color)
    }
}

/** Boutons rate / pause / trouve, comme sur le site. */
@Composable
fun AnswerBar(
    paused: Boolean,
    onMiss: (() -> Unit)?,
    onPause: () -> Unit,
    onFound: () -> Unit,
    missLabel: String = "RATÉ",
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceEvenly,
        verticalAlignment = Alignment.CenterVertically,
    ) {
        val haptic = LocalHapticFeedback.current
        // Vibration de validation en plus du son (joue par le ViewModel)
        RoundAction(Icons.Default.Close, missLabel, Warning,
            onMiss?.let { miss -> { haptic.performHapticFeedback(HapticFeedbackType.Reject); miss() } }, enabled = !paused)
        RoundAction(
            if (paused) Icons.Default.PlayArrow else Icons.Default.Pause,
            "PAUSE",
            MaterialTheme.colorScheme.secondary,
            onPause,
            size = 56,
        )
        RoundAction(Icons.Default.Check, "OK", Success,
            { haptic.performHapticFeedback(HapticFeedbackType.Confirm); onFound() }, enabled = !paused)
    }
}

@Composable
private fun RoundAction(
    icon: ImageVector,
    label: String,
    color: Color,
    onClick: (() -> Unit)?,
    enabled: Boolean = true,
    size: Int = 76,
) {
    Column(horizontalAlignment = Alignment.CenterHorizontally) {
        if (onClick != null) {
            FilledIconButton(
                onClick = onClick,
                enabled = enabled,
                modifier = Modifier.size(size.dp),
                colors = IconButtonDefaults.filledIconButtonColors(containerColor = color, contentColor = Color.White),
            ) {
                Icon(icon, contentDescription = label, modifier = Modifier.size((size / 2).dp))
            }
        } else {
            Spacer(Modifier.size(size.dp))
        }
        Text(if (onClick != null) label else "", style = MaterialTheme.typography.labelMedium)
    }
}

/** Cartes jouees pendant le tour ; un appui corrige trouvee / ratee. */
@Composable
fun TurnSummary(entries: List<TurnEntryUi>, onToggle: ((Int) -> Unit)?) {
    if (entries.isEmpty()) {
        Text("Aucune carte jouée.", style = MaterialTheme.typography.bodyMedium)
        return
    }
    if (onToggle != null) {
        Text(
            "Touchez une carte pour corriger le résultat.",
            style = MaterialTheme.typography.bodySmall,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
        )
    }
    Column(verticalArrangement = Arrangement.spacedBy(6.dp), modifier = Modifier.fillMaxWidth()) {
        entries.forEachIndexed { index, entry ->
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(
                        (if (entry.found) Success else Warning).copy(alpha = 0.18f),
                        RoundedCornerShape(10.dp),
                    )
                    .then(if (onToggle != null) Modifier.clickable { onToggle(index) } else Modifier)
                    .padding(horizontal = 14.dp, vertical = 12.dp),
                verticalAlignment = Alignment.CenterVertically,
            ) {
                Icon(
                    if (entry.found) Icons.Default.Check else Icons.Default.Close,
                    contentDescription = null,
                    tint = if (entry.found) Success else Warning,
                )
                Spacer(Modifier.width(10.dp))
                Text(entry.name, modifier = Modifier.weight(1f))
                entry.trailing?.invoke()
            }
        }
    }
}

/** Carte du recapitulatif de tour (copie immuable pour l'affichage). */
class TurnEntryUi(val name: String, val found: Boolean, val trailing: (@Composable () -> Unit)? = null)

fun TurnEntry.toUi(trailing: (@Composable () -> Unit)? = null) = TurnEntryUi(card.name, found, trailing)

/** Scores par equipe ; [rows] = (libelle, score de chaque equipe). */
@Composable
fun ScoreTable(nbTeams: Int, rows: List<Pair<String, List<Int>>>, highlightTeam: Int? = null) {
    Column(modifier = Modifier.fillMaxWidth()) {
        ScoreRow("Équipes", (1..nbTeams).map { "$it" }, bold = true, highlightTeam = highlightTeam)
        rows.forEachIndexed { index, (label, scores) ->
            // Les scores defilent jusqu'a leur nouvelle valeur
            val shown = scores.map { animateIntAsState(it, tween(900), label = "score").value }
            ScoreRow(label, shown.map { "$it" }, bold = index == rows.lastIndex && rows.size > 1, highlightTeam = highlightTeam)
        }
    }
}

@Composable
private fun ScoreRow(label: String, values: List<String>, bold: Boolean, highlightTeam: Int?) {
    Row(modifier = Modifier.fillMaxWidth().padding(vertical = 6.dp), verticalAlignment = Alignment.CenterVertically) {
        Text(label, modifier = Modifier.weight(1.4f), fontWeight = if (bold) FontWeight.Bold else FontWeight.Normal)
        values.forEachIndexed { index, value ->
            Box(modifier = Modifier.weight(1f), contentAlignment = Alignment.Center) {
                val highlighted = highlightTeam == index + 1
                Text(
                    value,
                    fontWeight = if (bold || highlighted) FontWeight.Bold else FontWeight.Normal,
                    color = if (highlighted) MaterialTheme.colorScheme.primary else Color.Unspecified,
                )
            }
        }
    }
}

@Composable
fun WinnerText(winners: List<Int>) {
    val text = when {
        winners.size > 1 -> "Match nul entre les équipes ${winners.joinToString(", ")}"
        winners.size == 1 -> "Félicitations équipe ${winners.first()} !"
        else -> ""
    }
    if (winners.isNotEmpty()) {
        // Trophee qui sautille
        val bounce = rememberInfiniteTransition(label = "trophy")
        val scale by bounce.animateFloat(
            initialValue = 0.9f,
            targetValue = 1.15f,
            animationSpec = infiniteRepeatable(tween(600, easing = FastOutSlowInEasing), RepeatMode.Reverse),
            label = "trophyScale",
        )
        val angle by bounce.animateFloat(
            initialValue = -8f,
            targetValue = 8f,
            animationSpec = infiniteRepeatable(tween(900), RepeatMode.Reverse),
            label = "trophyAngle",
        )
        Text(if (winners.size > 1) "🤝" else "🏆", fontSize = 72.sp, modifier = Modifier.scale(scale).rotate(angle))
    }
    Text(text, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold, textAlign = TextAlign.Center,
        color = MaterialTheme.colorScheme.primary)
}

@Composable
fun EndButtons(onReplay: () -> Unit, onHome: () -> Unit) {
    BigButton("Rejouer", onReplay)
    OutlinedButton(onClick = onHome, modifier = Modifier.fillMaxWidth().height(52.dp), shape = RoundedCornerShape(12.dp)) {
        Text("Accueil")
    }
}

@Composable
fun TeamBadge(team: Int) {
    Box(
        modifier = Modifier
            .background(MaterialTheme.colorScheme.secondary, CircleShape)
            .padding(horizontal = 14.dp, vertical = 6.dp),
    ) {
        Text("Équipe $team", color = MaterialTheme.colorScheme.onSecondary, fontWeight = FontWeight.Bold)
    }
}

fun teamGo(team: Int) = "Équipe $team, allez-y !"

/** Nombre de secondes de fin de chrono rythmees par un tic et une pulsation. */
const val LAST_SECONDS = 5
