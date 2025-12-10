<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Valuestore\Valuestore;

if (! function_exists('valuestore')) {
    /**
     * Retrieve a Valuestore instance stored inside storage/app/valuestore/.
     *
     * This helper ensures that the target directory exists and returns
     * a Valuestore object pointing to a JSON file located at:
     *
     *   storage/app/valuestore/{folder}/valuestore.json
     *
     * If no path (folder name) is provided, the default folder "system" is used.
     *
     * Example:
     *  $store = valuestore('settings');
     *  $store->put('app.name', 'My App');
     *  echo $store->get('app.name');
     *
     * @param  string|null  $path  Optional subfolder name inside valuestore/.
     */
    function valuestore(?string $path = null): Valuestore
    {
        $storage = Storage::disk('local');
        $folder = $path ?? 'system';
        $baseDir = implode('/', ['valuestore', $folder]);
        $fileName = 'valuestore.json';

        // Directory relative to storage/app/
        if (! $storage->exists($baseDir)) {
            $storage->makeDirectory($baseDir);
        }

        // Absolute path required by Valuestore
        $absoluteFilePath = implode('/', [storage_path("app/$baseDir"), $fileName]);

        return Valuestore::make($absoluteFilePath);
    }
}

if (! function_exists('state')) {
    function state(): Valuestore
    {
        $key = auth('web')->user()->email ?? 'Guest';
        $path = implode('/', ['state', $key]);

        return valuestore($path);
    }
}

if (! function_exists('setState')) {
    function setState(string $key, mixed $value): void
    {
        state()->put($key, $value);
    }
}

if (! function_exists('getState')) {
    function getState(string $key, mixed $default = null): mixed
    {
        $value = state()->get($key, $default);

        return $value ? $value : $default;
    }
}

if (! function_exists('removeState')) {
    function removeState(string $key): void
    {
        state()->forget($key);
    }
}

if (! function_exists('clearState')) {
    function clearState(): void
    {
        state()->flush();
    }
}
