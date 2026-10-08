<footer class="footer position-absolute bottom-footer py-2 w-100 z-index-1">
    <div class="container">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-12 col-md-6 my-auto">
                <div class="copyright text-center text-sm text-white text-lg-start">
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
                    <a href="https://www.gameandme.fr" class="text-white" target="_blank">Blog</a>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo config("app.url");?>&t=<?php echo config("app.name");?>" title="Share on Facebook" target="_blank"><img alt="Share on Facebook" width="30" src="images/social_flat_rounded_rects_svg/Facebook.svg" /></a></li>
                    <li><a href="https://twitter.com/intent/tweet?source=<?php echo config("app.url");?>&text=<?php echo config("app.name");?>:%20<?php echo config("app.url");?>&via=enpix" target="_blank" title="Tweet"><img alt="Tweet" width="30" src="images/social_flat_rounded_rects_svg/Twitter.svg" /></a></li>
                    <li><a href="http://pinterest.com/pin/create/button/?url=<?php echo config("app.url");?>/images/screenshot.png&description=<?php echo config("app.description");?>" target="_blank" title="Pin it"><img width="30" alt="Pin it" src="images/social_flat_rounded_rects_svg/Pinterest.svg" /></a></li>
                    <li><a href="mailto:?subject=<?php echo config("app.name");?>&body=DESC:<?php echo config("app.url");?>" target="_blank" title="Send email"><img alt="Send email" src="images/social_flat_rounded_rects_svg/Email.svg" width="30" /></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
