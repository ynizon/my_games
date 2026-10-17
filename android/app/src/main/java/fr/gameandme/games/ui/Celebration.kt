package fr.gameandme.games.ui

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableLongStateOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.runtime.withFrameNanos
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.StrokeCap
import androidx.compose.ui.graphics.drawscope.rotate
import androidx.compose.ui.layout.onSizeChanged
import androidx.compose.ui.platform.LocalDensity
import androidx.compose.ui.unit.IntSize
import kotlin.math.PI
import kotlin.math.abs
import kotlin.math.cos
import kotlin.math.sin
import kotlin.math.sqrt
import kotlin.random.Random

private val FESTIVE = listOf(
    Pink, Color(0xFFFFC107), Color(0xFF00BCD4), Color(0xFF8BC34A),
    Color(0xFFFF5722), Color(0xFF9C27B0), Color(0xFFFFEB3B), Color.White,
)

private enum class Kind { Rocket, Spark, Confetti }

private class Particle(
    val kind: Kind,
    var x: Float,
    var y: Float,
    var vx: Float,
    var vy: Float,
    val color: Color,
    val life: Float,
    val phase: Float = 0f,
) {
    var age = 0f
}

/**
 * Animation de fete par-dessus l'ecran : fusees qui explosent ([fireworks]) et/ou pluie de confettis ([confetti]).
 * Elle dure [durationMs], puis laisse retomber les dernieres particules. Ne capte pas les appuis.
 */
@Composable
fun Celebration(
    modifier: Modifier = Modifier,
    fireworks: Boolean = true,
    confetti: Boolean = true,
    durationMs: Long = 6000,
) {
    val particles = remember { ArrayList<Particle>() }
    var frame by remember { mutableLongStateOf(0L) }
    var size by remember { mutableStateOf(IntSize.Zero) }
    val dp = LocalDensity.current.density

    LaunchedEffect(Unit) {
        val random = Random(System.nanoTime())
        var last = 0L
        var elapsed = 0f
        var nextRocket = 0f
        var confettiBudget = 0f
        while (true) {
            val now = withFrameNanos { it }
            val dt = if (last == 0L) 0f else ((now - last) / 1e9f).coerceAtMost(0.05f)
            last = now
            elapsed += dt
            val w = size.width.toFloat()
            val h = size.height.toFloat()
            val running = elapsed * 1000 < durationMs
            if (w > 0f && h > 0f) {
                if (running && fireworks && elapsed >= nextRocket) {
                    // Hauteur d'explosion entre 30 % et 65 % de l'ecran
                    val rise = h * random.nextFloat().between(0.35f, 0.7f)
                    particles += Particle(
                        Kind.Rocket, w * random.nextFloat().between(0.15f, 0.85f), h,
                        random.nextFloat().between(-30f, 30f) * dp, -sqrt(2 * GRAVITY * dp * rise),
                        FESTIVE.random(random), life = 10f,
                    )
                    nextRocket = elapsed + random.nextFloat().between(0.35f, 0.8f)
                }
                if (running && confetti) {
                    confettiBudget += dt * 70
                    while (confettiBudget >= 1f) {
                        confettiBudget--
                        particles += Particle(
                            Kind.Confetti, w * random.nextFloat(), -10 * dp,
                            0f, random.nextFloat().between(90f, 180f) * dp,
                            FESTIVE.random(random), life = 10f, phase = random.nextFloat() * 6.28f,
                        )
                    }
                }
                update(particles, dt, dp, h, random)
            }
            frame = now
            if (!running && particles.isEmpty()) break
        }
    }

    Canvas(modifier.fillMaxSize().onSizeChanged { size = it }) {
        @Suppress("UNUSED_EXPRESSION") frame // relu a chaque image pour redessiner
        particles.forEach { p ->
            val alpha = (1f - p.age / p.life).coerceIn(0f, 1f)
            when (p.kind) {
                Kind.Rocket -> {
                    drawLine(p.color.copy(alpha = 0.5f), Offset(p.x, p.y), Offset(p.x - p.vx * 0.05f, p.y - p.vy * 0.05f),
                        strokeWidth = 3 * dp, cap = StrokeCap.Round)
                    drawCircle(Color.White, radius = 3 * dp, center = Offset(p.x, p.y))
                }
                Kind.Spark -> {
                    drawLine(p.color.copy(alpha = alpha * 0.6f), Offset(p.x, p.y), Offset(p.x - p.vx * 0.06f, p.y - p.vy * 0.06f),
                        strokeWidth = 2 * dp, cap = StrokeCap.Round)
                    drawCircle(p.color.copy(alpha = alpha), radius = 2.5f * dp, center = Offset(p.x, p.y))
                }
                Kind.Confetti -> rotate(p.age * 360f + p.phase * 57f, pivot = Offset(p.x, p.y)) {
                    // Largeur variable : effet de papier qui tourne sur lui-meme
                    val width = 8 * dp * abs(cos(p.age * 6 + p.phase)).coerceAtLeast(0.2f)
                    drawRect(p.color, topLeft = Offset(p.x - width / 2, p.y - 5 * dp), size = Size(width, 10 * dp))
                }
            }
        }
    }
}

private const val GRAVITY = 320f

private fun update(particles: ArrayList<Particle>, dt: Float, dp: Float, height: Float, random: Random) {
    val born = ArrayList<Particle>()
    val iterator = particles.iterator()
    while (iterator.hasNext()) {
        val p = iterator.next()
        p.age += dt
        when (p.kind) {
            Kind.Rocket -> {
                p.vy += GRAVITY * dp * dt
                if (p.vy >= -25 * dp) {
                    explode(p, born, dp, random)
                    iterator.remove()
                    continue
                }
            }
            Kind.Spark -> {
                p.vy += GRAVITY * 0.35f * dp * dt
                val drag = 1f - 1.4f * dt
                p.vx *= drag
                p.vy *= drag
            }
            Kind.Confetti -> p.vx = sin(p.age * 2.5f + p.phase) * 40 * dp
        }
        p.x += p.vx * dt
        p.y += p.vy * dt
        if (p.age >= p.life || p.y > height + 20 * dp) iterator.remove()
    }
    particles += born
}

private fun explode(rocket: Particle, into: MutableList<Particle>, dp: Float, random: Random) {
    Sounds.play(Sound.Firework)
    // Deux couleurs par bouquet
    val colors = listOf(rocket.color, FESTIVE.random(random))
    val count = 70
    for (i in 0 until count) {
        val angle = (2 * PI * i / count).toFloat() + random.nextFloat() * 0.1f
        val speed = random.nextFloat().between(110f, 240f) * dp
        into += Particle(
            Kind.Spark, rocket.x, rocket.y, cos(angle) * speed, sin(angle) * speed,
            colors[i % 2], life = random.nextFloat().between(1.0f, 1.7f),
        )
    }
}

private fun Float.between(min: Float, max: Float) = min + this * (max - min)
