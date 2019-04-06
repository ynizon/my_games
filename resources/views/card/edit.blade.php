@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading"><a href='/' ><img src="/images/favicon/favicon-32x32.png" /></a>&nbsp;&nbsp;Modification</div>
                <div class="panel-body">
				    
					{!! Form::model($card, ['route' => ['cards.update', $card->id], 'method' => 'put', 'onsubmit'=>'return setDescription()','class' => 'form-horizontal panel']) !!}
                        {{ csrf_field() }}

						<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nom</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{!! $card->name !!}" required autofocus />

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}" id="blocdescription">
                            <label for="description" class="col-md-4 control-label">Description</label>

                            <div class="col-md-6">
                                <textarea id="description" class="form-control inv" name="description" style="height:100px" >{!! $card->description !!}</textarea>

								<?php
								for ($k=1;$k<=12;$k++){
									?>
									<input type="text" placeHolder="Mot <?php echo $k;?>" class="form-control myword" name="description<?php echo $k;?>" id="description<?php echo $k;?>" value="">
									<?php
								}
								?>
								
								
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('lang') ? ' has-error' : '' }}">
                            <label for="lang" class="col-md-4 control-label">Langue</label>

                            <div class="col-md-6">
                                {!! Form::select('lang', config("app.langs"),$card->lang , ['id'=>"lang", 'class' => 'form-control']) !!}

                                @if ($errors->has('lang'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lang') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                            <label for="country" class="col-md-4 control-label">Pays</label>

                            <div class="col-md-6">
                                {!! Form::select('country', config("app.countries"),$card->country , ['id'=>"country", 'class' => 'form-control']) !!}
								
                                @if ($errors->has('country'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('game_id') ? ' has-error' : '' }}">
                            <label for="game_id" class="col-md-4 control-label">Jeux</label>

                            <div class="col-md-6">
                                {!! Form::select('game_id', $games,$card->game_id , ['onchange'=>'refreshDescription()','id'=>"game_id", 'class' => 'form-control']) !!}
								
                                @if ($errors->has('game_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('game_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="status" class="col-md-4 control-label">Statut</label>

                            <div class="col-md-6">
                                {!! Form::select('status', array("1"=>"Actif","0"=>"Inactif"),$card->status , ['id'=>"status", 'class' => 'form-control']) !!}
                            </div>
                        </div>
						
						
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Enregistrer
                                </button>

                            </div>
                        </div>
					{!! Form::close() !!}
					
					<script>
						refreshDescription();
					</script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
