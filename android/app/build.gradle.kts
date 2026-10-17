plugins {
    alias(libs.plugins.android.application)
    alias(libs.plugins.kotlin.android)
    alias(libs.plugins.kotlin.compose)
    alias(libs.plugins.kotlin.serialization)
}

android {
    namespace = "fr.gameandme.games"
    compileSdk = 36

    defaultConfig {
        applicationId = "fr.gameandme.games"
        minSdk = 26
        targetSdk = 36
        versionCode = 2
        versionName = "1.0"
    }

    // Cle d'upload Play Store : proprietes GAMES_UPLOAD_* dans ~/.gradle/gradle.properties (hors depot)
    val uploadStoreFile = project.findProperty("GAMES_UPLOAD_STORE_FILE") as String?
    signingConfigs {
        if (uploadStoreFile != null) {
            create("release") {
                storeFile = file(uploadStoreFile)
                storePassword = project.findProperty("GAMES_UPLOAD_STORE_PASSWORD") as String?
                keyAlias = project.findProperty("GAMES_UPLOAD_KEY_ALIAS") as String?
                keyPassword = project.findProperty("GAMES_UPLOAD_KEY_PASSWORD") as String?
            }
        }
    }

    buildTypes {
        debug {
            // Emulateur : 10.0.2.2 = le PC de developpement ("php artisan serve --host=0.0.0.0")
            val apiUrl = (project.findProperty("apiBaseUrl") as String?) ?: "http://10.0.2.2:8000/api/v1/"
            buildConfigField("String", "API_BASE_URL", "\"$apiUrl\"")
        }
        release {
            buildConfigField("String", "API_BASE_URL", "\"https://games.gameandme.fr/api/v1/\"")
            signingConfigs.findByName("release")?.let { signingConfig = it }
            isMinifyEnabled = true
            proguardFiles(getDefaultProguardFile("proguard-android-optimize.txt"), "proguard-rules.pro")
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
    buildFeatures {
        compose = true
        buildConfig = true
    }
}

kotlin {
    compilerOptions {
        jvmTarget.set(org.jetbrains.kotlin.gradle.dsl.JvmTarget.JVM_17)
    }
}

dependencies {
    implementation(libs.androidx.core.ktx)
    implementation(libs.androidx.activity.compose)
    implementation(platform(libs.androidx.compose.bom))
    implementation(libs.androidx.compose.ui)
    implementation(libs.androidx.compose.ui.tooling.preview)
    implementation(libs.androidx.compose.material3)
    implementation(libs.androidx.compose.material.icons)
    implementation(libs.androidx.lifecycle.viewmodel.compose)
    implementation(libs.androidx.lifecycle.runtime.compose)
    implementation(libs.androidx.navigation.compose)
    implementation(libs.kotlinx.serialization.json)
    implementation(libs.kotlinx.coroutines.android)
    implementation(libs.retrofit)
    implementation(libs.retrofit.kotlinx.serialization)
    implementation(libs.okhttp)
    debugImplementation(libs.androidx.compose.ui.tooling)

    testImplementation(libs.junit)
}
