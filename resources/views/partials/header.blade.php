<header>
    <div class="header">
        @if (request()->is('lösenord/återställ'))
            <div class="header-left">
                <a class="back-button" href="{{ route('login') }}">
                    <img src="{{ asset('assets/icons/back-icon.png') }}" />
                </a>
            </div>
        @endif
        @if (request()->is('spegel/inställningar'))
            <div class="header-left">
                <a class="back-button" href="{{ route('profile.index') }}">
                    <img src="{{ asset('assets/icons/back-icon.png') }}" />
                </a>
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
