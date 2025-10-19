<?php

namespace App\Services;

use PDO;
use App\Config\Config; // Добавляем, если класс не был импортирован

class TourDBStorage extends DBStorage implements ILoadStorage
{
    /**
     * Загружает данные обо всех турах из базы данных.
     * @param string $nameFile Не используется в DBStorage, но соответствует интерфейсу.
     * @return array|null
     */
    public function loadData($nameFile): ?array
    {
        // 🔥 ИСПРАВЛЕНО: Заменили 'category' на 'destination' и 'duration_days'
        $sql = "SELECT id, name, destination, description, image, price, duration_days, is_active FROM " . Config::TABLE_TourS;
        
        $result = $this->connection->query($sql, PDO::FETCH_ASSOC);
        
        // Добавим проверку на ошибку выполнения запроса
        if ($result === false) {
             error_log("SQL Error loading tours: " . print_r($this->connection->errorInfo(), true));
             return [];
        }

        $rows = $result->fetchAll();
        return $rows; 
    }
}
