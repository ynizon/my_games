package fr.gameandme.games.data

import org.junit.Assert.assertEquals
import org.junit.Assert.assertTrue
import org.junit.Test
import java.io.File

class CatalogTest {
    private fun card(id: Int, gameId: Int, name: String) = Card(id = id, gameId = gameId, name = name)

    @Test
    fun withoutDuplicates_removesSameIdAndSameNameInSameGame() {
        val catalog = Catalog(
            lang = "fr",
            version = "1",
            games = emptyList(),
            cards = listOf(
                card(1, 2, "Les Requins"),
                card(1, 2, "Les Requins"),      // meme id
                card(2, 2, "les requins !"),    // meme nom, casse et ponctuation differentes
                card(3, 2, "Légumes verts"),
                card(4, 2, "Legumes  verts"),   // meme nom sans accent
                card(5, 3, "Les Requins"),      // meme nom mais autre jeu : garde
            ),
            werewolfCards = listOf(card(10, 4, "Voyante"), card(10, 4, "Voyante")),
        )

        val clean = catalog.withoutDuplicates()

        assertEquals(listOf(1, 3, 5), clean.cards.map { it.id })
        assertEquals(listOf(10), clean.werewolfCards.map { it.id })
    }

    @Test
    fun normalizedName_ignoresAccentsCaseAndPunctuation() {
        assertEquals("ile de la reunion", normalizedName("L'Île de la Réunion").removePrefix("l "))
        assertEquals(normalizedName("Chou-fleur"), normalizedName("chou fleur"))
    }

    @Test
    fun bundledCatalog_isReadableAndPlayable() {
        // Copie integree a l'application (php artisan catalog:export)
        val catalog = json.decodeFromString<Catalog>(File("src/main/assets/catalog_fr.json").readText())

        val slugs = catalog.games.map { it.slug }.toSet()
        assertTrue(slugs.containsAll(listOf("lestroismanches", "checklist", "motsinterdits", "croquisexpress", "lesloupsduvillage")))
        catalog.games.filter { it.slug != "lesloupsduvillage" }.forEach { game ->
            assertTrue("pas de cartes pour ${game.slug}", catalog.cardsFor(game.id).isNotEmpty())
        }
        assertTrue(catalog.werewolfCards.isNotEmpty())
        assertEquals(catalog.cards.size, catalog.withoutDuplicates().cards.size)
    }
}
