<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class OutfitImageStorage
{
    private const SUPABASE_PREFIX = 'supabase:';

    public function store(UploadedFile $file, int $userId): string
    {
        $path = "ootds/{$userId}/".str()->uuid().'.'.$file->guessExtension();

        if (config('ootd.image_storage') !== 'supabase') {
            return $file->storeAs("ootds/{$userId}", basename($path), 'public');
        }

        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        $this->supabaseRequest()
            ->withHeaders([
                'Content-Type' => $mimeType,
                'x-upsert' => 'false',
            ])
            ->withBody($file->getContent(), $mimeType)
            ->post($this->objectEndpoint($path))
            ->throw();

        return self::SUPABASE_PREFIX.$path;
    }

    public function delete(string $path): void
    {
        $this->deleteMany([$path]);
    }

    /** @param array<int, string> $paths */
    public function deleteMany(array $paths): void
    {
        $remotePaths = [];
        $localPaths = [];

        foreach ($paths as $path) {
            if (str_starts_with($path, self::SUPABASE_PREFIX)) {
                $remotePaths[] = substr($path, strlen(self::SUPABASE_PREFIX));

                continue;
            }

            $localPaths[] = $path;
        }

        if ($localPaths !== []) {
            Storage::disk('public')->delete($localPaths);
        }

        if ($remotePaths !== []) {
            $this->deleteSupabasePrefixes($remotePaths);
        }
    }

    public function url(string $path): string
    {
        if (! str_starts_with($path, self::SUPABASE_PREFIX)) {
            return asset('storage/'.$path);
        }

        $publicUrl = rtrim((string) config('services.supabase.url'), '/');
        $bucket = (string) config('services.supabase.storage_bucket');
        $relativePath = substr($path, strlen(self::SUPABASE_PREFIX));

        return $publicUrl.'/storage/v1/object/public/'.rawurlencode($bucket).'/'.$this->encodePath($relativePath);
    }

    /** @param array<int, string> $prefixes */
    private function deleteSupabasePrefixes(array $prefixes): void
    {
        try {
            $response = $this->supabaseRequest()->delete(
                $this->bucketEndpoint(),
                ['prefixes' => $prefixes],
            );

            if ($response->failed()) {
                Log::warning('Could not delete OOTD image(s) from Supabase Storage.', [
                    'status' => $response->status(),
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Could not delete OOTD image(s) from Supabase Storage.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function supabaseRequest(): PendingRequest
    {
        $url = trim((string) config('services.supabase.url'));
        $key = trim((string) config('services.supabase.storage_key'));

        if ($url === '' || $key === '') {
            throw new RuntimeException('Supabase Storage is enabled but SUPABASE_URL or SUPABASE_SERVICE_ROLE_KEY is missing.');
        }

        $headers = ['apikey' => $key];

        if (! str_starts_with($key, 'sb_secret_')) {
            $headers['Authorization'] = 'Bearer '.$key;
        }

        return Http::withHeaders($headers)->timeout(30);
    }

    private function objectEndpoint(string $path): string
    {
        return $this->bucketEndpoint().'/'.$this->encodePath($path);
    }

    private function bucketEndpoint(): string
    {
        $url = rtrim((string) config('services.supabase.url'), '/');
        $bucket = (string) config('services.supabase.storage_bucket');

        return $url.'/storage/v1/object/'.rawurlencode($bucket);
    }

    private function encodePath(string $path): string
    {
        return implode('/', array_map(rawurlencode(...), explode('/', $path)));
    }
}
