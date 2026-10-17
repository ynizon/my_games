<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage="cards"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth :titlePage="__('messages.Cards')"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3 mb-0">
                                    {{__("messages.Cards")}}
                                    @if (isset($games[$game_id]))
                                        <span class="opacity-8">· {{ $games[$game_id]->name }}</span>
                                    @endif
                                </h6>
                            </div>
                        </div>
                        {{-- Filtre par jeu : un bouton par jeu, avec son nombre de cartes --}}
                        @php($counts = collect($cards)->countBy('game_id'))
                        <nav class="card-filter px-3 pt-4" aria-label="{{ __("messages.FilterByGame") }}">
                            <div class="card-filter__label">
                                <i class="material-icons" aria-hidden="true">filter_list</i>
                                {{ __("messages.FilterByGame") }}
                            </div>
                            <div class="card-filter__chips">
                                <a href="/cards" class="filter-chip {{ $game_id == 0 ? 'active' : '' }}" @if ($game_id == 0) aria-current="true" @endif>
                                    {{ __("messages.AllGames") }}
                                    <span class="filter-chip__count">{{ count($cards) }}</span>
                                </a>
                                @foreach ($games as $game)
                                    <a href="/cards?game_id={{ $game->id }}" class="filter-chip {{ $game->id == $game_id ? 'active' : '' }}" @if ($game->id == $game_id) aria-current="true" @endif>
                                        <x-game-icon :name="$game->name" class="filter-chip__icon" />
                                        {{ $game->name }}
                                        <span class="filter-chip__count">{{ $counts[$game->id] ?? 0 }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </nav>

                        @if(session()->has('ok'))
                            <div class="container">
                                <br/>
                                <div class="alert alert-success alert-dismissible text-white" role="alert">
                                    <span class="text-sm">{{ session('ok') }}</span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        @if(session()->has('error'))
                            <div class="container">
                                <br/>
                                <div class="alert alert-primary alert-dismissible text-white" role="alert">
                                    <span class="text-sm">{{ session('error') }}</span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class=" me-3 my-3 text-end">
                            <a class="btn bg-gradient-dark mb-0" href="/cards/create?game_id={{$game_id}}"><i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{__("messages.Add")}}</a>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            {{__("messages.Game")}}</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            {{__("messages.Name")}}</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            {{__("messages.Status")}}</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($cards as $card)
                                        @if ($card->game_id==$game_id || $game_id==0)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2">
                                                        <div>
                                                            <x-game-icon :name="$card->game->name" class="me-2" />
                                                        </div>
                                                        <div class="my-auto">
                                                            <h6 class="mb-0 text-sm">{{$card->game->name}}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{$card->name}}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    @if ($card->status == 1)
                                                        <span class="badge badge-sm bg-gradient-success">{{__("messages.Online")}}</span>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-secondary">{{__("messages.Offline")}}</span>
                                                    @endif
                                                </td>

                                                <td class="align-middle">
                                                    <a rel="tooltip" class="btn btn-success btn-link" data-original-title="" title=""
                                                       href='/cards/{{ $card->id }}/edit'>
                                                        <i class="material-icons">edit</i>
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                    <form class="trashform" action="{{route('cards.destroy', $card->id)}}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-link" data-original-title="" title="">
                                                            <i class="material-icons">close</i>
                                                            <div class="ripple-container"></div>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>

</x-layout>

<script>
    //Ajoute le bloc de recherche sur le table
    $(document).ready(function() {
        $(".table").DataTable({
            "paging":   false,
            "info":   false,
            "ordering": true,
            "order": [[1, 'asc']],
            "language": {
                "url": "/js/datatables.french.lang.json"
            },
            "initComplete": function(settings, json) {
                //On pose le focus sur la barre de recherche
                $("input[type='search']").focus();
            }
        });
        window.setTimeout(function() {
            $('.dataTables_filter').addClass('ps-3');
        },200);
    });
</script>
