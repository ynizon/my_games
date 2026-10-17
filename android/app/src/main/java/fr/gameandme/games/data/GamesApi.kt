package fr.gameandme.games.data

import fr.gameandme.games.BuildConfig
import kotlinx.serialization.SerializationException
import kotlinx.serialization.json.Json
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import retrofit2.HttpException
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.kotlinx.serialization.asConverterFactory
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.HTTP
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path
import retrofit2.http.Query
import java.io.IOException
import java.util.concurrent.TimeUnit

interface GamesApi {
    @GET("games")
    suspend fun games(): DataList<Game>

    @GET("cards/version")
    suspend fun cardsVersion(@Query("lang") lang: String): CardsVersion

    @GET("cards")
    suspend fun cards(@Query("lang") lang: String): DataList<Card>

    @GET("werewolf/cards")
    suspend fun werewolfCards(@Query("lang") lang: String): DataList<Card>

    @POST("auth/login")
    suspend fun login(@Body body: LoginRequest): AuthResponse

    @POST("auth/register")
    suspend fun register(@Body body: RegisterRequest): AuthResponse

    @POST("auth/forgot-password")
    suspend fun forgotPassword(@Body body: EmailRequest): ApiErrorBody

    @POST("auth/logout")
    suspend fun logout(): Response<Unit>

    @GET("me")
    suspend fun me(): DataItem<User>

    @PUT("me")
    suspend fun updateProfile(@Body body: ProfileRequest): DataItem<User>

    @HTTP(method = "DELETE", path = "me", hasBody = true)
    suspend fun deleteAccount(@Body body: PasswordRequest): Response<Unit>

    @GET("my/cards")
    suspend fun myCards(): DataList<Card>

    @GET("my/cards/check-double")
    suspend fun checkDouble(
        @Query("name") name: String,
        @Query("game_id") gameId: Int,
        @Query("lang") lang: String,
    ): DataList<Card>

    @GET("my/cards/{id}")
    suspend fun card(@Path("id") id: Int): DataItem<Card>

    @POST("my/cards")
    suspend fun createCard(@Body body: CardRequest): DataItem<Card>

    @PUT("my/cards/{id}")
    suspend fun updateCard(@Path("id") id: Int, @Body body: CardRequest): DataItem<Card>

    @DELETE("my/cards/{id}")
    suspend fun deleteCard(@Path("id") id: Int): Response<Unit>
}

val json = Json {
    ignoreUnknownKeys = true
    explicitNulls = false
    coerceInputValues = true
}

fun createApi(session: Session): GamesApi {
    val client = OkHttpClient.Builder()
        .connectTimeout(15, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .addInterceptor { chain ->
            val request = chain.request().newBuilder().header("Accept", "application/json")
            session.token?.let { request.header("Authorization", "Bearer $it") }
            val response = chain.proceed(request.build())
            // Jeton revoque ou compte suspendu : on oublie la session
            if (response.code == 401 && session.token != null) {
                session.clear()
            }
            response
        }
        .build()

    return Retrofit.Builder()
        .baseUrl(BuildConfig.API_BASE_URL)
        .client(client)
        .addConverterFactory(json.asConverterFactory("application/json".toMediaType()))
        .build()
        .create(GamesApi::class.java)
}

/** Erreur lisible par l'utilisateur, avec les erreurs de validation par champ. */
class ApiException(message: String, val fieldErrors: Map<String, List<String>> = emptyMap()) : Exception(message)

suspend fun <T> apiCall(block: suspend () -> T): Result<T> = try {
    Result.success(block())
} catch (e: HttpException) {
    val body = e.response()?.errorBody()?.string()
    val error = runCatching { json.decodeFromString<ApiErrorBody>(body.orEmpty()) }.getOrNull()
    val message = when {
        error != null && error.errors.isNotEmpty() -> error.errors.values.flatten().joinToString("\n")
        error != null && error.message.isNotBlank() -> error.message
        e.code() == 401 -> "Veuillez vous reconnecter."
        e.code() == 403 -> "Action non autorisée."
        e.code() == 429 -> "Trop de tentatives, réessayez dans une minute."
        else -> "Erreur du serveur (${e.code()})."
    }
    Result.failure(ApiException(message, error?.errors.orEmpty()))
} catch (e: IOException) {
    Result.failure(ApiException("Pas de connexion au serveur."))
} catch (e: SerializationException) {
    Result.failure(ApiException("Réponse inattendue du serveur."))
}
