<div class="flex-1 flex flex-col bg-stone-50" x-data="{ instructionsFocused: false }">
    <header class="sticky top-0 z-40 border-b border-stone-200/70 bg-stone-50/85 backdrop-blur-xl"
            style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-3">
            <div class="min-w-0">
                <a href="{{ $recipeId ? route('recipes.show', ['id' => $recipeId]) : route('recipes.index') }}" wire:navigate
                   class="mb-1 inline-flex items-center gap-1.5 text-xs font-medium text-stone-500 active:text-stone-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    Kembali
                </a>
                <h1 class="truncate text-lg font-semibold tracking-tight text-stone-900">
                    {{ $recipeId ? 'Edit Resep' : 'Buat Resep Baru' }}
                </h1>
                <p class="text-xs text-stone-400">
                    Susun resep rumahan yang rapi, ringan, dan mudah dipakai saat memasak.
                </p>
            </div>

            <button type="submit" form="recipe-form"
                    class="inline-flex h-11 items-center justify-center rounded-2xl bg-stone-900 px-4 text-sm font-medium text-white shadow-sm shadow-stone-900/10 transition active:scale-[0.98] active:bg-stone-950">
                Simpan
            </button>
        </div>
    </header>

    <form id="recipe-form" wire:submit="save" class="mx-auto flex w-full max-w-3xl flex-1 flex-col px-4 pb-28 pt-4"
          style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 7rem);">
        <div class="space-y-4">
            <section class="panel-card space-y-4"
                     x-data="{
                         openPhotoPicker() {
                             const input = this.$refs.photoUpload;

                             if (! input) {
                                 return;
                             }

                             input.value = null;
                             input.click();
                         }
                     }">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="eyebrow">Resep</p>
                        <h2 class="mt-1 text-base font-semibold text-stone-900">Informasi utama</h2>
                    </div>

                    <button type="button" wire:click="$toggle('is_favorite')"
                            class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-xs font-medium transition {{ $is_favorite ? 'bg-amber-100 text-amber-700' : 'bg-stone-100 text-stone-500' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                        </svg>
                        {{ $is_favorite ? 'Favorit' : 'Tandai favorit' }}
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label for="name" class="field-label">Nama resep</label>
                        <input id="name" wire:model.live.debounce.250ms="name" type="text" placeholder="Contoh: Ayam Kecap Keluarga"
                               class="field-input @error('name') field-input-error @enderror">
                        @error('name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="field-label">Deskripsi singkat</label>
                        <textarea id="description" wire:model.live.debounce.300ms="description" rows="3"
                                  placeholder="Tuliskan gambaran rasa, suasana, atau keunggulan resep ini."
                                  class="field-input min-h-24 resize-none @error('description') field-input-error @enderror"></textarea>
                        @error('description')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="panel-card space-y-4">
                <div>
                    <p class="eyebrow">Foto</p>
                    <h2 class="mt-1 text-base font-semibold text-stone-900">Gambar resep</h2>
                    <p class="mt-1 text-sm leading-relaxed text-stone-500">Unggah foto untuk tampilan kartu resep dan pengalaman baca yang lebih hangat.</p>
                </div>

                <div class="rounded-3xl border border-dashed border-stone-200 bg-stone-50/80">

                    {{-- Image / placeholder area — loading overlay scoped here so it centres in the box only --}}
                    <div class="relative">
                        <div wire:loading wire:target="photo"
                             class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 rounded-t-3xl bg-white/70 backdrop-blur-md">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-stone-400 animate-bounce [animation-delay:-0.3s]"></span>
                                <span class="h-2 w-2 rounded-full bg-stone-400 animate-bounce [animation-delay:-0.15s]"></span>
                                <span class="h-2 w-2 rounded-full bg-stone-400 animate-bounce"></span>
                            </div>
                            <p class="text-xs font-medium tracking-wide text-stone-500 uppercase">Mengunggah…</p>
                        </div>

                        @if($this->imagePreviewUrl())
                            <div class="aspect-[16/10] w-full overflow-hidden rounded-t-3xl bg-stone-100">
                                <img src="{{ $this->imagePreviewUrl() }}" alt="Preview resep" class="h-full w-full object-cover">
                            </div>
                        @else
                            <div class="flex min-h-56 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-sm shadow-stone-200/60">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 text-stone-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0L21.75 15.75m-10.5-6h.008v.008h-.008V9.75Zm.375-3h11.25a2.625 2.625 0 0 1 2.625 2.625v11.25a2.625 2.625 0 0 1-2.625 2.625H3.375A2.625 2.625 0 0 1 .75 20.625V9.375A2.625 2.625 0 0 1 3.375 6.75Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-stone-700">Belum ada gambar</p>
                                    <p class="mt-1 text-xs text-stone-400">Gunakan foto vertikal atau landscape ringan hingga 5MB.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2 border-t border-stone-200/80 bg-white px-4 py-4 rounded-b-3xl">
                        <input x-ref="photoUpload" id="photo-upload" wire:model="photo" type="file" accept="image/*" class="sr-only">
                        <button type="button"
                                @click="$refs.photoUpload.value = null; $refs.photoUpload.click()"
                                class="inline-flex cursor-pointer items-center justify-center rounded-2xl bg-stone-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-stone-900/10 transition active:scale-[0.98]">
                            {{ $this->imagePreviewUrl() ? 'Ganti foto' : 'Pilih foto' }}
                        </button>

                        @if($this->imagePreviewUrl())
                            <button type="button" wire:click="removePhoto"
                                    class="inline-flex items-center justify-center rounded-2xl bg-stone-100 px-4 py-2.5 text-sm font-medium text-stone-600 transition active:bg-stone-200">
                                Hapus foto
                            </button>
                        @endif
                    </div>
                </div>
                @error('photo')<p class="field-error">{{ $message }}</p>@enderror
            </section>

            <section class="panel-card space-y-4">
                <div>
                    <p class="eyebrow">Kategori</p>
                    <h2 class="mt-1 text-base font-semibold text-stone-900">Kelompokkan resep</h2>
                    <p class="mt-1 text-sm leading-relaxed text-stone-500">Pilih beberapa kategori agar resep mudah dicari berdasarkan bahan, rasa, atau waktu makan.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($this->categories as $cat)
                        @php($selected = $this->isCategorySelected((string) $cat->id))
                        <button type="button" wire:click="toggleCategory('{{ (string) $cat->id }}')"
                                wire:key="form-category-{{ $cat->id }}"
                                class="inline-flex items-center gap-2 rounded-full border px-3.5 py-2 text-sm font-medium transition active:scale-[0.99]"
                                style="{{ $this->categoryPillStyle($cat->color, $selected) }}">
                            @if($selected)
                                <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-white/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-3 w-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </span>
                            @endif
                            <span>{{ $cat->name }}</span>
                            <span class="rounded-full px-1.5 py-0.5 text-[10px] font-semibold"
                                  style="{{ $this->categoryBadgeStyle($cat->color, $selected) }}">
                                {{ $cat->recipes_count }}
                            </span>
                        </button>
                    @endforeach
                </div>
                @error('selectedCategories.*')<p class="field-error">{{ $message }}</p>@enderror
            </section>

            <section class="panel-card space-y-4">
                <div>
                    <p class="eyebrow">Komposisi</p>
                    <h2 class="mt-1 text-base font-semibold text-stone-900">Bahan-bahan</h2>
                    <p class="mt-1 text-sm leading-relaxed text-stone-500">Bangun daftar bahan secara fleksibel dengan takaran, satuan, catatan, dan urutan memasak.</p>
                </div>

                @error('ingredientRows')<p class="field-error">{{ $message }}</p>@enderror

                <div class="space-y-3">
                    @foreach($ingredientRows as $index => $row)
                        <div wire:key="ingredient-row-{{ $index }}"
                             class="overflow-hidden rounded-3xl border border-stone-200/80 bg-stone-50/70">
                            <div class="flex items-center justify-between border-b border-stone-200/70 px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs font-semibold text-stone-500 shadow-sm shadow-stone-200/70">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-sm font-medium text-stone-700">Baris bahan</p>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button" wire:click="moveIngredientUp({{ $index }})"
                                            class="icon-btn" @disabled($index === 0)>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="moveIngredientDown({{ $index }})"
                                            class="icon-btn" @disabled($index === count($ingredientRows) - 1)>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="removeIngredientRow({{ $index }})"
                                            class="icon-btn text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-3 px-4 py-4">
                                <div>
                                    <label class="field-label">Bahan</label>
                                    <button type="button" wire:click="openIngredientSearch({{ $index }})"
                                            class="flex min-h-12 w-full items-center justify-between rounded-2xl border border-stone-200 bg-white px-4 py-3 text-left text-sm text-stone-700 shadow-sm shadow-stone-100/70 transition active:scale-[0.99]">
                                        <span class="truncate {{ filled($row['ingredient_name']) ? 'text-stone-800' : 'text-stone-400' }}">
                                            {{ $row['ingredient_name'] ?: 'Pilih atau cari bahan' }}
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 shrink-0 text-stone-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                    @error('ingredientRows.' . $index . '.ingredient_id')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div class="grid grid-cols-[1fr,1fr] gap-3">
                                    <div>
                                        <label class="field-label">Jumlah</label>
                                        <input type="text" inputmode="decimal" wire:model.live.debounce.300ms="ingredientRows.{{ $index }}.quantity"
                                               placeholder="0.5"
                                               class="field-input @error('ingredientRows.' . $index . '.quantity') field-input-error @enderror">
                                        @error('ingredientRows.' . $index . '.quantity')<p class="field-error">{{ $message }}</p>@enderror
                                    </div>

                                    <div>
                                        <label class="field-label">Satuan</label>
                                        <select wire:model.live="ingredientRows.{{ $index }}.unit_id"
                                                class="field-input @error('ingredientRows.' . $index . '.unit_id') field-input-error @enderror">
                                            <option value="">Tanpa satuan</option>
                                            @foreach($this->units as $group => $units)
                                                <optgroup label="{{ $group }}">
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        @error('ingredientRows.' . $index . '.unit_id')<p class="field-error">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="field-label">Catatan</label>
                                    <input type="text" wire:model.live.debounce.300ms="ingredientRows.{{ $index }}.notes"
                                           placeholder="Contoh: iris tipis, haluskan, secukupnya"
                                           class="field-input @error('ingredientRows.' . $index . '.notes') field-input-error @enderror">
                                    @error('ingredientRows.' . $index . '.notes')<p class="field-error">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" wire:click="addIngredientRow"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-dashed border-stone-300 bg-stone-50 py-3 text-sm font-medium text-stone-600 transition active:bg-stone-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah bahan
                </button>
            </section>

            <section class="panel-card space-y-4">
                <div>
                    <p class="eyebrow">Instruksi</p>
                    <h2 class="mt-1 text-base font-semibold text-stone-900">Langkah memasak</h2>
                    <p class="mt-1 text-sm leading-relaxed text-stone-500">Tulis langkah secara ringkas dan nyaman dibaca di ponsel. Baris baru akan tetap terjaga.</p>
                </div>

                <div class="overflow-hidden rounded-3xl border border-stone-200 bg-white transition focus-within:border-emerald-300 focus-within:ring-4 focus-within:ring-emerald-100/70">
                    <div class="flex items-center justify-between border-b border-stone-100 px-4 py-3 text-xs text-stone-400">
                        <span>Gunakan format 1., 2., 3. agar mudah dibaca saat memasak.</span>
                        <span>{{ str($instructions)->length() }}</span>
                    </div>
                    <textarea wire:model.live.debounce.300ms="instructions" rows="10"
                              placeholder="1. Siapkan semua bahan.
2. Tumis bumbu hingga harum.
3. Masukkan bahan utama dan masak sampai matang."
                              class="min-h-56 w-full resize-none border-0 bg-transparent px-4 py-4 text-sm leading-7 text-stone-700 outline-none placeholder:text-stone-300"></textarea>
                </div>
                @error('instructions')<p class="field-error">{{ $message }}</p>@enderror
            </section>
        </div>
    </form>

    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200/70 bg-stone-50/90 px-4 pb-4 pt-3 backdrop-blur-xl"
         style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 1rem);">
        <div class="mx-auto flex max-w-3xl items-center gap-3">
            <a href="{{ $recipeId ? route('recipes.show', ['id' => $recipeId]) : route('recipes.index') }}" wire:navigate
               class="inline-flex h-12 flex-1 items-center justify-center rounded-2xl bg-white text-sm font-medium text-stone-600 shadow-sm shadow-stone-200/70 transition active:scale-[0.98]">
                Batal
            </a>
            <button type="submit" form="recipe-form"
                    class="inline-flex h-12 flex-[1.4] items-center justify-center rounded-2xl bg-stone-900 text-sm font-medium text-white shadow-lg shadow-stone-900/10 transition active:scale-[0.98] active:bg-stone-950">
                {{ $recipeId ? 'Simpan perubahan' : 'Buat resep' }}
            </button>
        </div>
    </div>

    @if($activeIngredientRow >= 0)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-black/30 p-0 backdrop-blur-sm sm:items-center sm:p-4">
            <div class="sheet-panel w-full max-w-lg rounded-t-[2rem] bg-white shadow-2xl sm:rounded-[2rem]"
                 style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 1rem);">
                <div class="flex items-center justify-between px-4 pb-2 pt-3">
                    <div>
                        <p class="eyebrow">Pilih bahan</p>
                        <h3 class="mt-1 text-base font-semibold text-stone-900">Cari atau buat bahan baru</h3>
                    </div>
                    <button type="button" wire:click="closeIngredientSearch" class="icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-4 pb-3">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.4a7.25 7.25 0 1 1-14.5 0 7.25 7.25 0 0 1 14.5 0Z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.200ms="ingredientSearch" autofocus placeholder="Cari bahan dapur"
                               class="field-input pl-10">
                    </div>
                </div>

                <div class="max-h-[55vh] overflow-y-auto px-4 pb-4">
                    @if(filled($ingredientSearch) && $this->filteredIngredients->doesntContain('name', $ingredientSearch))
                        <button type="button" wire:click="createAndSelectIngredient({{ $activeIngredientRow }})"
                                class="mb-3 flex w-full items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-left transition active:scale-[0.99]">
                            <div>
                                <p class="text-sm font-medium text-emerald-800">Buat bahan baru</p>
                                <p class="mt-1 text-xs text-emerald-600">{{ $ingredientSearch }}</p>
                            </div>
                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-emerald-700">Tambah</span>
                        </button>
                    @endif

                    <div class="space-y-2">
                        @forelse($this->filteredIngredients as $ingredient)
                            <button type="button" wire:click="selectIngredient({{ $activeIngredientRow }}, '{{ $ingredient->id }}', @js($ingredient->name))"
                                    class="flex w-full items-center justify-between rounded-2xl bg-stone-50 px-4 py-3 text-left transition active:scale-[0.99] active:bg-stone-100">
                                <div>
                                    <p class="text-sm font-medium text-stone-700">{{ $ingredient->name }}</p>
                                    @if($ingredient->normalized_name && $ingredient->normalized_name !== $ingredient->name)
                                        <p class="mt-1 text-xs text-stone-400">{{ $ingredient->normalized_name }}</p>
                                    @endif
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-stone-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 2.25 2.25 3.75-6" />
                                </svg>
                            </button>
                        @empty
                            <div class="rounded-2xl bg-stone-50 px-4 py-6 text-center text-sm text-stone-400">
                                Tidak ada bahan yang cocok.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

