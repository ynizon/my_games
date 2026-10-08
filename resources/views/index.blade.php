@extends('layouts.app')

@section('content')
    <main class="main-content  mt-0">
        <div class="page-header align-items-start min-vh-100 bgcustom">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">{{config('app.name')}}</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="input-group input-group-outline my-3">
                                    <div id="loader" >
                                        Loading...
                                    </div>
                                    <br/>
                                    <select class="form-control" id="mylang" onchange="window.location='/?lang='+this.value+'&redirect=/'">
                                        @foreach (config("app.langs") as $code=>$langtmp){
                                            <option value="{{$code}}" @if ($lang == $code) selected @endif >{{$langtmp}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class=" my-3">
                                    <div>
                                        {{__("messages.select-game")}} :
                                    </div>
                                    <div id="allgames">
                                        <ul>
                                            @foreach ($games as $game)
                                                @if ($game->status == 1)
                                                    <li><a href="/{{ strtolower(str_replace(" ","",str_replace("'","",$game->name)))}}/settings">{{ $game->name}}</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>

                                    <p>
                                        <?php echo __("messages.HelpMe");?> ynizon@gmail.com.<br/>
                                        <?php echo __("messages.Info");?>.<br/>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.guest></x-footers.guest>
        </div>
    </main>
    <script>
        // This works on all devices/browsers, and uses IndexedDBShim as a final fallback
        var indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || window.shimIndexedDB;

        //Get cards for country in indexedDB
        var i=0;
        var arrAllCards = [];

        //For the moment, there is only one language
        //echo $lang;

        //Permet de ne pas recharger sans cesse
        if (getCookie("getcard") == null || getCookie("nbcards") < {{Helper::countCards($lang)}}){
            $.getJSON("/cards/getall?lang="+$("#mylang").val()+"&game_id=0", function (data) {
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
                    @foreach ($games as $game)
                        var store{{ $game->id}} = db.createObjectStore("{{strtolower(str_replace(" ","",str_replace("'","",$game->name)))}}", {keyPath: "id"});
                    @endforeach
                };

                open.onsuccess = function() {
                    // Start a new transaction
                    var db = open.result;
                    var tx = db.transaction( [@foreach ($games as $game) "{{strtolower(str_replace(" ","",str_replace("'","",$game->name)))}}", @endforeach], "readwrite");
                    @foreach ($games as $game)
                        var store{{$game->id}} = tx.objectStore("{{strtolower(str_replace(" ","",str_replace("'","",$game->name)))}}");
                    @endforeach
                    putNext();

                    function putNext() {
                        if (i<arrAllCards.length) {
                            switch (parseInt(arrAllCards[i].game_id)){
                                @foreach ($games as $game)
                                    case {{$game->id}}:
                                        var res = store{{$game->id}}.put(arrAllCards[i]).onsuccess = putNext;
                                        res.onsuccess = function(event) {
                                            // report the success of our request
                                            console.log("update ok!!");
                                        };
                                        res.onerror = function(e){
                                            console.log("update failed!!");
                                        }
                                        break;
                                @endforeach
                            }

                            ++i;
                        } else {   // complete
                            console.log('Done. All cards are in indexedDb.');
                            setCookie("getcard","ok",90);
                            setCookie("nbcard",{{Helper::countCards($lang)}},90);
                        }
                    }

                    // Close the db when the transaction is done
                    tx.oncomplete = function() {
                        db.close();
                    };

                }
                $("#allgames").show();
                $("#loader").hide();
            });
        }else{
            $("#allgames").show();
            $("#loader").hide();
        }
    </script>
@endsection
