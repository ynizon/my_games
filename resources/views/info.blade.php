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
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Informations</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class=" my-3">
                                    <h3>{{ __("messages.SupportMe")}}</h3>
                                    <p>{!! __("messages.SupportMe_explain")!!}</p>

                                    <h3>{{ __("messages.play_disconnected")}}</h3>
                                    <p>{{ __("messages.play_disconnected_explain")}}</p>

                                    <h3>{{ __("messages.Copyright")}}</h3>
                                    <p>
                                       {!!  __("messages.Copyright_explain")!!}
                                        <a href='https://github.com/ynizon/my_games'>https://github.com/ynizon/my_games</a>.
                                    </p>

                                    <h3><?php echo __("messages.GameRules");?></h3>
                                    <div  class="panel panel-group" >
                                        @foreach ($games as $game)
                                            @if ($game->status == 1)
                                                <h4>{{$game->name}}</h4>
                                                <p>
                                                    {{ __("messages.description_".strtolower($game->name))}}
                                                </p>
                                            @endif
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.guest></x-footers.guest>
        </div>
    </main>
@endsection
