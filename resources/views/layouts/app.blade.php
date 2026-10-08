<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
@props(['bodyClass'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>
        {{config("app.name")}}
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets') }}/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
    <link id="pagestyle" href="{{ asset('assets') }}/css/styles2.css" rel="stylesheet" />

    <?php
    /*
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    */
    ?>
    <link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/brands.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/regular.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/solid.min.css') }}" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

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


    <!-- Only works in https -->
    @if (strpos(config("app.url"),"https") !== false)
        <script src="{{ asset('js/sw.js') }}" ></script>
    @endif











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
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '<?php echo config("app.google_analytics");?>', 'auto');
        ga('send', 'pageview');

    </script>

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>


</head>
<body class="bg-gray-200">

    <div class="container">
        @if(session()->has('ok'))
            <div class="alert alert-success alert-dismissible">{!! session('ok') !!}</div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger  alert-dismissible"><?php echo  session('error');?></div>
        @endif
    </div>

    @yield('content')

    <script src="{{ asset('assets') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('assets') }}/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('assets') }}/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="{{ asset('assets') }}/js/plugins/smooth-scrollbar.min.js"></script>
    @stack('js')
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

    </script>

    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets') }}/js/material-dashboard.min.js?v=3.0.0"></script>
</body>
</html>
