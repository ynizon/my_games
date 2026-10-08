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
                                <h6 class="text-white text-capitalize ps-3">{{__("messages.Games")}}</h6>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2 ps-3">
                            {!! Form::model($game, ['route' => ['games.update', $game->id], 'method' => 'put', 'onsubmit'=>'return setDescription()','class' => 'form-horizontal panel']) !!}
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Nom</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control border border-2 p-2" name="name" value="{!! $game->name !!}" required autofocus />

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
                                        <textarea id="description" type="text" class="form-control border border-2 p-2" name="description" style="height:100px" >{!! $game->description !!}</textarea>

                                        @if ($errors->has('description'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3 col-md-6 form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label for="status" class="col-md-4 control-label">Statut</label>

                                    <div class="col-md-6">
                                        {!! Form::select('status', array("1"=>"Actif","0"=>"Inactif"),$game->status , ['id'=>"status", 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn bg-gradient-dark">
                                Enregistrer
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
