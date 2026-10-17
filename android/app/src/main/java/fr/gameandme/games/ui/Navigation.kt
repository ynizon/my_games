package fr.gameandme.games.ui

import fr.gameandme.games.game.GameSlug

import androidx.compose.runtime.Composable
import androidx.compose.ui.platform.LocalContext
import androidx.lifecycle.SavedStateHandle
import androidx.lifecycle.ViewModel
import androidx.lifecycle.createSavedStateHandle
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.lifecycle.viewmodel.initializer
import androidx.lifecycle.viewmodel.viewModelFactory
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import fr.gameandme.games.GamesApplication
import fr.gameandme.games.ui.account.AccountScreen
import fr.gameandme.games.ui.account.AccountViewModel
import fr.gameandme.games.ui.account.CardEditScreen
import fr.gameandme.games.ui.account.CardEditViewModel
import fr.gameandme.games.ui.account.MyCardsScreen
import fr.gameandme.games.ui.account.MyCardsViewModel
import fr.gameandme.games.ui.games.BrainstormScreen
import fr.gameandme.games.ui.games.BrainstormViewModel
import fr.gameandme.games.ui.games.RoundsScreen
import fr.gameandme.games.ui.games.RoundsViewModel
import fr.gameandme.games.ui.games.TimesUpScreen
import fr.gameandme.games.ui.games.TimesUpViewModel
import fr.gameandme.games.ui.games.WerewolfScreen
import fr.gameandme.games.ui.games.WerewolfViewModel

/** ViewModel construit avec les dependances de l'application et les arguments de navigation. */
@Composable
inline fun <reified VM : ViewModel> appViewModel(crossinline create: (GamesApplication, SavedStateHandle) -> VM): VM {
    val app = LocalContext.current.applicationContext as GamesApplication
    return viewModel(factory = viewModelFactory { initializer { create(app, createSavedStateHandle()) } })
}

private fun intArg(name: String, default: Int) = navArgument(name) { type = NavType.IntType; defaultValue = default }

@Composable
fun GamesNavHost(nav: NavHostController = rememberNavController()) {
    val home: () -> Unit = { nav.popBackStack("home", inclusive = false) }
    val back: () -> Unit = { nav.popBackStack() }

    NavHost(navController = nav, startDestination = "home") {
        composable("home") {
            HomeScreen(
                vm = appViewModel { app, _ -> HomeViewModel(app) },
                onGame = { nav.navigate("setup/${it.slug}") },
                onAccount = { nav.navigate("account") },
            )
        }
        composable("setup/{slug}") { entry ->
            val slug = entry.arguments?.getString("slug").orEmpty()
            SetupScreen(
                vm = appViewModel { app, _ -> SetupViewModel(app) },
                slug = slug,
                onBack = back,
                onPlay = { values, roles ->
                    val teams = values["nbteams"] ?: 2
                    nav.navigate(
                        when (slug) {
                            GameSlug.TIMES_UP -> "timesup?teams=$teams"
                            GameSlug.BRAINSTORM -> "brainstorm?teams=$teams&rounds=${values["nbcards"] ?: 2}"
                            GameSlug.WEREWOLF ->
                                "werewolf?players=${values["nbplayers"] ?: 8}&wolves=${values["nbwolfs"] ?: 2}&cards=${roles.joinToString(",")}"
                            else -> "rounds/$slug?teams=$teams&sets=${values["nbsets"] ?: 1}"
                        }
                    )
                },
            )
        }
        composable("timesup?teams={teams}", listOf(intArg("teams", 2))) {
            TimesUpScreen(appViewModel { app, state -> TimesUpViewModel(app, state) }, onHome = home)
        }
        composable("brainstorm?teams={teams}&rounds={rounds}", listOf(intArg("teams", 2), intArg("rounds", 2))) {
            BrainstormScreen(appViewModel { app, state -> BrainstormViewModel(app, state) }, onHome = home)
        }
        composable("rounds/{slug}?teams={teams}&sets={sets}", listOf(intArg("teams", 2), intArg("sets", 1))) {
            RoundsScreen(appViewModel { app, state -> RoundsViewModel(app, state) }, onHome = home)
        }
        composable(
            "werewolf?players={players}&wolves={wolves}&cards={cards}",
            listOf(intArg("players", 8), intArg("wolves", 2), navArgument("cards") { type = NavType.StringType; defaultValue = "" }),
        ) {
            WerewolfScreen(appViewModel { app, state -> WerewolfViewModel(app, state) }, onHome = home)
        }
        composable("account") {
            AccountScreen(
                vm = appViewModel { app, _ -> AccountViewModel(app) },
                onBack = back,
                onMyCards = { nav.navigate("cards") },
            )
        }
        composable("cards") {
            MyCardsScreen(
                vm = appViewModel { app, _ -> MyCardsViewModel(app) },
                onBack = back,
                onEdit = { nav.navigate("card/$it") },
            )
        }
        composable("card/{id}", listOf(navArgument("id") { type = NavType.IntType })) {
            CardEditScreen(vm = appViewModel { app, state -> CardEditViewModel(app, state) }, onDone = back)
        }
    }
}
