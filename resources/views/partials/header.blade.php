<header>
    <div class="header">
        @if (request()->is('password/reset') || request()->is('password/email'))
            <div class="header-left">
                <button class="main-button-design" onclick=location.href="{{ url('/login') }}">
                    Tillbaks till inloggning
                </button>
            </div>
        @endif
        <div class="header-center">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo"/>
        </div>
    </div>
</header>
