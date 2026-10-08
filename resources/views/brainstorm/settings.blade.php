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
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Brainstorm</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formgame" method="get" role="form" class="text-start" action="/brainstorm">
                                    <div class="my-3">
                                        <label class="form-label">{{ __("messages.NbTeams")}}</label>
                                    </div>

                                    <div class="my-3">
                                        <select class="form-control" name="nbteams">
                                            @for ($i=2;$i<=4;$i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="my-3">
                                        <label class="form-label">{{ __("messages.Cards to find")}}</label>
                                    </div>

                                    <div class="my-3">
                                        <select class="form-control" name="nbcards">
                                            @for ($i=2;$i<=6;$i++)
                                                <option @if ($i==3) selected @endif value="{{ $i}}">{{ $i}}</option>
                                            @endfor
                                        </select>
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
