<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

trait LibraryTrait
{
    public function increaseLimitsForReporting(float $time = 600, string $memory = '512M'): void
    {
        @set_time_limit((int) $time);
        @ini_set('memory_limit', $memory);
    }

    public function logError(Throwable $exception): JsonResponse
    {
        $statusCode = $this->resolveHttpStatusCode($exception);
        $message = $exception->getMessage() !== '' ? $exception->getMessage() : 'Error interno del servidor';

        Log::error('SELGESTIUN exception captured', [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'class' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'user_id' => auth()->id(),
            'ip' => request()?->ip(),
            'sucursal_id' => session('sucursal_id'),
        ]);

        return response()->json([
            'ok' => false,
            'message' => $message,
            'error_code' => $statusCode,
        ], $statusCode);
    }

    public function saveFile(string $subfolder, ?UploadedFile $fileInfo = null, string $disk = 'public'): string
    {
        if ($fileInfo === null) {
            return '';
        }

        $folder = trim($subfolder, '/');
        $extension = $fileInfo->getClientOriginalExtension();
        $extension = $extension !== '' ? strtolower($extension) : ($fileInfo->guessExtension() ?: 'bin');
        $fileName = (string) Str::uuid() . '.' . $extension;

        Storage::disk($disk)->putFileAs($folder, $fileInfo, $fileName);

        return $folder . '/' . $fileName;
    }

    public function deleteFile(string $subfolder, string $fileName, string $disk = 'public'): void
    {
        $folder = trim($subfolder, '/');
        $path = trim($folder . '/' . ltrim($fileName, '/'), '/');

        Storage::disk($disk)->delete($path);
    }

    public function toAlphaNumeric(string $text): string
    {
        $asciiText = Str::ascii($text);
        $sanitized = preg_replace('/[^A-Za-z0-9\s]/', '', $asciiText) ?? '';

        return trim((string) preg_replace('/\s+/', ' ', $sanitized));
    }

    private function resolveHttpStatusCode(Throwable $exception): int
    {
        $code = $exception->getCode();

        if (is_int($code) && $code >= 400 && $code <= 599) {
            return $code;
        }

        return 500;
    }
}
