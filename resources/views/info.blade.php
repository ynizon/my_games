@extends('layouts.app')

@section('content')
<?php
$user = Auth::user();
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">

				<div class="panel-heading" style="display:block">
					<div style="float:left">
						<a href='/' ><img src="/images/favicon/favicon-32x32.png" /></a>&nbsp;&nbsp;Informations
					</div>

					<br style="clear:both"/>
				</div>


                <div class="panel-body">
					<h2><?php echo __("messages.GameRules");?></h2>
					<div  class="panel panel-group" id="accordion" >
						<?php
						foreach ($games as $game){
							if ($game->status == 1){
							?>
								 <div class="panel panel-default">
									<div class="panel-heading" style="background:#3097d1;">
									  <h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $game->id;?>" style="color:#fff;">
										  <?php echo $game->name;?>
										</a>
									  </h4>
									</div>

									<div id="collapse<?php echo $game->id;?>"  class="panel-collapse collapse "><!-- add class in for opening-->
									  <div class="panel-body">
										<?php echo __("messages.description_".strtolower($game->name));?>
									  </div>
									</div>
								  </div>
							<?php
							}
						}
						?>
					</div>

					<h2><?php echo __("messages.play_disconnected");?></h2>
					<p><?php echo __("messages.play_disconnected_explain");?>

					</p>

					<h2><?php echo __("messages.SupportMe");?></h2>
					<p><?php echo __("messages.SupportMe_explain");?>
						<br/>
						<ul class="share-buttons">
						  <li><a href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2F<?php echo config("app.url");?>&t=<?php echo config("app.name");?>" title="Share on Facebook" target="_blank"><img alt="Share on Facebook" src="images/social_flat_rounded_rects_svg/Facebook.svg" /></a></li>
						  <li><a href="https://twitter.com/intent/tweet?source=<?php echo config("app.url");?>&text=<?php echo config("app.name");?>:%20<?php echo config("app.url");?>&via=enpix" target="_blank" title="Tweet"><img alt="Tweet" src="images/social_flat_rounded_rects_svg/Twitter.svg" /></a></li>
						  <li><a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2FURL&media=<?php echo config("app.url");?>/images/screenshot.png&description=<?php echo config("app.description");?>" target="_blank" title="Pin it"><img alt="Pin it" src="images/social_flat_rounded_rects_svg/Pinterest.svg" /></a></li>
						  <li><a href="mailto:?subject=<?php echo config("app.name");?>&body=DESC:%20http%3A%2F%2F<?php echo config("app.url");?>" target="_blank" title="Send email"><img alt="Send email" src="images/social_flat_rounded_rects_svg/Email.svg" /></a></li>
						</ul>
					</p>

					<h2><?php echo __("messages.Copyright");?></h2>
					<p>
						<?php echo __("messages.Copyright_explain");?>
					</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
