<header>
    <div class="header">
        @if (request()->is('lösenord/återställ'))
            <div class="header-left">
                <button class="main-button-design" onclick=location.href="{{ url('/logga-in') }}">
                    Tillbaks till inloggning
                </button>
            </div>
        @endif
        @if (request()->is('spegel/inställningar'))
            <div class="header-left">
                <button class="main-button-design" onclick=location.href="{{ url('/spegel') }}">
                    Tillbaks till spegeln
                </button>
            </div>
        @endif
        <div class="header-center">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo"/>
        </div>
        @if (request()->is('spegel'))
            <div class="header-right">
                <a href="{{ route('profile.settings') }}">
                    <img src="{{ asset('assets/icons/settings-icon.png') }}" id="settings-link" alt="settings Icon">
                </a>
            </div>
        @endif
    </div>
</header>
