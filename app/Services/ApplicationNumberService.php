<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\DB;

class ApplicationNumberService
{
    /**
     * Generate a unique sequential application number.
     * Format: MUN-APP-XXXX where XXXX is a padded sequential integer.
     */
    public static function generate(): string
    {
        return DB::transaction(function () {
            // Lock the table for writing to prevent race conditions during concurrent applications
            $lastApp = Application::lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextSequence = 1;

            if ($lastApp && preg_match('/MUN-APP-(\d+)/', $lastApp->application_number, $matches)) {
                $nextSequence = ((int)$matches[1]) + 1;
            }

            return 'MUN-APP-' . str_pad((string)$nextSequence, 4, '0', STR_PAD_LEFT);
        });
    }
}
