<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="alternate" href="/?lang=fr_FR" hreflang="fr"/>
	<link rel="alternate" href="/?lang=en_EN" hreflang="en"/>

	<meta property="og:type" content="website">
	<meta property="og:url" content="<?php echo config("app.url");?>">
	<meta property="og:title" content="<?php echo config("app.name");?>">
	<meta property="og:image" content="<?php echo config("app.url");?>/images/screenshot.png">
	<meta property="og:description" content="<?php echo __("messages.AppDescription");?>">
	<meta name="description" content="<?php echo __("messages.AppDescription");?>" />
	<meta property="og:site_name" content="<?php echo config("app.name");?>">
	<meta property="og:locale" content="fr_FR">
	<!-- Next tags are optional but recommended -->
	<meta property="og:image:width" content="398">
	<meta property="og:image:height" content="585">

	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@enpix">
	<meta name="twitter:creator" content="@enpix">
	<meta name="twitter:url" content="<?php echo config("app.url");?>">
	<meta name="twitter:title" content="My Games">
	<meta name="twitter:description" content="Jouer à Time's up, Brainstorm, Loups Garous... sur votre mobile">
	<meta name="twitter:image" content="<?php echo config("app.url");?>/images/screenshot.png">

	<link rel="manifest" href="/manifest.json">
	<link rel="apple-touch-icon" sizes="57x57" href="/images/favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/images/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/images/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/images/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/images/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/images/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/images/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/images/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/images/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/images/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
	<link rel="manifest" href="/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/images/favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', '<?php echo config("app.google_analytics");?>', 'auto');
	  ga('send', 'pageview');

	</script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
	<link href="{{ asset('css/styles.css') }}" rel="stylesheet">
	<link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/brands.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/regular.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/solid.min.css') }}" rel="stylesheet">

	<link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">

	<!-- Appel de jQuery, bootstrap, et vue -->
	<script src="{{ asset('js/app.js') }}"></script>
	<?php
	/*
	<script src="{{ asset('js/jquery.min.js') }}" ></script>
	<script src="{{ asset('js/bootstrap.min.js') }}" ></script>
	*/
	?>

	<script src="{{ asset('js/jquery-ui.js') }}" ></script>
	<script src="{{ asset('js/jquery.ui.datepicker-fr.js') }}" ></script>
	<script src="{{ asset('js/jquery.dataTables.min.js') }}" ></script>
	<script src="{{ asset('js/utils.js') }}" ></script>
	<script src="{{ asset('js/prefixfree.min.js') }}" ></script>

	<?php
	//Only for https
	if (strpos(config("app.url"),"https") !== false){
		?>
		<script src="{{ asset('js/sw.js') }}" ></script>
		<?php
	}

	?>


    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
	<div id="PopupMask" ><img src="{{ asset('images/loader.gif') }}" id="loading-indicator" /></div>

    <div id="app">
		<?php
		if (Auth::user()){
		?>
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
					<a class="navbar-brand" href="{{ url('/') }}">
						<img src="/images/logo.png" height="30"/>
					</a>
                </div>

				<div style="float:left;padding:10px;padding-left:50px;font-weight:bold;font-size:24px;" id="app-title">

				</div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Connexion</a></li>
                            <?php
							/*
							<li><a href="{{ route('register') }}">S'enregistrer</a></li>
							*/
							?>
                        @else
                            <li class="dropdown" id="user_dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
									<li>
										<a href="/">
											Accueil
										</a>
									</li>
									<?php
									$user =  Auth::user();
									if ($user->can("user-edit")) {
									?>
										<li>
											<a href="/users">
												Utilisateurs
											</a>
										</li>
									<?php
									}
									if ($user->can("game-edit")) {
									?>
										<li>
											<a href="/games">
												Jeux
											</a>
										</li>

									<?php
									}
									if ($user->can("card-edit")) {
									?>
										<li>
											<a href="/cards">
												Cartes
											</a>
										</li>
									<?php
									}
									?>
									<li>
										<hr/>
									</li>
									<li>
                                       <a href="/profile">
                                            Mon profil
                                        </a>
									</li>
									<li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Déconnexion
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
		<?php
		}
		?>
		<div class="container">
			@if(session()->has('ok'))
				<div class="alert alert-success alert-dismissible">{!! session('ok') !!}</div>
			@endif

			@if(session()->has('error'))
				<div class="alert alert-danger  alert-dismissible"><?php echo  session('error');?></div>
			@endif
		</div>

		@yield('content')
    </div>

</body>
</html>
