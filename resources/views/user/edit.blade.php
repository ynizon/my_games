<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage="games"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Games"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">{{__("messages.Users")}}</h6>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2 ps-3">
                            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put','files'=>true,'class' => 'form-horizontal panel']) !!}
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Nom</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control border border-2 p-2" name="name" value="{!! $user->name !!}" required autofocus />

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail (=login de connexion)</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control border border-2 p-2" name="email" value="{!! $user->email !!}" required />

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="text" class="form-control border border-2 p-2" name="password" value="" />

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3 col-md-6 form-group{{ $errors->has('role') ? ' has-error' : '' }}">
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
                            </div>

                            <div class="mb-3 col-md-6 form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="status" class="col-md-4 control-label">Statut</label>

                                <div class="col-md-6">
                                    {!! Form::select('status', array("1"=>"Actif","0"=>"Inactif"),$user->status , ['onchange'=>'refreshAffectation()','id'=>"status", 'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <button type="submit" class="btn bg-gradient-dark">
                                {{__("messages.Submit")}}
                            </button>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>


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
