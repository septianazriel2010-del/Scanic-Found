<?php

namespace App\Services;

use App\Models\ItemReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ItemReportService
{
    /**
     * Buat laporan baru + proses upload foto (jika ada).
     * Dipisah dari Controller supaya Controller cuma urusan HTTP request/response.
     */
    public function create(array $data, int $userId, ?UploadedFile $photo): ItemReport
    {
        if ($photo) {
            $data['photo_path'] = $this->storePhoto($photo);
        }

        $data['user_id'] = $userId;
        $data['status'] = ItemReport::STATUS_OPEN;

        return ItemReport::create($data);
    }

    public function update(ItemReport $itemReport, array $data, ?UploadedFile $photo): ItemReport
    {
        if ($photo) {
            $data['photo_path'] = $this->storePhoto($photo);
        }

        $oldPhotoPath = $photo ? $itemReport->photo_path : null;
        $itemReport->update($data);

        if ($oldPhotoPath && $oldPhotoPath !== $itemReport->photo_path) {
            Storage::disk(config('filesystems.default'))->delete($oldPhotoPath);
        }

        return $itemReport->fresh();
    }

    private function storePhoto(UploadedFile $photo): string
    {
        $path = $photo->store('item-reports', config('filesystems.default'));

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'photo' => 'Foto gagal diunggah ke storage. Periksa konfigurasi storage lalu coba lagi.',
            ]);
        }

        return $path;
    }

    public function delete(ItemReport $itemReport): void
    {
        if ($itemReport->photo_path) {
            Storage::disk(config('filesystems.default'))->delete($itemReport->photo_path);
        }

        $itemReport->delete();
    }
}
