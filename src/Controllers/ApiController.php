<?php
namespace App\Controllers;

use App\Models\CityModel;

class ApiController
{
    private $cityModel;

    public function __construct()
    {
        $this->cityModel = new CityModel();
    }

    public function searchCities()
    {
        header('Content-Type: application/json');
        
        $query = $_GET['query'] ?? '';
        $limit = $_GET['limit'] ?? 10;
        
        if (empty($query)) {
            echo json_encode([]);
            exit();
        }

        $cities = $this->cityModel->searchCities($query, $limit);
        echo json_encode($cities);
        exit(); // Добавьте exit() чтобы прекратить выполнение
    }
}