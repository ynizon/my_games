package fr.gameandme.games.ui

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

// Couleurs du theme Material Dashboard du site
val Pink = Color(0xFFE91E63)
val PinkDark = Color(0xFFC2185B)
val Slate = Color(0xFF344767)
val Success = Color(0xFF4CAF50)
val Warning = Color(0xFFFB8C00)

private val LightColors = lightColorScheme(
    primary = Pink,
    onPrimary = Color.White,
    primaryContainer = Color(0xFFFCE4EC),
    onPrimaryContainer = PinkDark,
    secondary = Slate,
    onSecondary = Color.White,
    background = Color(0xFFF0F2F5),
    surface = Color.White,
    error = Color(0xFFF44335),
)

private val DarkColors = darkColorScheme(
    primary = Color(0xFFF06292),
    onPrimary = Color.Black,
    primaryContainer = PinkDark,
    onPrimaryContainer = Color.White,
    secondary = Color(0xFF8FA4C9),
    background = Color(0xFF121418),
    surface = Color(0xFF1E2128),
)

@Composable
fun GamesTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = if (isSystemInDarkTheme()) DarkColors else LightColors,
        content = content,
    )
}
