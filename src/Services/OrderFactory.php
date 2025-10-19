<?php

namespace App\Services;

use App\Config\Config;
use App\Models\Booking;

class BookingFactory {

    public static function createBooking(): Booking {
        if (Config::STORAGE_TYPE == Config::TYPE_FILE) {
            $serviceStorage = new FileStorage();
            $BookingModel = new Booking($serviceStorage, Config::FILE_BookingS);
        }
        if (Config::STORAGE_TYPE == Config::TYPE_DB) {
            $serviceStorage = new BookingDBStorage();
            $BookingModel = new Booking($serviceStorage, Config::TABLE_BookingS);
        }
        return $BookingModel;
    }

}