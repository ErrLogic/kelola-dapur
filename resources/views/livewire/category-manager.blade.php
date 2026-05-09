<div class="flex-1 flex flex-col bg-stone-50">
    <header class="sticky top-0 z-40 border-b border-stone-200/70 bg-stone-50/85 backdrop-blur-xl"
            style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="mx-auto flex max-w-2xl items-center justify-between px-4 py-3">
            <div>
                <a href="{{ route('recipes.index') }}" wire:navigate
                   class="mb-1 inline-flex items-center gap-1.5 text-xs font-medium text-stone-500 active:text-stone-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    Kembali ke resep
                </a>
                <h1 class="text-lg font-semibold tracking-tight text-stone-900">Kategori Resep</h1>
                <p class="text-xs text-stone-400">Kelola chip kategori untuk membantu pencarian dan pengelompokan resep.</p>
            </div>
            <div class="rounded-2xl bg-white px-3 py-2 text-center shadow-sm shadow-stone-200/70">
                <p class="text-[11px] uppercase tracking-[0.18em] text-stone-400">Total</p>
                <p class="text-sm font-semibold text-stone-800">{{ $this->categories->count() }}</p>
            </div>
        </div>
    </header>

    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col px-4 pb-10 pt-4"
         style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 2rem);">
        <section class="panel-card space-y-4">
            <div>
                <p class="eyebrow">Tambah kategori</p>
                <h2 class="mt-1 text-base font-semibold text-stone-900">Nama kategori baru</h2>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="newCategoryName" placeholder="Contoh: Panggang"
                           class="field-input @error('newCategoryName') field-input-error @enderror">
                    @error('newCategoryName')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center gap-2 rounded-2xl border border-stone-200 bg-white px-3">
                    <input type="color" wire:model.live="newCategoryColor" class="h-8 w-8 cursor-pointer rounded-lg border-0 bg-transparent p-0">
                    <input type="text" wire:model.live.debounce.250ms="newCategoryColor" class="w-24 border-0 bg-transparent text-xs font-medium uppercase text-stone-600 outline-none">
                </div>
                <button type="button" wire:click="addCategory"
                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-stone-900 px-5 text-sm font-medium text-white shadow-sm shadow-stone-900/10 transition active:scale-[0.98] active:bg-stone-950">
                    Tambah
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach($this->colorPresets as $preset)
                    <button type="button" wire:click="$set('newCategoryColor', '{{ $preset }}')"
                            class="h-7 w-7 rounded-full border-2 {{ strtoupper($newCategoryColor) === strtoupper($preset) ? 'border-stone-800' : 'border-white' }}"
                            style="background: {{ $preset }}"></button>
                @endforeach
            </div>
            @error('newCategoryColor')<p class="field-error">{{ $message }}</p>@enderror
        </section>

        <section class="mt-4 space-y-3">
            @foreach($this->categories as $category)
                <div wire:key="category-{{ $category->id }}" class="panel-card">
                    @if($editingId === $category->id)
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="eyebrow">Sedang diedit</p>
                                    <h3 class="mt-1 text-base font-semibold text-stone-900">{{ $category->name }}</h3>
                                </div>
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                    {{ $category->recipes_count }} resep
                                </span>
                            </div>

                            <div>
                                <input type="text" wire:model.live.debounce.200ms="editingName"
                                       class="field-input @error('editingName') field-input-error @enderror">
                                @error('editingName')<p class="field-error">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-center gap-2 rounded-2xl border border-stone-200 bg-white px-3 py-2">
                                <input type="color" wire:model.live="editingColor" class="h-8 w-8 cursor-pointer rounded-lg border-0 bg-transparent p-0">
                                <input type="text" wire:model.live.debounce.250ms="editingColor" class="w-24 border-0 bg-transparent text-xs font-medium uppercase text-stone-600 outline-none">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($this->colorPresets as $preset)
                                    <button type="button" wire:click="$set('editingColor', '{{ $preset }}')"
                                            class="h-7 w-7 rounded-full border-2 {{ strtoupper($editingColor) === strtoupper($preset) ? 'border-stone-800' : 'border-white' }}"
                                            style="background: {{ $preset }}"></button>
                                @endforeach
                            </div>
                            @error('editingColor')<p class="field-error">{{ $message }}</p>@enderror

                            <div class="flex gap-2">
                                <button type="button" wire:click="saveEditing"
                                        class="inline-flex h-11 flex-1 items-center justify-center rounded-2xl bg-stone-900 text-sm font-medium text-white transition active:scale-[0.98]">
                                    Simpan
                                </button>
                                <button type="button" wire:click="cancelEditing"
                                        class="inline-flex h-11 flex-1 items-center justify-center rounded-2xl bg-stone-100 text-sm font-medium text-stone-600 transition active:bg-stone-200">
                                    Batal
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                                          style="{{ $this->categoryPillStyle($category->color) }}">{{ $category->name }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold text-white"
                                          style="background: {{ $category->color ?? '#64748B' }};">
                                        {{ $category->recipes_count }} resep
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-stone-400">/{{ $category->slug }} · {{ strtoupper($category->color ?? '#64748B') }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="startEditing('{{ $category->id }}')"
                                        class="icon-btn text-stone-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="deleteCategory('{{ $category->id }}')"
                                        class="icon-btn text-red-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
    </div>
</div>

