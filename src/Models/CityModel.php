<?php
namespace App\Models;

use PDO;

class CityModel
{
    private $db;
    private $table = 'cities';

    public function __construct()
    {
        // Используем глобальное подключение или создаем новое
        global $pdo; // если у вас есть глобальная переменная $pdo
        
        if (isset($pdo) && $pdo instanceof PDO) {
            $this->db = $pdo;
        } else {
            // Создаем новое подключение
            $this->db = new PDO(
                'mysql:dbname=travel;host=localhost;charset=utf8',
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
    }

    public function searchCities($query, $limit = 10)
    {
        $sql = "SELECT id, name, country, iata_code 
                FROM {$this->table} 
                WHERE name LIKE :query 
                AND is_active = 1 
                ORDER BY 
                    CASE 
                        WHEN name = :exact_query THEN 1
                        WHEN name LIKE :start_query THEN 2
                        ELSE 3
                    END,
                    name
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        
        $searchQuery = "%{$query}%";
        $startQuery = "{$query}%";
        
        $stmt->bindValue(':query', $searchQuery);
        $stmt->bindValue(':exact_query', $query);
        $stmt->bindValue(':start_query', $startQuery);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActiveCities()
    {
        $sql = "SELECT id, name, country, iata_code 
                FROM {$this->table} 
                WHERE is_active = 1 
                ORDER BY name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}