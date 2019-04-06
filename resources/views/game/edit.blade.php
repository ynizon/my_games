@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading">Modification</div>
                <div class="panel-body">
				    
					{!! Form::model($game, ['route' => ['games.update', $game->id], 'method' => 'put', 'onsubmit'=>'return setDescription()','class' => 'form-horizontal panel']) !!}
                        {{ csrf_field() }}

						<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nom</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{!! $game->name !!}" required autofocus />

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
                                <textarea id="description" type="text" class="form-control" name="description" style="height:100px" >{!! $game->description !!}</textarea>

								@if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
												
						<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="status" class="col-md-4 control-label">Statut</label>

                            <div class="col-md-6">
                                {!! Form::select('status', array("1"=>"Actif","0"=>"Inactif"),$game->status , ['id'=>"status", 'class' => 'form-control']) !!}
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
