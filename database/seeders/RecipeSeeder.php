<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Unit;
use App\Models\RecipeCategory;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Get units
        $kg = Unit::where('code', 'kg')->first();
        $g = Unit::where('code', 'g')->first();
        $ml = Unit::where('code', 'ml')->first();
        $sdt = Unit::where('code', 'sdt')->first();
        $sdm = Unit::where('code', 'sdm')->first();
        $butir = Unit::where('code', 'butir')->first();
        $siung = Unit::where('code', 'siung')->first();
        $batang = Unit::where('code', 'batang')->first();
        $buah = Unit::where('code', 'buah')->first();
        $bungkus = Unit::where('code', 'bungkus')->first();
        $pcs = Unit::where('code', 'pcs')->first();
        $secukupnya = Unit::where('code', 'secukupnya')->first();

        // Helper to get or create ingredient
        $getIngredient = function ($name) {
            return Ingredient::firstOrCreate(
                ['name' => $name],
                ['normalized_name' => strtolower($name)]
            );
        };

        // Create ingredients
        $bawangMerah = $getIngredient('bawang merah');
        $bawangPutih = $getIngredient('bawang putih');
        $cabaiMerah = $getIngredient('cabai merah');
        $cabaiRawit = $getIngredient('cabai rawit');
        $kecapManis = $getIngredient('kecap manis');
        $kecapAsin = $getIngredient('kecap asin');
        $garam = $getIngredient('garam');
        $gulaPasir = $getIngredient('gula pasir');
        $merica = $getIngredient('merica');
        $minyakGoreng = $getIngredient('minyak goreng');
        $ayam = $getIngredient('ayam');
        $wortel = $getIngredient('wortel');
        $kentang = $getIngredient('kentang');
        $kol = $getIngredient('kol');
        $daunBawang = $getIngredient('daun bawang');
        $seledri = $getIngredient('seledri');
        $telur = $getIngredient('telur');
        $tomat = $getIngredient('tomat');
        $tempe = $getIngredient('tempe');
        $pisang = $getIngredient('pisang');
        $tepungTerigu = $getIngredient('tepung terigu');
        $air = $getIngredient('air');
        $kalduAyam = $getIngredient('kaldu ayam');

        // Get categories
        $ayamCat = RecipeCategory::where('slug', 'ayam')->first();
        $rumahan = RecipeCategory::where('slug', 'rumahan')->first();
        $manis = RecipeCategory::where('slug', 'manis')->first();
        $sayur = RecipeCategory::where('slug', 'sayur')->first();
        $sup = RecipeCategory::where('slug', 'sup')->first();
        $telurCat = RecipeCategory::where('slug', 'telur')->first();
        $pedas = RecipeCategory::where('slug', 'pedas')->first();
        $tempeCat = RecipeCategory::where('slug', 'tempe')->first();
        $cemilan = RecipeCategory::where('slug', 'cemilan')->first();
        $gurih = RecipeCategory::where('slug', 'gurih')->first();

        // Recipe 1: Ayam Kecap
        $recipe = Recipe::firstOrCreate(
            ['slug' => 'ayam-kecap'],
            [
                'name' => 'Ayam Kecap',
                'description' => 'Ayam kecap manis gurih dengan bumbu yang meresap sempurna. Masakan rumahan yang disukai semua keluarga.',
                'instructions' => "1. Potong ayam menjadi beberapa bagian, cuci bersih dan tiriskan.\n2. Haluskan bawang merah, bawang putih, dan merica.\n3. Panaskan minyak, tumis bumbu halus hingga harum.\n4. Masukkan ayam, aduk hingga berubah warna.\n5. Tambahkan kecap manis, garam, dan gula pasir. Aduk rata.\n6. Tuang sedikit air, masak dengan api kecil hingga ayam empuk dan bumbu meresap.\n7. Masak hingga kuah mengental, lalu angkat dan sajikan.",
                'is_favorite' => true,
                'image_url' => 'https://dummyimage.com/500x500/000/fff.jpg',
            ]
        );

        if ($recipe->ingredients()->count() === 0) {
            $recipe->ingredients()->attach([
                $ayam->id => ['quantity' => 0.5, 'unit_id' => $kg->id, 'notes' => 'potong menjadi beberapa bagian', 'sort_order' => 1],
                $bawangMerah->id => ['quantity' => 5, 'unit_id' => $siung->id, 'notes' => 'haluskan', 'sort_order' => 2],
                $bawangPutih->id => ['quantity' => 3, 'unit_id' => $siung->id, 'notes' => 'haluskan', 'sort_order' => 3],
                $kecapManis->id => ['quantity' => 100, 'unit_id' => $ml->id, 'notes' => null, 'sort_order' => 4],
                $garam->id => ['quantity' => 1, 'unit_id' => $sdt->id, 'notes' => null, 'sort_order' => 5],
                $gulaPasir->id => ['quantity' => 1, 'unit_id' => $sdt->id, 'notes' => 'sesuai selera', 'sort_order' => 6],
                $merica->id => ['quantity' => 0.5, 'unit_id' => $sdt->id, 'notes' => 'bubuk', 'sort_order' => 7],
                $minyakGoreng->id => ['quantity' => 3, 'unit_id' => $sdm->id, 'notes' => 'untuk menumis', 'sort_order' => 8],
                $air->id => ['quantity' => 100, 'unit_id' => $ml->id, 'notes' => 'sesuai kebutuhan', 'sort_order' => 9],
            ]);
        }
        $recipe->categories()->sync([$ayamCat->id, $rumahan->id, $manis->id]);

        // Recipe 2: Sayur Sop
        $recipe = Recipe::firstOrCreate(
            ['slug' => 'sayur-sop'],
            [
                'name' => 'Sayur Sop',
                'description' => 'Sup sayuran segar dengan kuah bening yang hangat dan bergizi. Cocok disantap saat cuaca dingin.',
                'instructions' => "1. Potong wortel, kentang, dan kol sesuai ukuran.\n2. Panaskan air dalam panci, masak hingga mendidih.\n3. Masukkan wortel dan kentang terlebih dahulu karena lebih lama matangnya.\n4. Setelah setengah matang, masukkan kol, daun bawang, dan seledri.\n5. Tambahkan garam, merica, dan kaldu ayam. Aduk rata.\n6. Masak hingga semua sayuran matang tapi tidak lembek.\n7. Koreksi rasa, angkat dan sajikan hangat.",
                'is_favorite' => false,
                'image_url' => 'https://dummyimage.com/500x500/000/fff.jpg',
            ]
        );

        if ($recipe->ingredients()->count() === 0) {
            $recipe->ingredients()->attach([
                $wortel->id => ['quantity' => 2, 'unit_id' => $buah->id, 'notes' => 'potong bulat', 'sort_order' => 1],
                $kentang->id => ['quantity' => 2, 'unit_id' => $buah->id, 'notes' => 'potong dadu', 'sort_order' => 2],
                $kol->id => ['quantity' => 0.25, 'unit_id' => $kg->id, 'notes' => 'potong kasar', 'sort_order' => 3],
                $daunBawang->id => ['quantity' => 2, 'unit_id' => $batang->id, 'notes' => 'potong kecil', 'sort_order' => 4],
                $seledri->id => ['quantity' => 1, 'unit_id' => $batang->id, 'notes' => null, 'sort_order' => 5],
                $bawangMerah->id => ['quantity' => 3, 'unit_id' => $siung->id, 'notes' => 'iris tipis', 'sort_order' => 6],
                $garam->id => ['quantity' => 1, 'unit_id' => $sdt->id, 'notes' => null, 'sort_order' => 7],
                $merica->id => ['quantity' => 0.5, 'unit_id' => $sdt->id, 'notes' => 'bubuk', 'sort_order' => 8],
                $kalduAyam->id => ['quantity' => 1, 'unit_id' => $bungkus->id, 'notes' => null, 'sort_order' => 9],
                $air->id => ['quantity' => 750, 'unit_id' => $ml->id, 'notes' => 'untuk kuah', 'sort_order' => 10],
            ]);
        }
        $recipe->categories()->sync([$sayur->id, $sup->id, $rumahan->id]);

        // Recipe 3: Telur Balado
        $recipe = Recipe::firstOrCreate(
            ['slug' => 'telur-balado'],
            [
                'name' => 'Telur Balado',
                'description' => 'Telur ceplok dengan sambal balado merah yang pedas dan menggugah selera.',
                'instructions' => "1. Panaskan minyak dan goreng telur menjadi telur ceplok. Sisihkan.\n2. Haluskan cabai merah, cabai rawit, bawang merah, dan bawang putih.\n3. Panaskan sedikit minyak, tumis bumbu halus hingga harum.\n4. Tambahkan garam, gula pasir, dan tomat potong. Aduk rata.\n5. Masukkan telur ceplok ke dalam sambal, aduk hingga terselimuti bumbu.\n6. Masak sebentar hingga bumbu meresap. Angkat dan sajikan.",
                'is_favorite' => false,
                'image_url' => 'https://dummyimage.com/500x500/000/fff.jpg',
            ]
        );

        if ($recipe->ingredients()->count() === 0) {
            $recipe->ingredients()->attach([
                $telur->id => ['quantity' => 4, 'unit_id' => $butir->id, 'notes' => 'buat telur ceplok', 'sort_order' => 1],
                $cabaiMerah->id => ['quantity' => 5, 'unit_id' => $buah->id, 'notes' => 'haluskan', 'sort_order' => 2],
                $cabaiRawit->id => ['quantity' => 3, 'unit_id' => $buah->id, 'notes' => 'haluskan', 'sort_order' => 3],
                $bawangMerah->id => ['quantity' => 4, 'unit_id' => $siung->id, 'notes' => 'haluskan', 'sort_order' => 4],
                $bawangPutih->id => ['quantity' => 2, 'unit_id' => $siung->id, 'notes' => 'haluskan', 'sort_order' => 5],
                $tomat->id => ['quantity' => 1, 'unit_id' => $buah->id, 'notes' => 'potong kecil', 'sort_order' => 6],
                $garam->id => ['quantity' => 1, 'unit_id' => $sdt->id, 'notes' => null, 'sort_order' => 7],
                $gulaPasir->id => ['quantity' => 0.5, 'unit_id' => $sdt->id, 'notes' => null, 'sort_order' => 8],
                $minyakGoreng->id => ['quantity' => 4, 'unit_id' => $sdm->id, 'notes' => 'untuk menggoreng dan menumis', 'sort_order' => 9],
            ]);
        }
        $recipe->categories()->sync([$telurCat->id, $pedas->id]);

        // Recipe 4: Tempe Orek
        $recipe = Recipe::firstOrCreate(
            ['slug' => 'tempe-orek'],
            [
                'name' => 'Tempe Orek',
                'description' => 'Tempe potong kecil yang dimasak dengan kecap manis dan bumbu hingga berkaramel. Manis gurih khas Jawa.',
                'instructions' => "1. Potong tempe menjadi dadu kecil, lalu goreng setengah matang. Sisihkan.\n2. Iris bawang merah, bawang putih, dan cabai rawit.\n3. Panaskan minyak, tumis bawang merah dan bawang putih hingga harum.\n4. Masukkan cabai rawit dan daun bawang, aduk sebentar.\n5. Tambahkan kecap manis, kecap asin, garam, dan gula pasir. Aduk rata.\n6. Masukkan tempe goreng, aduk hingga tempe terselimuti bumbu kecap.\n7. Masak hingga bumbu berkaramel dan meresap. Angkat dan sajikan.",
                'is_favorite' => true,
                'image_url' => 'https://dummyimage.com/500x500/000/fff.jpg',
            ]
        );

        if ($recipe->ingredients()->count() === 0) {
            $recipe->ingredients()->attach([
                $tempe->id => ['quantity' => 1, 'unit_id' => $pcs->id, 'notes' => 'potong dadu kecil', 'sort_order' => 1],
                $bawangMerah->id => ['quantity' => 4, 'unit_id' => $siung->id, 'notes' => 'iris tipis', 'sort_order' => 2],
                $bawangPutih->id => ['quantity' => 2, 'unit_id' => $siung->id, 'notes' => 'iris tipis', 'sort_order' => 3],
                $cabaiRawit->id => ['quantity' => 3, 'unit_id' => $buah->id, 'notes' => 'iris serong', 'sort_order' => 4],
                $daunBawang->id => ['quantity' => 1, 'unit_id' => $batang->id, 'notes' => 'potong kecil', 'sort_order' => 5],
                $kecapManis->id => ['quantity' => 3, 'unit_id' => $sdm->id, 'notes' => null, 'sort_order' => 6],
                $kecapAsin->id => ['quantity' => 1, 'unit_id' => $sdm->id, 'notes' => null, 'sort_order' => 7],
                $garam->id => ['quantity' => 0.5, 'unit_id' => $sdt->id, 'notes' => null, 'sort_order' => 8],
                $gulaPasir->id => ['quantity' => 1, 'unit_id' => $sdt->id, 'notes' => null, 'sort_order' => 9],
                $minyakGoreng->id => ['quantity' => 5, 'unit_id' => $sdm->id, 'notes' => 'untuk menggoreng dan menumis', 'sort_order' => 10],
            ]);
        }
        $recipe->categories()->sync([$tempeCat->id, $rumahan->id, $manis->id, $gurih->id]);

        // Recipe 5: Pisang Goreng
        $recipe = Recipe::firstOrCreate(
            ['slug' => 'pisang-goreng'],
            [
                'name' => 'Pisang Goreng',
                'description' => 'Pisang kepok yang digoreng dengan balutan tepung renyah. Cemilan tradisional yang selalu diminati.',
                'instructions' => "1. Kupas pisang dan belah memanjang menjadi dua bagian.\n2. Campur tepung terigu, garam, dan air secukupnya hingga menjadi adonan kental.\n3. Celupkan pisang ke dalam adonan tepung hingga terbalut rata.\n4. Panaskan minyak goreng dengan api sedang.\n5. Goreng pisang balut tepung hingga kuning kecokelatan.\n6. Angkat, tiriskan minyaknya, dan sajikan selagi hangat.",
                'is_favorite' => false,
                'image_url' => 'https://dummyimage.com/500x500/000/fff.jpg',
            ]
        );

        if ($recipe->ingredients()->count() === 0) {
            $recipe->ingredients()->attach([
                $pisang->id => ['quantity' => 5, 'unit_id' => $buah->id, 'notes' => 'pisang kepok, kupas', 'sort_order' => 1],
                $tepungTerigu->id => ['quantity' => 100, 'unit_id' => $g->id, 'notes' => null, 'sort_order' => 2],
                $garam->id => ['quantity' => 0.5, 'unit_id' => $sdt->id, 'notes' => null, 'sort_order' => 3],
                $air->id => ['quantity' => 100, 'unit_id' => $ml->id, 'notes' => 'sesuai kebutuhan', 'sort_order' => 4],
                $minyakGoreng->id => ['quantity' => null, 'unit_id' => $secukupnya->id, 'notes' => 'untuk menggoreng', 'sort_order' => 5],
            ]);
        }
        $recipe->categories()->sync([$cemilan->id, $manis->id]);
    }
}
