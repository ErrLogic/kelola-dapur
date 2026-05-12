<div class="flex-1 flex flex-col"
     x-data="{ showDeleteModal: false, showActions: false }">

    {{-- Header --}}
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-stone-50/80 border-b border-stone-200/60"
            style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('recipes.index') }}" wire:navigate
               class="inline-flex items-center gap-1.5 text-stone-500 active:text-stone-700 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Kembali
            </a>
            <div class="flex items-center gap-2">
                <button wire:click="toggleFavorite"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors {{ $this->recipe->is_favorite ? 'bg-amber-50 text-amber-500' : 'bg-stone-100 text-stone-400' }} active:scale-95">
                    @if($this->recipe->is_favorite)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    @endif
                </button>
                <div class="relative">
                    <button @click="showActions = !showActions"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-stone-100 text-stone-500 active:bg-stone-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                    </button>
                    <div x-show="showActions" @click.outside="showActions = false" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-12 w-44 bg-white rounded-xl shadow-lg shadow-stone-200/50 border border-stone-100 overflow-hidden">
                        <a href="{{ route('recipes.edit', $this->recipe) }}" wire:navigate
                           class="flex items-center gap-2.5 px-4 py-3 text-sm text-stone-600 active:bg-stone-50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Edit Resep
                        </a>
                        <button @click="showDeleteModal = true; showActions = false"
                                class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-red-500 active:bg-red-50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            Hapus Resep
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <div class="flex-1 max-w-2xl mx-auto w-full"
         style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 2rem);">

        @php $recipe = $this->recipe; @endphp

        {{-- Hero Image --}}
        @if($recipe->image_url)
            <div class="aspect-[16/10] bg-stone-100 overflow-hidden">
                <img src="{{ $this->getImageUrl() }}"
                     alt="{{ $recipe->name }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            </div>
        @endif

        <div class="px-4 py-5 space-y-6">
            {{-- Title Section --}}
            <div>
                <h1 class="text-2xl font-bold text-stone-900 tracking-tight leading-tight">{{ $recipe->name }}</h1>
                @if($recipe->description)
                    <p class="mt-2 text-sm text-stone-500 leading-relaxed">{{ $recipe->description }}</p>
                @endif
                @if($recipe->categories->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @foreach($recipe->categories as $cat)
                            <span class="inline-block px-2.5 py-1 bg-stone-100 text-stone-600 rounded-lg text-xs font-medium">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Ingredients --}}
            @if($recipe->ingredients->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm shadow-stone-200/60 overflow-hidden">
                    <div class="px-4 pt-4 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-amber-50 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-amber-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-stone-800">Bahan-bahan</h2>
                            <span class="text-xs text-stone-400">{{ $recipe->ingredients->count() }} item</span>
                        </div>
                    </div>
                    <div class="divide-y divide-stone-50">
                        @foreach($recipe->ingredients as $ingredient)
                            <div class="px-4 py-3 flex items-start gap-3">
                                <div class="w-5 h-5 mt-0.5 rounded-full border-2 border-stone-200 shrink-0 flex items-center justify-center">
                                    <span class="text-[9px] text-stone-400 font-medium">{{ $loop->iteration }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-baseline gap-1.5 flex-wrap">
                                        <span class="text-sm font-medium text-stone-700">{{ $ingredient->name }}</span>
                                        @if($ingredient->pivot->quantity || $ingredient->pivot->unit_id)
                                            <span class="text-xs text-amber-700 font-medium">
                                                @if($ingredient->pivot->quantity){{ rtrim(rtrim(number_format($ingredient->pivot->quantity, 2), '0'), '.') }}@endif
                                                @if($ingredient->pivot->unit_id){{ optional($ingredient->pivot->unit)->name }}@endif
                                            </span>
                                        @endif
                                    </div>
                                    @if($ingredient->pivot->notes)
                                        <p class="text-xs text-stone-400 mt-0.5">{{ $ingredient->pivot->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Instructions --}}
            @if($recipe->instructions)
                <div class="bg-white rounded-2xl shadow-sm shadow-stone-200/60 overflow-hidden">
                    <div class="px-4 pt-4 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-emerald-50 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-emerald-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-stone-800">Langkah Memasak</h2>
                        </div>
                    </div>
                    <div class="px-4 pb-4">
                        @php
                            $steps = preg_split('/\n/', $recipe->instructions);
                        @endphp
                        <div class="space-y-3 mt-1">
                            @foreach($steps as $step)
                                @if(trim($step))
                                    @php
                                        $cleanStep = preg_replace('/^\d+[\.\)]\s*/', '', trim($step));
                                    @endphp
                                    <div class="flex gap-3">
                                        <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold flex items-center justify-center shrink-0 mt-0.5">
                                            {{ $loop->iteration }}
                                        </div>
                                        <p class="text-sm text-stone-600 leading-relaxed flex-1">{{ $cleanStep }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Cooking Session Tracker --}}
            @if($this->activeSession)
                <div x-data="cookingTimer({ initialSeconds: {{ now()->diffInSeconds($this->activeSession->started_at) }} })"
                     class="bg-amber-50 border border-amber-200/60 rounded-2xl p-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium text-amber-600 flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Sedang memasak...
                        </p>
                        <p x-text="formatted" class="text-2xl font-mono font-bold text-amber-700 tabular-nums mt-0.5"></p>
                    </div>
                    <button wire:click="finishCooking"
                            class="shrink-0 px-4 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-medium active:bg-amber-700 transition-colors shadow-sm">
                        Selesai
                    </button>
                </div>
            @else
                <button wire:click="startCooking"
                        class="w-full flex items-center justify-center gap-2 py-3 bg-amber-600 text-white rounded-xl text-sm font-medium active:bg-amber-700 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Mulai Memasak
                </button>
            @endif

            {{-- Last Cooked Date --}}
            <div class="flex items-center gap-1.5 text-xs text-stone-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                @if($this->lastCookedAt)
                    Terakhir dimasak: {{ $this->lastCookedAt->translatedFormat('d M Y') }}
                @else
                    Belum pernah dimasak
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('recipes.edit', $recipe) }}" wire:navigate
                   class="flex-1 flex items-center justify-center gap-2 py-3 bg-stone-800 text-white rounded-xl text-sm font-medium active:bg-stone-900 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Resep
                </a>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="showDeleteModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
         style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 1rem);">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDeleteModal = false"></div>
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
             class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-stone-800 mb-1">Hapus Resep?</h3>
            <p class="text-sm text-stone-500 mb-5">{{ $recipe->name }} akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button @click="showDeleteModal = false"
                        class="flex-1 py-2.5 rounded-xl bg-stone-100 text-stone-600 text-sm font-medium active:bg-stone-200 transition-colors">Batal</button>
                <button wire:click="deleteRecipe"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-medium active:bg-red-600 transition-colors">Hapus</button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('cookingTimer', (config) => ({
        seconds: config.initialSeconds,
        interval: null,

        init() {
            this.interval = setInterval(() => {
                this.seconds++;
            }, 1000);
        },

        stop() {
            clearInterval(this.interval);
            this.interval = null;
        },

        get formatted() {
            const h = Math.floor(this.seconds / 3600);
            const m = Math.floor((this.seconds % 3600) / 60);
            const s = this.seconds % 60;
            return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        }
    }));
</script>
@endscript