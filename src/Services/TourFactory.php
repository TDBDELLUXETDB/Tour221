<?php

namespace App\Services;

use App\Config\Config;
use App\Models\Tour;

class TourFactory {

    public static function createTour():Tour {
        if (Config::STORAGE_TYPE == Config::TYPE_FILE) {
            $serviceStorage = new FileStorage();
            $model = new Tour($serviceStorage, Config::FILE_TourS);
        }
        if (Config::STORAGE_TYPE == Config::TYPE_DB) {
            $serviceStorage = new TourDBStorage();
            $model = new Tour($serviceStorage, Config::TABLE_TourS);
        }
        return $model;
    }

}