<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">

        <a class="navbar-brand" href="{{ route('main') }}">
            <img src="{{{ asset('/images/logo.png') }}}" class="desktop-logo-img block-10-logo" alt="" />
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item {{ set_active_menu('main') }}">
                    <a class="nav-link" href="{{ route('main') }}">Главная</a>
                </li>

                @guest
                    <li class="nav-item {{ set_active_menu('login') }}">
                        <a class="nav-link" href="{{ route('login') }}">Вход</a>
                    </li>
                    <li class="nav-item {{ set_active_menu('register') }}">
                        <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Выход
                        </a>
                    </li>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                @endguest

            </ul>
        </div>
    </div>
</nav>
