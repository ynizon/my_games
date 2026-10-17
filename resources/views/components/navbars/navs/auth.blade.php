@props(['titlePage'])

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl admin-topbar" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/">{{ config("app.name") }}</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $titlePage }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">{{ $titlePage }}</h6>
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="d-none" id="logout-form">
            @csrf
        </form>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="admin-user d-none d-sm-inline">{{ Auth::user()->name }}</span>
            <button type="submit" form="logout-form" class="btn btn-sm admin-logout mb-0">
                <i class="material-icons text-sm">logout</i>
                <span class="d-none d-sm-inline">{{ __("messages.SignOut") }}</span>
            </button>
            <a href="javascript:;" class="nav-link text-body p-0 ps-2 d-xl-none" id="iconNavbarSidenav" aria-label="Menu">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                </div>
            </a>
        </div>
    </div>
</nav>
