@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading"><?php echo __("messages.Settings");?></div>

                <div class="panel-body">
					<form method="get" action="/loupgaroudethiercelieux" style="margin:auto;text-align:center;max-width:300px;">
						<?php echo __("messages.Players");?>
						<select class="form-control" name="nbplayers">
							<?php
							for ($i=8;$i<=24;$i++){
							?>
								<option value="<?php echo $i;?>"><?php echo $i;?></option>
							<?php
							}
							?>
						</select>
						<br/>
						<?php echo __("messages.Wolfs");?>
						<br/>
						<select class="form-control" name="nbwolfs">
							<?php
							for ($i=2;$i<=4;$i++){
							?>
								<option value="<?php echo $i;?>"><?php echo $i;?></option>
							<?php
							}
							?>
						</select>
						<br/>
						
						<table class="table table-striped">
							<tbody>
								<?php
								$k=0;
								$extensions = array();
								foreach ($cards as $card){
									$ext = json_decode($card->description,true)["word1"];
									$extensions[$ext] = $ext;
								}
								
								foreach ($extensions as $ext){
									?>
									<tr>
										<th  colspan="2"style="text-align:center">
											<?php
											echo $ext;
											?>
										</th>
									</tr>
									<?php
									foreach ($cards as $card){
										$extcard = json_decode($card->description,true)["word1"];
										if ($extcard == $ext){
											$k++;
											$name = str_replace(" ","_",strtolower($card->name));
											?>
											<tr>
												<td>
													<label for="card<?php echo $k;?>"><img src="/images/wolf_<?php echo $name;?>.png">
													</label>
												</td>
												<td style="text-align:left">
													<div onclick="$('#desccard<?php echo $card->id;?>').toggleClass('inv');">
														<?php
														if ($name == "villageois" or $name=="loup_garou"){
															?>
															<input class="inv" checked id="card<?php echo $k;?>" name="cards[]" type="checkbox" value="<?php echo $card->id;?>" />
															<?php
														}else{
															?>
															<input id="card<?php echo $k;?>" name="cards[]" type="checkbox" value="<?php echo $card->id;?>" />
															<?php
														}
														?>													
														<label onclick="$('#desccard<?php echo $card->id;?>').toggleClass('inv');" style="font-weight:unset" for="card<?php echo $k;?>"><?php echo $card->name;?></label>
													</div>
													<div class="inv" id="desccard<?php echo $card->id;?>">
														<?php echo __("messages.Loup ".$card->name);?>
													</div>
												</td>
											</tr>
										<?php
										}
									}									
								}
								?>								
							</tbody>
						</table>
						<input type="submit" value="<?php echo __("messages.Play");?> !" class="arrondiplay btn btn-primary	"/>
					</form>
                </div>				
            </div>
			
			<div id="footer">
				<a href='/'><?php echo __("messages.back_to_homepage");?></a>
			</div>
        </div>
    </div>
</div>
@endsection
