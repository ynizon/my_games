@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading">Modification</div>
                <div class="panel-body">
				    
					{!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put','files'=>true,'class' => 'form-horizontal panel']) !!}
                        {{ csrf_field() }}

						<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nom</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{!! $user->name !!}" required autofocus />

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{!! $user->email !!}" required />

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<?php
						if (auth::user()->hasRole("Admin") or auth::user()->hasRole("User") or auth::user()->hasRole("Manager")){
						?>
							<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
								<label for="password" class="col-md-4 control-label">Password</label>

								<div class="col-md-6">
									<input id="password" type="text" class="form-control" name="password" value="" />

									@if ($errors->has('password'))
										<span class="help-block">
											<strong>{{ $errors->first('password') }}</strong>
										</span>
									@endif
								</div>
							</div>
						<?php
						}
						?>
						
						<div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                            <label for="role" class="col-md-4 control-label">Rôle du compte</label>

                            <div class="col-md-6">
								<?php
								$roles = config('app.users_roles');
								if (Auth::user()->hasRole("User")){
									unset($roles["Admin"]);
									unset($roles["Manager"]);
								}
								if (Auth::user()->hasRole("Manager")){
									unset($roles["Admin"]);
								}
								?>
                                {!! Form::select('role', $roles,$role , ['class' => 'form-control']) !!}
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="status" class="col-md-4 control-label">Statut</label>

                            <div class="col-md-6">
                                {!! Form::select('status', array("1"=>"Actif","0"=>"Inactif"),$user->status , ['onchange'=>'refreshAffectation()','id'=>"status", 'class' => 'form-control']) !!}
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
						//Affiche la case uniquement si status = 0
						function refreshAffectation(){
							if ($("#status").val() == 0){
								$("#bloc_remove_affectations").show();
							}else{
								$("#bloc_remove_affectations").hide();
							}
						}
						refreshAffectation();
					</script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
