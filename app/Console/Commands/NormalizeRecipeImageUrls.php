<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Services\ImageUploadService;
use Illuminate\Console\Command;

class NormalizeRecipeImageUrls extends Command
{
    protected $signature = 'recipes:normalize-image-urls
                            {--dry-run : Show what would change without writing}';

    protected $description = 'Convert any full/presigned image URLs stored in recipes.image_url to clean object keys.';

    public function handle(ImageUploadService $service): int
    {
        $dry = (bool) $this->option('dry-run');
        $updated = 0;
        $skipped = 0;

        Recipe::query()
            ->whereNotNull('image_url')
            ->orderBy('id')
            ->chunkById(200, function ($recipes) use ($service, $dry, &$updated, &$skipped) {
                foreach ($recipes as $recipe) {
                    $current = (string) $recipe->image_url;

                    if (! str_starts_with($current, 'http')) {
                        $skipped++;
                        continue;
                    }

                    $reflection = new \ReflectionMethod($service, 'extractPath');
                    $reflection->setAccessible(true);
                    $cleanKey = $reflection->invoke($service, $current);

                    if ($cleanKey === '' || $cleanKey === $current) {
                        $this->warn("Could not normalise: {$recipe->id} -> {$current}");
                        $skipped++;
                        continue;
                    }

                    $this->line("{$recipe->id}\n  from: {$current}\n  to:   {$cleanKey}");

                    if (! $dry) {
                        $recipe->forceFill(['image_url' => $cleanKey])->saveQuietly();
                    }

                    $updated++;
                }
            });

        $verb = $dry ? 'Would update' : 'Updated';
        $this->info("{$verb} {$updated} row(s). Skipped {$skipped}.");

        return self::SUCCESS;
    }
}

