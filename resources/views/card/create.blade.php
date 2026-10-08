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
                                <h6 class="text-white text-capitalize ps-3">{{__("messages.Cards")}}</h6>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2 ps-3">
                            {!! Form::open(['url' => 'cards', 'method' => 'post', 'onsubmit'=>'return setDescription()', 'class' => 'form-horizontal panel']) !!}
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group{{ $errors->has('game_id') ? ' has-error' : '' }}">
                                    <label for="game_id" class="col-md-4 control-label">{{__("messages.Game")}}</label>

                                    <div class="col-md-6">
                                        {!! Form::select('game_id', $games, $game_id , ['onchange'=>'refreshDescription()','id'=>"game_id", 'class' => 'form-control']) !!}

                                        @if ($errors->has('game_id'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('game_id') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Nom</label>

                                    <div class="col-md-6">
                                        <input id="name" onkeydown="checkDouble(this)" type="text" class="form-control border border-2 p-2" name="name" value="" required autofocus />

                                        <ul id="warning-double">

                                        </ul>
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
                                        <textarea id="description" class="form-control border border-2 p-2 inv" name="description"  ></textarea>

                                        @for ($k=1;$k<=12;$k++)
                                            <div style="padding-left:10px;padding-bottom:5px;">
                                                <input type="text" placeHolder="Mot {{ $k}}" class="form-control border border-2 ps-3 p-2 myword" name="description{{ $k}}" id="description{{ $k}}" value="">
                                            </div>
                                        @endfor


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
                                        {!! Form::select('lang', config("app.langs"),"fr" , ['id'=>"lang", 'class' => 'form-control']) !!}

                                        @if ($errors->has('lang'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('lang') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                                    <label for="country" class="col-md-4 control-label">Difficulté</label>

                                    <div class="col-md-6">
                                        {!! Form::select('difficulty', [1=>"Easy",2=>"Medium",3=>"Hard"],2 , ['id'=>"difficulty", 'class' => 'form-control']) !!}

                                        @if ($errors->has('difficulty'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('difficulty') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                                    <label for="country" class="col-md-4 control-label">Pays</label>

                                    <div class="col-md-6">
                                        {!! Form::select('country', config("app.countries"), "fr" , ['id'=>"country", 'class' => 'form-control']) !!}

                                        @if ($errors->has('country'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('country') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3 col-md-6 form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label for="country" class="col-md-4 control-label">{{__("messages.Status")}}</label>

                                    <div class="col-md-6">
                                        {!! Form::select('status', array("1"=>"Actif","0"=>"Inactif"),1, ['id'=>"status", 'class' => 'form-control']) !!}
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

<script>
    let oldSearch = '';
    function checkDouble(oItem){
        var sValue = $(oItem).val();
        var sLang = $("#lang").val();
        if (sValue != "" && oldSearch != sValue) {
            oldSearch = sValue;
            $("#warning-double").html("");
            $.getJSON("/cards/checkdouble?name=" + sValue + "&lang=" + sLang + "&game_id=" + $("#game_id").val(), function (data) {
                var sList = "{{__("messages.Already in Database")}}:<br/>";
                var i = 0;
                while (i < data.length) {
                    sList += "<li>" + data[i].name + "</li>";
                    i++;
                }
                $("#warning-double").html(sList);
            });
        }
    }

    refreshDescription();
</script>
