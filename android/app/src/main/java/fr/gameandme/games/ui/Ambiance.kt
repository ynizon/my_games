package fr.gameandme.games.ui

import androidx.compose.animation.core.LinearEasing
import androidx.compose.animation.core.animateFloat
import androidx.compose.animation.core.infiniteRepeatable
import androidx.compose.animation.core.rememberInfiniteTransition
import androidx.compose.animation.core.tween
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.BoxWithConstraints
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.offset
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.remember
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.alpha
import androidx.compose.ui.draw.rotate
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalDensity
import androidx.compose.ui.unit.IntOffset
import androidx.compose.ui.unit.sp
import kotlin.math.PI
import kotlin.math.abs
import kotlin.math.sin
import kotlin.random.Random

/** Ambiance visuelle propre a chaque jeu : degrade de fond, couleur de barre et symboles qui flottent. */
enum class Ambiance(
    val bar: Color,
    val top: Color,
    val bottom: Color,
    val symbols: List<String>,
    val night: Boolean = false,
) {
    TimesUp(Color(0xFFEF6C00), Color(0xFFFFF3E0), Color(0xFFFFCC80), listOf("⏳", "⌛", "⏰", "🎭")),
    Brainstorm(Color(0xFF0277BD), Color(0xFFE1F5FE), Color(0xFFB3E5FC), listOf("💡", "🧠", "❓", "✨")),
    Taboo(Pink, Color(0xFFFCE4EC), Color(0xFFF8BBD0), listOf("🤫", "🚫", "🙊", "💬")),
    Pictionary(Color(0xFF7B1FA2), Color(0xFFFFFDE7), Color(0xFFE1BEE7), listOf("✏️", "🎨", "🖍️", "🖌️")),
    Werewolf(Color(0xFF1A2347), Color(0xFF0B1230), Color(0xFF241338), emptyList(), night = true),
}

/** Fond anime d'une partie, dessine derriere le contenu. */
@Composable
fun AmbianceBackground(ambiance: Ambiance, modifier: Modifier = Modifier) {
    val dark = isSystemInDarkTheme()
    // En theme sombre, les degrades clairs ne font que teinter le fond
    val brush = if (ambiance.night || !dark) {
        Brush.verticalGradient(listOf(ambiance.top, ambiance.bottom))
    } else {
        Brush.verticalGradient(listOf(ambiance.top.copy(alpha = 0.06f), ambiance.bottom.copy(alpha = 0.16f)))
    }
    val time by rememberInfiniteTransition(label = "ambiance").animateFloat(
        initialValue = 0f,
        targetValue = 1f,
        animationSpec = infiniteRepeatable(tween(LOOP_MS, easing = LinearEasing)),
        label = "time",
    )
    Box(modifier.fillMaxSize().background(brush)) {
        if (ambiance.night) NightSky { time } else FloatingSymbols(ambiance.symbols) { time }
    }
}

private const val LOOP_MS = 24_000

private class Floater(val symbol: String, val x: Float, val start: Float, val speed: Int, val size: Int, val phase: Float)

/** Symboles du jeu qui montent doucement en ondulant. [time] : 0..1 sur une boucle. */
@Composable
private fun FloatingSymbols(symbols: List<String>, time: () -> Float) {
    val floaters = remember(symbols) {
        val random = Random(symbols.hashCode())
        List(10) {
            Floater(symbols[it % symbols.size], random.nextFloat(), random.nextFloat(), 1 + random.nextInt(2),
                28 + random.nextInt(20), random.nextFloat() * 6.28f)
        }
    }
    BoxWithConstraints(Modifier.fillMaxSize()) {
        val density = LocalDensity.current
        val width = with(density) { maxWidth.toPx() }
        val height = with(density) { maxHeight.toPx() }
        floaters.forEach { f ->
            Text(
                f.symbol,
                fontSize = f.size.sp,
                modifier = Modifier
                    .offset {
                        val t = time()
                        // Un nombre entier de tours par boucle : pas de saut quand l'animation recommence
                        val progress = (f.start + t * f.speed) % 1f
                        val sway = sin(t * 2 * PI.toFloat() * f.speed + f.phase) * 24 * density.density
                        IntOffset((f.x * (width - 40 * density.density) + sway).toInt(), ((1.1f - progress * 1.3f) * height).toInt())
                    }
                    .rotate(sin(f.phase) * 20)
                    .alpha(0.22f),
            )
        }
    }
}

/** Nuit du Loup-Garou : etoiles qui scintillent et pleine lune. */
@Composable
private fun NightSky(time: () -> Float) {
    val stars = remember {
        val random = Random(13)
        List(70) { Triple(Offset(random.nextFloat(), random.nextFloat() * 0.85f), random.nextFloat() * 6.28f, 1 + random.nextInt(4)) }
    }
    Canvas(Modifier.fillMaxSize()) {
        val t = time()
        stars.forEach { (pos, phase, speed) ->
            val twinkle = 0.25f + 0.75f * abs(sin(t * 2 * PI.toFloat() * speed + phase))
            drawCircle(Color.White.copy(alpha = twinkle), radius = 1.6f * density, center = Offset(pos.x * size.width, pos.y * size.height))
        }
        // Lune avec halo
        val moon = Offset(size.width * 0.8f, size.height * 0.12f)
        val radius = 34 * density
        drawCircle(
            Brush.radialGradient(listOf(Color(0x55FFF8E1), Color.Transparent), center = moon, radius = radius * 3),
            radius = radius * 3,
            center = moon,
        )
        drawCircle(Color(0xFFFFF8E1), radius = radius, center = moon)
        drawCircle(Color(0x22B0A080), radius = radius * 0.25f, center = moon + Offset(-radius * 0.3f, -radius * 0.2f))
        drawCircle(Color(0x22B0A080), radius = radius * 0.15f, center = moon + Offset(radius * 0.35f, radius * 0.3f))
    }
}
