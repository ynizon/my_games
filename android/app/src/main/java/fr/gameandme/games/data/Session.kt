package fr.gameandme.games.data

import android.content.Context
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.core.content.edit

/** Jeton de l'API et utilisateur connecte, gardes entre deux lancements. */
class Session(context: Context) {
    private val prefs = context.getSharedPreferences("session", Context.MODE_PRIVATE)

    var token: String? = prefs.getString(KEY_TOKEN, null)
        private set

    var user: User? by mutableStateOf(
        prefs.getString(KEY_USER, null)?.let { runCatching { json.decodeFromString<User>(it) }.getOrNull() }
    )
        private set

    fun save(token: String, user: User) {
        this.token = token
        saveUser(user)
        prefs.edit { putString(KEY_TOKEN, token) }
    }

    fun saveUser(user: User) {
        this.user = user
        prefs.edit { putString(KEY_USER, json.encodeToString(User.serializer(), user)) }
    }

    fun clear() {
        token = null
        user = null
        prefs.edit { clear() }
    }

    private companion object {
        const val KEY_TOKEN = "token"
        const val KEY_USER = "user"
    }
}
