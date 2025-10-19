<?php 

namespace App\Services;

use App\Config\Config;
use App\Models\User;

class UserFactory {

    public static function createUser():User {
        if (Config::STORAGE_TYPE == Config::TYPE_FILE) {
            $serviceStorage = new FileStorage();
            $model = new User($serviceStorage, Config::FILE_TourS);
        }
        if (Config::STORAGE_TYPE == Config::TYPE_DB) {
            $serviceStorage = new UserDBStorage();
            $model = new User($serviceStorage, Config::TABLE_TourS);
        }
        return $model;
    }

}