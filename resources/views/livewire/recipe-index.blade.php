<div class="flex-1 flex flex-col" x-data="{ showDeleteModal: false, deleteId: null, deleteName: '' }">
    {{-- Header --}}
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-stone-50/80 border-b border-stone-200/60"
            style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="max-w-2xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold tracking-tight text-stone-900">Kelola Dapur</h1>
                    <p class="text-xs text-stone-400 mt-0.5">{{ $this->recipes->total() }} resep</p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Category + New Recipe --}}
                    <a href="{{ route('categories.index') }}" wire:navigate
                       class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 text-amber-700 active:bg-amber-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                    </a>
                    <a href="{{ route('recipes.create') }}" wire:navigate
                       class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-600 text-white shadow-sm active:bg-amber-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>

                    {{-- Spacer --}}
                    <div class="w-3"></div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-500 text-white shadow-sm active:bg-red-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Search & Filters --}}
    <div class="sticky top-[calc(env(safe-area-inset-top,0px)+65px)] z-30 bg-stone-50/80 backdrop-blur-xl">
        <div class="max-w-2xl mx-auto px-4 pb-3 pt-2 space-y-3">
            {{-- Search bar --}}
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="w-4.5 h-4.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input wire:model.live.debounce.600ms="search"
                       type="text"
                       placeholder="Cari resep (min. 3 karakter)..."
                       class="w-full pl-10 pr-4 py-2.5 bg-stone-100/80 border-0 rounded-xl text-sm text-stone-700 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:bg-white transition-all">
                @if($search)
                    <button wire:click="setSearch('')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 active:text-stone-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            @if(strlen(trim($search)) > 0 && strlen(trim($search)) < 3)
                <p class="text-[11px] text-stone-400">Ketik minimal 3 karakter untuk mulai mencari resep.</p>
            @endif

            {{-- Filter chips --}}
            <div class="overflow-x-auto scrollbar-hide -mx-4">
                <div class="flex items-center gap-2 px-4 pb-0.5" style="width: max-content; min-width: 100%;">
                    <button wire:click="showAll"
                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-medium transition-all {{ $filter === '' && $category === '' ? 'bg-stone-800 text-white shadow-sm' : 'bg-stone-100 text-stone-500 active:bg-stone-200' }}">
                        Semua
                    </button>
                    <button wire:click="toggleFavoriteFilter"
                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-medium transition-all flex items-center gap-1 {{ $filter === 'favorite' ? 'bg-amber-600 text-white shadow-sm' : 'bg-stone-100 text-stone-500 active:bg-stone-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.765-2.033C3.735 12.418 2 10.157 2 7.5c0-2.09 1.612-3.75 3.604-3.75 1.083 0 2.118.481 2.896 1.293A4.188 4.188 0 0111.396 3.75c1.992 0 3.604 1.66 3.604 3.75 0 2.657-1.735 4.918-3.702 6.687a22.045 22.045 0 01-3.928 2.715l-.019.01-.005.003h-.002a.723.723 0 01-.69 0l-.002-.001z" />
                        </svg>
                        Favorit
                    </button>

                    <div class="w-px h-5 bg-stone-200 shrink-0"></div>

                    @foreach($this->categories as $cat)
                        <button wire:click="setCategoryFilter('{{ $cat->id }}')"
                                class="shrink-0 px-3.5 py-1.5 rounded-full border text-xs font-medium transition-all whitespace-nowrap"
                                style="{{ $this->categoryPillStyle($cat->color, $category === (string) $cat->id) }}">
                            {{ $cat->name }}
                        </button>
                    @endforeach

                    {{-- Right padding sentinel so last pill isn't flush against the edge --}}
                    <span class="shrink-0 w-4" aria-hidden="true"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recipe List --}}
    <div class="flex-1 max-w-2xl mx-auto w-full px-4 pt-2 pb-8"
         style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 2rem);">

        @if($this->recipes->total() === 0)
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-8 h-8 text-stone-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <p class="text-stone-400 text-sm">Belum ada resep</p>
                <a href="{{ route('recipes.create') }}" wire:navigate
                   class="mt-4 text-sm text-amber-600 font-medium">
                    Buat resep pertama →
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($this->recipes as $recipe)
                    <div wire:key="recipe-{{ $recipe->id }}"
                         class="group relative bg-white rounded-2xl shadow-sm shadow-stone-200/50 overflow-hidden active:scale-[0.98] transition-transform duration-150">
                        <a href="{{ route('recipes.show', ['id' => $recipe->id]) }}" wire:navigate class="flex items-stretch min-h-[6.75rem]">
                            {{-- Image --}}
                            @if($recipe->image_url)
                                <div class="w-22 shrink-0 bg-stone-100 self-stretch">
                                    <img src="{{ $recipe->image_url }}"
                                         alt="{{ $recipe->name }}"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </div>
                            @else
                                <div class="w-22 shrink-0 bg-gradient-to-br from-stone-100 to-stone-50 flex items-center justify-center self-stretch">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-7 h-7 text-stone-200">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Content --}}
                            <div class="flex-1 p-3.5 flex flex-col justify-center min-w-0">
                                <div class="flex items-start gap-2">
                                    <h3 class="text-sm font-semibold text-stone-800 truncate flex-1">{{ $recipe->name }}</h3>
                                    @if($recipe->is_favorite)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5">
                                            <path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.765-2.033C3.735 12.418 2 10.157 2 7.5c0-2.09 1.612-3.75 3.604-3.75 1.083 0 2.118.481 2.896 1.293A4.188 4.188 0 0111.396 3.75c1.992 0 3.604 1.66 3.604 3.75 0 2.657-1.735 4.918-3.702 6.687a22.045 22.045 0 01-3.928 2.715l-.019.01-.005.003h-.002a.723.723 0 01-.69 0l-.002-.001z" />
                                        </svg>
                                    @endif
                                </div>
                                @if($recipe->categories->isNotEmpty())
                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                        @foreach($recipe->categories->take(3) as $cat)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md border text-[10px] font-medium"
                                                  style="{{ $this->categoryPillStyle($cat->color, $category === (string) $cat->id) }}">
                                                <span>{{ $cat->name }}</span>
                                            </span>
                                        @endforeach
                                        @if($recipe->categories->count() > 3)
                                            <span class="text-[10px] text-stone-400">+{{ $recipe->categories->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                                @if($recipe->description)
                                    <p class="text-xs text-stone-400 mt-1 line-clamp-2 leading-relaxed">{{ $recipe->description }}</p>
                                @endif
                            </div>

                            {{-- Arrow --}}
                            <div class="flex items-center pr-3 text-stone-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </a>

                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                {{ $this->recipes->onEachSide(1)->links() }}
            </div>
        @endif
    </div>

    {{-- Delete confirmation modal --}}
    <div x-show="showDeleteModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
         style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 1rem);">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDeleteModal = false"></div>
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-stone-800 mb-1">Hapus Resep?</h3>
            <p class="text-sm text-stone-500 mb-5">
                <span x-text="deleteName"></span> akan dihapus permanen.
            </p>
            <div class="flex gap-3">
                <button @click="showDeleteModal = false"
                        class="flex-1 py-2.5 rounded-xl bg-stone-100 text-stone-600 text-sm font-medium active:bg-stone-200 transition-colors">
                    Batal
                </button>
                <button @click="$wire.deleteRecipe(deleteId); showDeleteModal = false"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-medium active:bg-red-600 transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

