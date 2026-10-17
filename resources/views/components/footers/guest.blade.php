<footer class="footer position-absolute bottom-footer py-2 w-100 z-index-1">
    <div class="container">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-12 my-auto">
                <div class="copyright text-center text-sm text-white">
                    © <script>
                        document.write(new Date().getFullYear())

                    </script>,
                    <a href="mailto:ynizon@gmail.com" class="font-weight-bold text-white" target="_blank">Yohann Nizon</a>
                    &nbsp;|&nbsp;
                    @auth
                        <a href="/dashboard" class="text-white">Admin</a>
                    @endauth

                    @guest
                        <a href="/sign-in" class="text-white">Connexion</a>
                    @endguest
                    &nbsp;|&nbsp;
                    <a href="/info" class="text-white">Informations</a>
                    &nbsp;|&nbsp;
                    <a href="/confidentialite" class="text-white">Confidentialité</a>
                    &nbsp;|&nbsp;
                    <a href="https://www.gameandme.fr" class="text-white" target="_blank">Blog</a>
                </div>
            </div>
        </div>
    </div>
</footer>
