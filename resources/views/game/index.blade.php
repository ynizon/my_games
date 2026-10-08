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

                        @if(session()->has('ok'))
                            <div class="container">
                                <br/>
                                <div class="alert alert-success alert-dismissible text-white" role="alert">
                                        <span class="text-sm">{!! session('ok') !!}</span>
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
                                    <span class="text-sm">{!! session('error') !!}</span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class=" me-3 my-3 text-end">
                            <a class="btn bg-gradient-dark mb-0" href="/games/create"><i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{__("messages.Add")}}</a>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            {{__("messages.Name")}}</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            {{__("messages.Cards")}}</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            {{__("messages.Status")}}</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($games as $game)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2">
                                                    <div>
                                                        <a href="/cards?game_id={{$game->id}}">
                                                            <img src="http://games.test/assets/img/small-logos/logo-asana.svg" class="avatar avatar-sm rounded-circle me-2">
                                                        </a>
                                                    </div>
                                                    <div class="my-auto">
                                                        <h6 class="mb-0 text-sm">{{$game->name}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{$game->cards()->count()}}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($game->status == 1)
                                                    <span class="badge badge-sm bg-gradient-success">{{__("messages.Online")}}</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">{{__("messages.Offline")}}</span>
                                                @endif
                                            </td>

                                            <td class="align-middle">
                                                <a rel="tooltip" class="btn btn-success btn-link" data-original-title="" title=""
                                                   href='/games/{!! $game->id !!}/edit'>
                                                    <i class="material-icons">edit</i>
                                                    <div class="ripple-container"></div>
                                                </a>
                                                <form class="trashform" action="{{route('games.destroy', $game->id)}}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-link" data-original-title="" title="">
                                                        <i class="material-icons">close</i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
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
            "order": [[0, 'asc']],
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
