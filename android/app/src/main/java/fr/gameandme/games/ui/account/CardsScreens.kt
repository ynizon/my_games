package fr.gameandme.games.ui.account

import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.FilterChip
import androidx.compose.material3.FloatingActionButton
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Switch
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateListOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.SavedStateHandle
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import fr.gameandme.games.GamesApplication
import fr.gameandme.games.data.Card
import fr.gameandme.games.data.CardRequest
import fr.gameandme.games.data.CatalogRepository
import fr.gameandme.games.data.Game
import fr.gameandme.games.data.apiCall
import fr.gameandme.games.ui.BigButton
import fr.gameandme.games.ui.Panel
import fr.gameandme.games.ui.ScreenScaffold
import fr.gameandme.games.ui.Success
import fr.gameandme.games.ui.Warning
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

// ---------------------------------------------------------------- Liste

class MyCardsViewModel(private val app: GamesApplication) : ViewModel() {
    val games: List<Game> get() = app.catalog.catalog.value?.games.orEmpty()
    var cards by mutableStateOf<List<Card>>(emptyList())
        private set
    var loading by mutableStateOf(false)
        private set
    var error by mutableStateOf<String?>(null)
        private set

    fun refresh() {
        loading = true
        viewModelScope.launch {
            apiCall { app.api.myCards().data }
                .onSuccess { cards = it; error = null }
                .onFailure { error = it.message }
            loading = false
        }
    }
}

@Composable
fun MyCardsScreen(vm: MyCardsViewModel, onBack: () -> Unit, onEdit: (Int) -> Unit) {
    var gameFilter by remember { mutableStateOf<Int?>(null) }
    LaunchedEffect(Unit) { vm.refresh() }

    ScreenScaffold(
        title = "Mes cartes",
        onBack = onBack,
        floatingActionButton = {
            FloatingActionButton(onClick = { onEdit(0) }, containerColor = MaterialTheme.colorScheme.primary) {
                Icon(Icons.Default.Add, contentDescription = "Nouvelle carte")
            }
        },
    ) { padding ->
        LazyColumn(
            modifier = Modifier.padding(padding).fillMaxSize(),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(8.dp),
        ) {
            item {
                Row(modifier = Modifier.horizontalScroll(rememberScrollState()), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    FilterChip(selected = gameFilter == null, onClick = { gameFilter = null }, label = { Text("Tous") })
                    vm.games.forEach { game ->
                        FilterChip(selected = gameFilter == game.id, onClick = { gameFilter = game.id }, label = { Text(game.name) })
                    }
                }
            }
            if (vm.loading) item { LinearProgressIndicator(modifier = Modifier.fillMaxWidth()) }
            vm.error?.let { item { Text(it, color = MaterialTheme.colorScheme.error) } }
            val shown = vm.cards.filter { gameFilter == null || it.gameId == gameFilter }
            if (!vm.loading && shown.isEmpty()) {
                item { Text("Aucune carte. Touchez + pour en proposer une.") }
            }
            items(shown, key = { it.id }) { card ->
                Panel(modifier = Modifier.clickable { onEdit(card.id) }) {
                    Row(modifier = Modifier.fillMaxWidth(), verticalAlignment = Alignment.CenterVertically) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text(card.name, fontWeight = FontWeight.Bold)
                            Text(
                                vm.games.firstOrNull { it.id == card.gameId }?.name.orEmpty(),
                                style = MaterialTheme.typography.bodySmall,
                            )
                        }
                        Text(
                            if (card.status == 1) "Validée" else "En attente",
                            color = if (card.status == 1) Success else Warning,
                            style = MaterialTheme.typography.labelLarge,
                        )
                    }
                }
            }
        }
    }
}

// ---------------------------------------------------------------- Edition

class CardEditViewModel(private val app: GamesApplication, state: SavedStateHandle) : ViewModel() {
    val cardId: Int = state["id"] ?: 0
    val games: List<Game> = app.catalog.catalog.value?.games.orEmpty()
    val canValidate: Boolean = app.session.user?.permissions?.cardEdit == true

    var gameId by mutableStateOf(games.firstOrNull()?.id ?: 1)
    var name by mutableStateOf("")
    var difficulty by mutableStateOf(1)
    var validated by mutableStateOf(canValidate)
    val words = mutableStateListOf<String>()

    var loading by mutableStateOf(cardId != 0)
        private set
    var error by mutableStateOf<String?>(null)
        private set
    var doubles by mutableStateOf<List<String>>(emptyList())
        private set
    var done by mutableStateOf(false)
        private set

    val nbWords: Int get() = games.firstOrNull { it.id == gameId }?.nbWords ?: 0

    init {
        if (cardId != 0) {
            viewModelScope.launch {
                apiCall { app.api.card(cardId).data }
                    .onSuccess { card ->
                        gameId = card.gameId
                        name = card.name
                        difficulty = card.difficulty
                        validated = card.status == 1
                        words.clear()
                        // Loup-Garou : le mot est l'extension
                        words.addAll(if (card.words.isEmpty() && card.extension != null) listOf(card.extension) else card.words)
                    }
                    .onFailure { error = it.message }
                loading = false
            }
        }
    }

    /** Cartes au nom proche dans le meme jeu (hors carte editee). */
    suspend fun checkDoubles() {
        if (name.trim().length < 3) {
            doubles = emptyList()
            return
        }
        apiCall { app.api.checkDouble(name.trim(), gameId, CatalogRepository.DEFAULT_LANG).data }
            .onSuccess { found -> doubles = found.filter { it.id != cardId }.map { it.name } }
    }

    fun save() {
        loading = true
        error = null
        val request = CardRequest(
            name = name.trim(),
            gameId = gameId,
            lang = CatalogRepository.DEFAULT_LANG,
            difficulty = difficulty,
            words = (0 until nbWords).map { words.getOrNull(it).orEmpty().trim() },
            status = if (canValidate) (if (validated) 1 else 0) else null,
        )
        viewModelScope.launch {
            apiCall { if (cardId == 0) app.api.createCard(request) else app.api.updateCard(cardId, request) }
                .onSuccess { done = true }
                .onFailure { error = it.message }
            loading = false
        }
    }

    fun delete() {
        loading = true
        viewModelScope.launch {
            apiCall { app.api.deleteCard(cardId) }
                .onSuccess { done = true }
                .onFailure { error = it.message }
            loading = false
        }
    }
}

@Composable
fun CardEditScreen(vm: CardEditViewModel, onDone: () -> Unit) {
    var confirmDelete by remember { mutableStateOf(false) }
    LaunchedEffect(vm.done) { if (vm.done) onDone() }
    LaunchedEffect(vm.name, vm.gameId) {
        delay(600)
        vm.checkDoubles()
    }
    if (confirmDelete) {
        AlertDialog(
            onDismissRequest = { confirmDelete = false },
            title = { Text("Supprimer la carte ?") },
            confirmButton = { TextButton(onClick = { confirmDelete = false; vm.delete() }) { Text("Supprimer") } },
            dismissButton = { TextButton(onClick = { confirmDelete = false }) { Text("Annuler") } },
        )
    }

    ScreenScaffold(
        title = if (vm.cardId == 0) "Nouvelle carte" else "Modifier la carte",
        onBack = onDone,
        actions = {
            if (vm.cardId != 0) {
                IconButton(onClick = { confirmDelete = true }) { Icon(Icons.Default.Delete, contentDescription = "Supprimer") }
            }
        },
    ) { padding ->
        Column(
            modifier = Modifier
                .padding(padding)
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
        ) {
            if (vm.loading) LinearProgressIndicator(modifier = Modifier.fillMaxWidth())
            Panel {
                Text("Jeu", fontWeight = FontWeight.Bold, modifier = Modifier.fillMaxWidth())
                Row(modifier = Modifier.horizontalScroll(rememberScrollState()), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    vm.games.forEach { game ->
                        FilterChip(selected = vm.gameId == game.id, onClick = { vm.gameId = game.id }, label = { Text(game.name) })
                    }
                }
                OutlinedTextField(vm.name, { vm.name = it }, label = { Text("Nom") }, singleLine = true, modifier = Modifier.fillMaxWidth())
                if (vm.doubles.isNotEmpty()) {
                    Text(
                        "Cartes proches déjà existantes : " + vm.doubles.take(5).joinToString(", "),
                        color = Warning,
                        style = MaterialTheme.typography.bodySmall,
                    )
                }
                Text("Difficulté", fontWeight = FontWeight.Bold, modifier = Modifier.fillMaxWidth())
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                    listOf(1 to "Facile", 2 to "Moyen", 3 to "Difficile").forEach { (value, label) ->
                        FilterChip(selected = vm.difficulty == value, onClick = { vm.difficulty = value }, label = { Text(label) })
                    }
                }
            }
            if (vm.nbWords > 0) {
                Panel {
                    Text(
                        when (vm.nbWords) {
                            1 -> "Extension"
                            5 -> "Mots tabous"
                            else -> "Références à trouver"
                        },
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.fillMaxWidth(),
                    )
                    repeat(vm.nbWords) { index ->
                        OutlinedTextField(
                            value = vm.words.getOrNull(index).orEmpty(),
                            onValueChange = { value ->
                                while (vm.words.size <= index) vm.words.add("")
                                vm.words[index] = value
                            },
                            label = { Text(if (vm.nbWords == 1) "Extension" else "Mot ${index + 1}") },
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                        )
                    }
                }
            }
            if (vm.canValidate) {
                Panel {
                    Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.fillMaxWidth()) {
                        Text("Carte validée (visible dans les jeux)", modifier = Modifier.weight(1f))
                        Switch(checked = vm.validated, onCheckedChange = { vm.validated = it })
                    }
                }
            } else {
                Text(
                    "Votre carte sera visible dans les jeux après validation par un administrateur.",
                    style = MaterialTheme.typography.bodySmall,
                )
            }
            vm.error?.let { Text(it, color = MaterialTheme.colorScheme.error) }
            BigButton("Enregistrer", vm::save, enabled = !vm.loading && vm.name.isNotBlank())
        }
    }
}
