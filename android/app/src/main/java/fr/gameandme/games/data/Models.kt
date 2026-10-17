package fr.gameandme.games.data

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

/** Reponses de l'API Laravel (/api/v1), voir docs/api.md. */

@Serializable
data class DataList<T>(val data: List<T>, val meta: CardsMeta? = null)

@Serializable
data class DataItem<T>(val data: T)

@Serializable
data class CardsMeta(val lang: String, val version: String)

@Serializable
data class CardsVersion(val lang: String, val version: String)

@Serializable
data class Bounds(val min: Int, val max: Int)

@Serializable
data class Game(
    val id: Int,
    val slug: String,
    val name: String,
    val description: String = "",
    @SerialName("nb_words") val nbWords: Int = 0,
    val settings: Map<String, Bounds> = emptyMap(),
)

@Serializable
data class Card(
    val id: Int,
    @SerialName("game_id") val gameId: Int,
    val name: String,
    val lang: String = "fr",
    val country: String = "-",
    val difficulty: Int = 1,
    val words: List<String> = emptyList(),
    val status: Int = 1,
    // Loup-Garou uniquement
    val extension: String? = null,
    val image: String? = null,
    val help: String? = null,
    val role: String? = null,
    val copies: Int = 1,
    @SerialName("is_wolf") val isWolf: Boolean = false,
)

@Serializable
data class Permissions(
    @SerialName("card_edit") val cardEdit: Boolean = false,
    @SerialName("card_propose") val cardPropose: Boolean = false,
)

@Serializable
data class User(
    val id: Int,
    val name: String,
    val nickname: String? = null,
    val email: String,
    val lang: String = "fr",
    val roles: List<String> = emptyList(),
    val permissions: Permissions = Permissions(),
) {
    val canManageCards: Boolean get() = permissions.cardEdit || permissions.cardPropose
}

@Serializable
data class AuthResponse(val token: String, val user: User)

@Serializable
data class LoginRequest(
    val email: String,
    val password: String,
    @SerialName("device_name") val deviceName: String,
)

@Serializable
data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    @SerialName("device_name") val deviceName: String,
)

@Serializable
data class EmailRequest(val email: String)

@Serializable
data class PasswordRequest(val password: String)

@Serializable
data class ProfileRequest(
    val name: String,
    val email: String,
    val nickname: String? = null,
    val password: String? = null,
)

@Serializable
data class CardRequest(
    val name: String,
    @SerialName("game_id") val gameId: Int,
    val lang: String,
    val difficulty: Int,
    val words: List<String>,
    val status: Int? = null,
)

@Serializable
data class ApiErrorBody(
    val message: String = "",
    val errors: Map<String, List<String>> = emptyMap(),
)
