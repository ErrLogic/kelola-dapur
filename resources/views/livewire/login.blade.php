<div class="flex min-h-dvh flex-col items-center justify-center px-6 py-12"
     style="padding-top: max(env(safe-area-inset-top, 0px), 3rem); padding-bottom: max(env(safe-area-inset-bottom, 0px), 3rem);">

    {{-- Logo / Brand --}}
    <div class="mb-8 flex flex-col items-center gap-3">
        <img src="{{ asset('icon.svg') }}" alt="Kelola Dapur" class="h-16 w-16 rounded-[1.25rem] shadow-lg shadow-stone-900/10">
        <div class="text-center">
            <h1 class="text-xl font-semibold tracking-tight text-stone-900">Kelola Dapur</h1>
            <p class="mt-1 text-sm text-stone-400">Masuk untuk melanjutkan</p>
        </div>
    </div>

    {{-- Login card --}}
    <div class="w-full max-w-sm">
        <div class="panel-card space-y-4">
            <form wire:submit="login" class="space-y-4">
                <div>
                    <label for="username" class="field-label">Username</label>
                    <input id="username"
                           wire:model="username"
                           type="text"
                           autocomplete="username"
                           autocapitalize="none"
                           autocorrect="off"
                           spellcheck="false"
                           placeholder="username"
                           class="field-input @error('username') field-input-error @enderror">
                    @error('username')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="field-label">Password</label>
                    <input id="password"
                           wire:model="password"
                           type="password"
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="field-input @error('password') field-input-error @enderror">
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="inline-flex h-12 w-full items-center justify-center rounded-2xl bg-stone-900 text-sm font-medium text-white shadow-sm shadow-stone-900/10 transition active:scale-[0.98] active:bg-stone-950">
                    <span wire:loading.remove wire:target="login">Masuk</span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <p class="mt-8 text-center text-xs text-stone-300">Kelola Dapur &copy; {{ date('Y') }}</p>
</div>
