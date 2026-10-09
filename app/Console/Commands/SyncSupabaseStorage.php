<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class SyncSupabaseStorage extends Command
{
    protected $signature = 'storage:sync-supabase {--dry-run : List image files without uploading them} {--overwrite : Replace objects that already exist in Supabase}';

    protected $description = 'Sync local public image files to Supabase Storage';

    public function handle(): int
    {
        $local = Storage::disk('public');

        try {
            $images = array_values(array_filter(
                $local->allFiles(),
                fn (string $path): bool => in_array(
                    strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                    ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                    true,
                ),
            ));
        } catch (Throwable $exception) {
            $this->error('Could not list local public storage: '.$exception->getMessage());

            return self::FAILURE;
        }

        if ($images === []) {
            $this->info('No supported image files found in storage/app/public.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d image file(s).', count($images)));

        if ($this->option('dry-run')) {
            foreach ($images as $path) {
                $this->line('Would sync: '.$path);
            }

            return self::SUCCESS;
        }

        $supabase = Storage::disk('supabase');
        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($images as $path) {
            try {
                if (! $this->option('overwrite') && $supabase->exists($path)) {
                    $this->line('Skipped existing object: '.$path);
                    $skipped++;
                    continue;
                }

                $stream = $local->readStream($path);
                if (! is_resource($stream)) {
                    throw new RuntimeException('Could not read local file stream.');
                }

                try {
                    $stored = $supabase->writeStream($path, $stream, ['visibility' => 'public']);
                } finally {
                    fclose($stream);
                }

                if (! $stored) {
                    throw new RuntimeException('Supabase did not store the file.');
                }

                $this->info('Synced: '.$path);
                $uploaded++;
            } catch (Throwable $exception) {
                $this->error(sprintf('Failed: %s (%s)', $path, $exception->getMessage()));
                $failed++;
            }
        }

        $this->line(sprintf('Finished: %d uploaded, %d skipped, %d failed.', $uploaded, $skipped, $failed));

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}