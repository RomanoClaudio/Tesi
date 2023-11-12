<section>
    <div>

    </div>
    <header>
        <h2 class="text-lg font-medium text-stone-100">
            {{ __('Informazioni Profilo') }}
        </h2>

        <p class="mt-1 text-sm text-stone-300">
            {{ __("Aggiorna le informazioni del tuo account") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" class="text-stone-200" :value="__('Nome')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block bg-stone-300 border-stone-600 focus:border-stone-700 focus:ring-stone-700 shadow-md w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" class="text-stone-200" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block bg-stone-300 border-stone-600 focus:border-stone-700 focus:ring-stone-700 shadow-md w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Il tuo indirizzo email non è ancora stato verificato.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Clicca per inviare di nuovo la mail di verifica.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Una nuova mail di verifica è stata inviata.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Salva') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
