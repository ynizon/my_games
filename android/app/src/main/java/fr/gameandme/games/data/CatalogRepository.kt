package fr.gameandme.games.data

import android.content.Context
import androidx.core.content.edit
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.withContext
import kotlinx.serialization.Serializable
import java.io.File
import java.text.Normalizer

/** Jeux et cartes telecharges, pour jouer sans connexion. */
@Serializable
data class Catalog(
    val lang: String,
    val version: String,
    val games: List<Game>,
    val cards: List<Card>,
    val werewolfCards: List<Card>,
    /** Copie integree a l'application (aucun telechargement n'a encore reussi). */
    val bundled: Boolean = false,
) {
    fun cardsFor(gameId: Int): List<Card> = cards.filter { it.gameId == gameId }

    /**
     * Catalogue sans doublon : une carte n'apparait qu'une fois par id, et un meme nom
     * (sans tenir compte des accents, de la casse ni de la ponctuation) une seule fois par jeu.
     * La premiere carte rencontree est gardee.
     */
    fun withoutDuplicates(): Catalog = copy(
        cards = cards.distinctBy { it.id }.distinctBy { it.gameId to normalizedName(it.name) },
        werewolfCards = werewolfCards.distinctBy { it.id },
    )
}

/** "Les Requins !" et "les requins" donnent la meme cle. */
internal fun normalizedName(name: String): String =
    Normalizer.normalize(name, Normalizer.Form.NFD)
        .replace(Regex("\\p{M}+"), "")
        .lowercase()
        .replace(Regex("[^a-z0-9]+"), " ")
        .trim()

class CatalogRepository(context: Context, private val api: GamesApi) {
    private val file = File(context.filesDir, "catalog.json")
    private val assets = context.assets
    /** Historique des cartes jouees : cle = id de la carte, valeur = date (ms) de sa derniere partie. */
    private val played = context.getSharedPreferences("played_cards", Context.MODE_PRIVATE)

    private val _catalog = MutableStateFlow<Catalog?>(null)
    val catalog: StateFlow<Catalog?> = _catalog

    /** Catalogue du telephone ; a defaut, celui integre a l'application (jouable des le premier lancement). */
    suspend fun loadLocal() = withContext(Dispatchers.IO) {
        if (_catalog.value != null) return@withContext
        val saved = if (file.exists()) runCatching { json.decodeFromString<Catalog>(file.readText()) }.getOrNull() else null
        _catalog.value = (saved ?: bundled())?.withoutDuplicates()
    }

    private fun bundled(): Catalog? = runCatching {
        assets.open("catalog_$DEFAULT_LANG.json").bufferedReader().use { json.decodeFromString<Catalog>(it.readText()) }
            .copy(bundled = true)
    }.getOrNull()

    /**
     * Met le catalogue a jour avec celui du site : il est retelecharge si la version des cartes
     * a change sur le serveur (ou si [force]). Sans connexion, le catalogue local reste utilisable.
     */
    suspend fun sync(lang: String = DEFAULT_LANG, force: Boolean = false): Result<Catalog> {
        loadLocal()
        val local = _catalog.value
        return apiCall {
            val version = api.cardsVersion(lang).version
            if (!force && local != null && local.lang == lang && local.version == version) {
                // Les cartes n'ont pas change ; la liste des jeux est legere, on la rafraichit
                local.copy(games = api.games().data, bundled = false)
            } else {
                val cards = api.cards(lang)
                Catalog(
                    lang = lang,
                    version = cards.meta?.version ?: version,
                    games = api.games().data,
                    cards = cards.data,
                    werewolfCards = api.werewolfCards(lang).data,
                )
            }.withoutDuplicates()
        }.onSuccess { catalog ->
            _catalog.value = catalog
            withContext(Dispatchers.IO) { file.writeText(json.encodeToString(Catalog.serializer(), catalog)) }
        }
    }

    /**
     * Date (ms) de la derniere partie de chaque carte jouee sur ce telephone, pour proposer en priorite
     * les cartes jamais jouees puis les plus anciennes. Garde a part du catalogue : survit a ses mises a jour.
     */
    fun lastPlayed(): Map<Int, Long> =
        played.all.mapNotNull { (id, time) -> id.toIntOrNull()?.let { key -> (time as? Long)?.let { key to it } } }.toMap()

    fun markPlayed(ids: Collection<Int>) {
        val now = System.currentTimeMillis()
        played.edit { ids.forEach { putLong(it.toString(), now) } }
    }

    companion object {
        const val DEFAULT_LANG = "fr"
    }
}
