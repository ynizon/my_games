@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading"><?php ;echo __("messages.Settings");?></div>

                <div class="panel-body">
					<form id="formgame" method="get" action="/loupgaroudethiercelieux" style="margin:auto;text-align:center;max-width:300px;">
						<?php echo __("messages.Players");?>
						<select class="form-control" name="nbplayers">
							<?php
							for ($i=8;$i<=24;$i++){
							?>
								<option value="{{$i}}">{{$i}}</option>
							<?php
							}
							?>
						</select>
						<br/>
						{{ __("messages.Wolfs")}}
						<br/>
						<select class="form-control" name="nbwolfs">
							@for ($i=2;$i<=4;$i++)
								<option value="{{$i}}">{{$i}}</option>
							@endfor
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
                                            {{$ext}}
										</th>
									</tr>
									<?php
									foreach ($cards as $card){
										$extcard = json_decode($card->description,true)["word1"];
										if ($extcard == $ext){
											$k++;
											$name = $card->name;
											?>
											<tr>
												<td>
													<label for="card{{$k}}"><img src="/images/wolf_{{Helper::slugify($name)}}.png">
													</label>
												</td>
												<td style="text-align:left">
													<div onclick="$('#desccard{{$card->id}}').toggleClass('inv');">
                                                        @if ($name == "Villageois" || $name=="Loup Garou")
															<input class="inv" checked id="card{{$k}}" name="cards[]" type="checkbox" value="{{$card->id}}" />
                                                        @else
															<input id="card{{$k}}" name="cards[]" type="checkbox" value="{{$card->id}}" />
                                                        @endif
														<label onclick="$('#desccard{{$card->id}}').toggleClass('inv');" style="font-weight:unset" for="card{{$k}}">{{$card->name}}</label>
													</div>
													<div class="inv" id="desccard{{$card->id}}">
														{{__("messages.Loup ".$card->name)}}
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
                        <br/>
                        <div id="arrondiplay" >
                            <div class="btn">
                                <i class="fa fa-play"  ></i>
                            </div>
                        </div>
					</form>
                </div>
            </div>

			<div id="footer">
				<a href='/'>{{ __("messages.back_to_homepage")}}</a>
			</div>
        </div>
    </div>
</div>
@endsection
