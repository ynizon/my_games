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
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Loup Garou</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formgame" method="get" action="/loupgaroudethiercelieux">
                                    <div class="my-3">
                                        <label class="form-label">{{ __("messages.Players")}}</label>
                                    </div>

                                    <div class="my-3">
                                        <select class="form-control" name="nbplayers">
                                            @for ($i=8;$i<=24;$i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="my-3">
                                        <label class="form-label">{{ __("messages.Wolfs")}}</label>
                                    </div>

                                    <div class="my-3">
                                        <select class="form-control" name="nbwolfs">
                                            @for ($i=2;$i<=4;$i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="my-3">
                                        <table class="table table-striped tablewolf">
                                            <tbody>
                                            <?php
                                            $k=0;
                                            $extensions = array();
                                            foreach ($cards as $card) {
                                                $ext = json_decode($card->description, true)["word1"];
                                                $extensions[$ext] = $ext;
                                            }

                                            foreach ($extensions as $ext) {
                                                ?>
                                            <tr>
                                                <th  colspan="2"style="text-align:center">
                                                    {{$ext}}
                                                </th>
                                            </tr>
                                                <?php
                                                foreach ($cards as $card) {
                                                    $extcard = json_decode($card->description, true)["word1"];
                                                    if ($extcard == $ext) {
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
                                    </div>

                                    <div class="mb-4">
                                        <button id="arrondiplay" class="btn btn-icon btn-3 btn-primary" type="button">
                                            <span class="btn-inner--icon"><i class="material-icons">play_arrow</i></span>
                                            <span class="btn-inner--text">{{__("messages.Play")}}</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div id="footer">
                                <a href='/'>{{ __("messages.back_to_homepage")}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
