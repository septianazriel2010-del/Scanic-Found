<?php

namespace App\Services;

use App\Models\ItemReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemReportService
{
    /**
     * Buat laporan baru + proses upload foto (jika ada).
     * Dipisah dari Controller supaya Controller cuma urusan HTTP request/response.
     */
    public function create(array $data, int $userId, ?UploadedFile $photo): ItemReport
    {
        if ($photo) {
            $data['photo_path'] = $photo->store('item-reports', config('filesystems.default'));
        }

        $data['user_id'] = $userId;
        $data['status'] = ItemReport::STATUS_OPEN;

        return ItemReport::create($data);
    }

    public function update(ItemReport $itemReport, array $data, ?UploadedFile $photo): ItemReport
    {
        if ($photo) {
            // Hapus foto lama supaya storage tidak menumpuk file yatim.
            if ($itemReport->photo_path) {
                Storage::disk(config('filesystems.default'))->delete($itemReport->photo_path);
            }
            $data['photo_path'] = $photo->store('item-reports', config('filesystems.default'));
        }

        $itemReport->update($data);

        return $itemReport->fresh();
    }

    public function delete(ItemReport $itemReport): void
    {
        if ($itemReport->photo_path) {
            Storage::disk(config('filesystems.default'))->delete($itemReport->photo_path);
        }

        $itemReport->delete();
    }
}
