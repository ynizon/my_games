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
						<img src="images/favicon/apple-icon-60x60.png" />&nbsp;&nbsp;
						<?php echo __("messages.Games");?>
						<?php
						if ($user){
							if ($user->hasRole("Admin")){?>
								<a href='/games/create'><i class="fa fa-plus"></i></a>
						<?php
							}
						}
						?>
						&nbsp;&nbsp;&nbsp;
						<select onchange="window.location='/home?lang='+this.value+'&redirect=/'">
							<?php
							foreach (config("app.langs") as $code=>$langtmp){
								?>
								<option value="<?php echo $code;?>" <?php if ($lang == $code){echo "selected";}?> ><?php echo $langtmp;?></option>
								<?php
							}
							?>
						</select>
					</div>
					
					<br style="clear:both"/>
				</div>				
				
				
                <div class="panel-body">
					<ul>
					<?php
					foreach ($games as $game){
						if ($game->status == 1){
						?>
							<li><a href="/<?php echo strtolower(str_replace(" ","",str_replace("'","",$game->name)));?>/settings"><?php echo $game->name;?></a></li>
						<?php
						}
					}
					?>
					</ul>					
					
					<p>
					<?php echo __("messages.HelpMe");?> ynizon@gmail.com.<br/>
					<?php echo __("messages.Info");?>.<br/>
					<a href='/login'><?php echo __("messages.Connection");?></a>
					
						<ul class="share-buttons">
						  <li><a href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2F<?php echo config("app.url");?>&t=<?php echo config("app.name");?>" title="Share on Facebook" target="_blank"><img alt="Share on Facebook" src="images/social_flat_rounded_rects_svg/Facebook.svg" /></a></li>
						  <li><a href="https://twitter.com/intent/tweet?source=<?php echo config("app.url");?>&text=<?php echo config("app.name");?>:%20<?php echo config("app.url");?>&via=enpix" target="_blank" title="Tweet"><img alt="Tweet" src="images/social_flat_rounded_rects_svg/Twitter.svg" /></a></li>
						  <li><a href="https://plus.google.com/share?url=http%3A%2F%2F<?php echo config("app.url");?>" target="_blank" title="Share on Google+"><img alt="Share on Google+" src="images/social_flat_rounded_rects_svg/Google+.svg" /></a></li>
						  <li><a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2FURL&media=<?php echo config("app.url");?>/images/screenshot.png&description=<?php echo config("app.description");?>" target="_blank" title="Pin it"><img alt="Pin it" src="images/social_flat_rounded_rects_svg/Pinterest.svg" /></a></li>
						  <li><a href="mailto:?subject=<?php echo config("app.name");?>&body=DESC:%20http%3A%2F%2F<?php echo config("app.url");?>" target="_blank" title="Send email"><img alt="Send email" src="images/social_flat_rounded_rects_svg/Email.svg" /></a></li>
						</ul>
					</p>
					
					<script>
						// This works on all devices/browsers, and uses IndexedDBShim as a final fallback 
						var indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || window.shimIndexedDB;

						//Get cards for country in indexedDB
						var i=0;
						var arrAllCards = [];
						$.getJSON("/cards/getall?lang=<?php echo $lang;?>&game_id=0", function (data) {
							arrAllCards = data;
						
							//Delete old database
							var DBDeleteRequest = window.indexedDB.deleteDatabase("MyGames");

							DBDeleteRequest.onerror = function(event) {
							  console.log("Error deleting database");
							};
							 
							DBDeleteRequest.onsuccess = function(event) {
							  console.log("Deleting database success");
							};

							// Open (or create) the database
							var open = indexedDB.open("MyGames", 1);

							// Create the schema
							open.onupgradeneeded = function() {
								var db = open.result;
								<?php
								foreach ($games as $game){
									$sDB = strtolower(str_replace(" ","",str_replace("'","",$game->name)));
									?>
									var store<?php echo $game->id;?> = db.createObjectStore("<?php echo $sDB;?>", {keyPath: "id"});
									<?php
								}
								?>
								
							};

							open.onsuccess = function() {
								// Start a new transaction
								<?php $sDB ="timesup";?>
								var db = open.result;
								var tx = db.transaction( [<?php foreach ($games as $game){$sDB = strtolower(str_replace(" ","",str_replace("'","",$game->name)));echo '"'.$sDB.'",';}?>], "readwrite");
								<?php
								foreach ($games as $game){
									$sDB = strtolower(str_replace(" ","",str_replace("'","",$game->name)));
								?>
									
									var store<?php echo $game->id;?> = tx.objectStore("<?php echo $sDB;?>");
								<?php
								}
								?>
								putNext();
								
								function putNext() {
									if (i<arrAllCards.length) {
										switch (parseInt(arrAllCards[i].game_id)){
											<?php
											foreach ($games as $game){
												?>
												case <?php echo $game->id;?>:
												store<?php echo $game->id;?>.put(arrAllCards[i]).onsuccess = putNext;
												break;
												<?php
											}
											?>
										}
										
										++i;
									} else {   // complete
										console.log('Done. All cards are in indexedDb.');
									}
								}   
								
							
								// Close the db when the transaction is done
								tx.oncomplete = function() {
									db.close();
								};
								
							}
						});
						
					</script>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
