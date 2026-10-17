package fr.gameandme.games.ui.account

import android.os.Build
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.SecondaryTabRow
import androidx.compose.material3.Tab
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableIntStateOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalUriHandler
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import fr.gameandme.games.BuildConfig
import fr.gameandme.games.GamesApplication
import fr.gameandme.games.data.EmailRequest
import fr.gameandme.games.data.LoginRequest
import fr.gameandme.games.data.PasswordRequest
import fr.gameandme.games.data.ProfileRequest
import fr.gameandme.games.data.RegisterRequest
import fr.gameandme.games.data.apiCall
import fr.gameandme.games.ui.BigButton
import fr.gameandme.games.ui.Panel
import fr.gameandme.games.ui.ScreenScaffold
import fr.gameandme.games.ui.Success
import kotlinx.coroutines.launch
import retrofit2.HttpException

/** Pages publiques du site (confidentialite, suppression de compte), a cote de l'API. */
private val WEB_BASE_URL = BuildConfig.API_BASE_URL.substringBefore("api/")

class AccountViewModel(private val app: GamesApplication) : ViewModel() {
    val session = app.session
    var loading by mutableStateOf(false)
        private set
    var error by mutableStateOf<String?>(null)
        private set
    var info by mutableStateOf<String?>(null)
        private set

    private val deviceName = "${Build.MANUFACTURER} ${Build.MODEL}".trim()

    init {
        // Rafraichit les droits de l'utilisateur connecte
        if (session.token != null) {
            viewModelScope.launch { apiCall { app.api.me().data }.onSuccess { session.saveUser(it) } }
        }
    }

    fun login(email: String, password: String) = run {
        app.api.login(LoginRequest(email.trim(), password, deviceName)).also { session.save(it.token, it.user) }
    }

    fun register(name: String, email: String, password: String) = run {
        app.api.register(RegisterRequest(name.trim(), email.trim(), password, deviceName)).also { session.save(it.token, it.user) }
    }

    fun forgotPassword(email: String) = run {
        app.api.forgotPassword(EmailRequest(email.trim()))
        info = "Si cette adresse est connue, un lien de réinitialisation vient d'être envoyé."
    }

    fun updateProfile(name: String, nickname: String, email: String, password: String) = run {
        val user = app.api.updateProfile(
            ProfileRequest(name.trim(), email.trim(), nickname.trim().ifEmpty { null }, password.ifEmpty { null })
        ).data
        session.saveUser(user)
        info = "Profil mis à jour."
    }

    fun deleteAccount(password: String) = run {
        val response = app.api.deleteAccount(PasswordRequest(password))
        if (!response.isSuccessful) throw HttpException(response)
        session.clear()
        info = "Votre compte a été supprimé."
    }

    fun logout() {
        viewModelScope.launch {
            apiCall { app.api.logout() }
            session.clear()
        }
    }

    private fun run(block: suspend () -> Unit) {
        if (loading) return
        loading = true
        error = null
        info = null
        viewModelScope.launch {
            apiCall { block() }.onFailure { error = it.message }
            loading = false
        }
    }
}

@Composable
fun AccountScreen(vm: AccountViewModel, onBack: () -> Unit, onMyCards: () -> Unit) {
    val user = vm.session.user
    ScreenScaffold(title = if (user == null) "Connexion" else "Mon compte", onBack = onBack) { padding ->
        Column(
            modifier = Modifier
                .padding(padding)
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
        ) {
            if (user == null) LoginForms(vm) else {
                Panel {
                    Text("Bonjour ${user.nickname?.ifBlank { null } ?: user.name}", style = MaterialTheme.typography.titleLarge)
                    Text(user.email, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    if (user.canManageCards) {
                        BigButton("Mes cartes", onMyCards)
                        Text(
                            if (user.permissions.cardEdit) "Vous pouvez gérer et valider toutes les cartes."
                            else "Proposez vos cartes : elles seront ajoutées aux jeux après validation.",
                            style = MaterialTheme.typography.bodySmall,
                            textAlign = TextAlign.Center,
                        )
                    }
                }
                ProfileForm(vm)
                OutlinedButton(onClick = vm::logout, modifier = Modifier.fillMaxWidth()) { Text("Se déconnecter") }
                DeleteAccount(vm)
            }
            val uriHandler = LocalUriHandler.current
            TextButton(onClick = { uriHandler.openUri(WEB_BASE_URL + "confidentialite") }, modifier = Modifier.fillMaxWidth()) {
                Text("Règles de confidentialité")
            }
        }
    }
}

@Composable
private fun Messages(vm: AccountViewModel) {
    vm.error?.let { Text(it, color = MaterialTheme.colorScheme.error) }
    vm.info?.let { Text(it, color = Success) }
}

@Composable
private fun LoginForms(vm: AccountViewModel) {
    var tab by rememberSaveable { mutableIntStateOf(0) }
    var name by rememberSaveable { mutableStateOf("") }
    var email by rememberSaveable { mutableStateOf("") }
    var password by rememberSaveable { mutableStateOf("") }

    Panel {
        Text(
            "Un compte permet de proposer vos propres cartes. Il n'est pas nécessaire pour jouer.",
            textAlign = TextAlign.Center,
            style = MaterialTheme.typography.bodyMedium,
        )
        SecondaryTabRow(selectedTabIndex = tab) {
            Tab(selected = tab == 0, onClick = { tab = 0 }, text = { Text("Connexion") })
            Tab(selected = tab == 1, onClick = { tab = 1 }, text = { Text("Inscription") })
        }
        if (tab == 1) {
            OutlinedTextField(name, { name = it }, label = { Text("Nom") }, singleLine = true, modifier = Modifier.fillMaxWidth())
        }
        OutlinedTextField(
            email, { email = it }, label = { Text("E-mail") }, singleLine = true, modifier = Modifier.fillMaxWidth(),
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
        )
        OutlinedTextField(
            password, { password = it }, label = { Text("Mot de passe") }, singleLine = true, modifier = Modifier.fillMaxWidth(),
            visualTransformation = PasswordVisualTransformation(),
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
        )
        Messages(vm)
        if (tab == 0) {
            BigButton("Se connecter", { vm.login(email, password) }, enabled = !vm.loading)
            TextButton(onClick = { vm.forgotPassword(email) }, enabled = !vm.loading) { Text("Mot de passe oublié ?") }
        } else {
            BigButton("Créer mon compte", { vm.register(name, email, password) }, enabled = !vm.loading)
        }
    }
}

@Composable
private fun ProfileForm(vm: AccountViewModel) {
    val user = vm.session.user ?: return
    var name by rememberSaveable(user.id) { mutableStateOf(user.name) }
    var nickname by rememberSaveable(user.id) { mutableStateOf(user.nickname.orEmpty()) }
    var email by rememberSaveable(user.id) { mutableStateOf(user.email) }
    var password by rememberSaveable { mutableStateOf("") }

    Panel {
        Text("Mon profil", style = MaterialTheme.typography.titleMedium)
        OutlinedTextField(name, { name = it }, label = { Text("Nom") }, singleLine = true, modifier = Modifier.fillMaxWidth())
        OutlinedTextField(nickname, { nickname = it }, label = { Text("Pseudo") }, singleLine = true, modifier = Modifier.fillMaxWidth())
        OutlinedTextField(
            email, { email = it }, label = { Text("E-mail") }, singleLine = true, modifier = Modifier.fillMaxWidth(),
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
        )
        OutlinedTextField(
            password, { password = it }, label = { Text("Nouveau mot de passe (facultatif)") }, singleLine = true,
            modifier = Modifier.fillMaxWidth(), visualTransformation = PasswordVisualTransformation(),
        )
        Messages(vm)
        BigButton("Enregistrer", { vm.updateProfile(name, nickname, email, password); password = "" }, enabled = !vm.loading)
    }
}

@Composable
private fun DeleteAccount(vm: AccountViewModel) {
    var confirm by rememberSaveable { mutableStateOf(false) }
    var password by rememberSaveable { mutableStateOf("") }

    TextButton(onClick = { confirm = true }, modifier = Modifier.fillMaxWidth()) {
        Text("Supprimer mon compte", color = MaterialTheme.colorScheme.error)
    }
    if (confirm) {
        AlertDialog(
            onDismissRequest = { if (!vm.loading) confirm = false },
            title = { Text("Supprimer mon compte") },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text(
                        "La suppression est définitive. Vos cartes en attente sont effacées ; " +
                            "vos cartes validées restent dans les jeux, sans votre nom.",
                    )
                    OutlinedTextField(
                        password, { password = it }, label = { Text("Mot de passe") }, singleLine = true,
                        modifier = Modifier.fillMaxWidth(), visualTransformation = PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
                    )
                    vm.error?.let { Text(it, color = MaterialTheme.colorScheme.error) }
                }
            },
            confirmButton = {
                TextButton(onClick = { vm.deleteAccount(password) }, enabled = password.isNotEmpty() && !vm.loading) {
                    Text("Supprimer", color = MaterialTheme.colorScheme.error)
                }
            },
            dismissButton = {
                TextButton(onClick = { confirm = false; password = "" }, enabled = !vm.loading) { Text("Annuler") }
            },
        )
    }
}
