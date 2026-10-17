package fr.gameandme.games

import android.app.Application
import fr.gameandme.games.data.CatalogRepository
import fr.gameandme.games.data.GamesApi
import fr.gameandme.games.data.Session
import fr.gameandme.games.data.createApi
import fr.gameandme.games.ui.Sounds

/** Dependances partagees par toute l'application. */
class GamesApplication : Application() {
    lateinit var session: Session
        private set
    lateinit var api: GamesApi
        private set
    lateinit var catalog: CatalogRepository
        private set

    override fun onCreate() {
        super.onCreate()
        session = Session(this)
        api = createApi(session)
        catalog = CatalogRepository(this, api)
        Sounds.init(this)
    }
}
