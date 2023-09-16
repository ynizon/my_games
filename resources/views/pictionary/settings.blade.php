@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading"><?php echo __("messages.Teams");?></div>

                <div class="panel-body">
					<form id="formgame" method="get" action="/pictionary" style="margin:auto;text-align:center;max-width:300px;">
						<select class="form-control" name="nbteams">
							<?php
							for ($i=2;$i<=4;$i++){
							?>
								<option value="<?php echo $i;?>"><?php echo $i;?></option>
							<?php
							}
							?>
						</select>
						<br/>
						<span style="font-size: 21px;"><?php echo __("messages.Set");?></span>
						<br/>
						<select class="form-control" name="nbsets">
							<?php
							for ($i=1;$i<=4;$i++){
							?>
								<option <?php if ($i==3){echo "selected";} ?> value="<?php echo $i;?>"><?php echo $i;?></option>
							<?php
							}
							?>
						</select>
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
				<a href='/'><?php echo __("messages.back_to_homepage");?></a>
			</div>
        </div>
    </div>
</div>
@endsection
