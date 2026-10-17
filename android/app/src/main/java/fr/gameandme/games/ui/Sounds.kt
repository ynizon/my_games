package fr.gameandme.games.ui

import android.content.Context
import android.media.AudioAttributes
import android.media.SoundPool
import fr.gameandme.games.R

/** Sons des jeux (les memes que sur le site). */
enum class Sound(val res: Int) {
    Ok(R.raw.ok), Error(R.raw.error), Pass(R.raw.pass), Pause(R.raw.pause), Beep(R.raw.beep), Finish(R.raw.finish),
    // Sons propres a l'application
    Fanfare(R.raw.fanfare), Firework(R.raw.firework), Tick(R.raw.tick), Sparkle(R.raw.sparkle), Go(R.raw.go),
    Flip(R.raw.flip), Howl(R.raw.howl),
}

object Sounds {
    private var pool: SoundPool? = null
    private val ids = mutableMapOf<Sound, Int>()
    private val loaded = mutableSetOf<Int>()
    // Sons demandes avant la fin de leur chargement : joues des qu'ils sont prets
    private val pending = mutableSetOf<Int>()

    fun init(context: Context) {
        val attributes = AudioAttributes.Builder()
            .setUsage(AudioAttributes.USAGE_GAME)
            .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
            .build()
        val soundPool = SoundPool.Builder().setMaxStreams(8).setAudioAttributes(attributes).build()
        soundPool.setOnLoadCompleteListener { pool, sampleId, status ->
            if (status == 0) {
                synchronized(this) {
                    loaded.add(sampleId)
                    if (pending.remove(sampleId)) pool.play(sampleId, 1f, 1f, 1, 0, 1f)
                }
            }
        }
        Sound.entries.forEach { ids[it] = soundPool.load(context, it.res, 1) }
        pool = soundPool
    }

    fun play(sound: Sound) {
        val pool = pool ?: return
        val id = ids[sound] ?: return
        synchronized(this) {
            if (id in loaded) pool.play(id, 1f, 1f, 1, 0, 1f) else pending.add(id)
        }
    }
}
