<?php

namespace App\Traits;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Keeps files on the public disk consistent with the database.
 *
 * Database transactions do not roll back filesystem changes, so every
 * operation follows the same order:
 *  1. New uploads are written first under generated, collision-free names.
 *  2. The database work runs inside a transaction.
 *  3. If anything fails, the files written in step 1 are removed and the
 *     previous files are left untouched.
 *  4. Files that the change made obsolete are deleted only after the
 *     transaction has committed.
 */
trait ManagesUploadedFiles
{
    /**
     * Directories on the public disk whose files this application manages.
     * Deletion is refused for any path outside of them.
     */
    private const MANAGED_FILE_DIRECTORIES = ['achievements', 'avatars', 'cv', 'projects'];

    /**
     * Store uploads with safe generated names, removing any already stored
     * file from this batch if a later one fails.
     *
     * @param  array<int, UploadedFile>  $files
     * @return list<string>
     */
    protected function storeUploadedFiles(array $files, string $directory): array
    {
        $storedPaths = [];

        try {
            foreach ($files as $file) {
                $storedPaths[] = $this->storeUploadedFile($file, $directory);
            }
        } catch (Throwable $exception) {
            $this->deleteManagedFiles($storedPaths);

            throw $exception;
        }

        return $storedPaths;
    }

    /**
     * Store a single upload under a sanitized, unique file name.
     */
    protected function storeUploadedFile(UploadedFile $file, string $directory): string
    {
        $path = $file->storeAs($directory, $this->safeFileName($file), 'public');

        if ($path === false) {
            throw new RuntimeException("Unable to store the uploaded file in [{$directory}].");
        }

        return $path;
    }

    /**
     * Run database work and reconcile the filesystem with its outcome.
     *
     * @template TResult
     *
     * @param  Closure(): TResult  $databaseWork
     * @param  list<string>  $newPaths  Files written for this change; removed if it fails.
     * @param  list<string>  $obsoletePaths  Files this change replaces; removed only after commit.
     * @return TResult
     */
    protected function commitFileChanges(Closure $databaseWork, array $newPaths = [], array $obsoletePaths = []): mixed
    {
        $obsoletePaths = array_values(array_diff(array_filter($obsoletePaths), $newPaths));

        try {
            return DB::transaction(function () use ($databaseWork, $obsoletePaths): mixed {
                $result = $databaseWork();

                DB::afterCommit(fn () => $this->deleteManagedFiles($obsoletePaths));

                return $result;
            });
        } catch (Throwable $exception) {
            $this->deleteManagedFiles($newPaths);

            throw $exception;
        }
    }

    /**
     * Delete files from the public disk, ignoring any path outside the
     * managed directories.
     *
     * @param  array<int, mixed>  $paths
     */
    protected function deleteManagedFiles(array $paths): void
    {
        $managedPaths = array_values(array_filter($paths, fn (mixed $path): bool => $this->isManagedFilePath($path)));

        if ($managedPaths !== []) {
            Storage::disk('public')->delete($managedPaths);
        }
    }

    /**
     * Determine whether a stored path points inside a managed directory.
     */
    protected function isManagedFilePath(mixed $path): bool
    {
        if (! is_string($path) || $path === '' || str_contains($path, '..') || str_contains($path, '\\')) {
            return false;
        }

        $segments = explode('/', $path);

        return count($segments) === 2
            && in_array($segments[0], self::MANAGED_FILE_DIRECTORIES, true)
            && $segments[1] !== ''
            && ! str_starts_with($segments[1], '.');
    }

    /**
     * Build "<slugged-original-name>-<random>.<extension>".
     */
    private function safeFileName(UploadedFile $file): string
    {
        $baseName = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->slug()
            ->limit(60, '')
            ->trim('-')
            ->toString();

        $extension = strtolower((string) ($file->guessExtension() ?: $file->getClientOriginalExtension()));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'bin';

        $uniqueSuffix = Str::lower(Str::random(16));

        return ($baseName === '' ? $uniqueSuffix : "{$baseName}-{$uniqueSuffix}").'.'.$extension;
    }
}
