<?php
namespace App\Controllers;

use App\Models\Tour;
use App\Services\TourDBStorage;
use App\Views\TourTemplate;
use App\Config\Config;

class TourController {

    // Получение туров для сайта /Tours
    public function get(?int $id = null, array $filters = []): string {
        if (session_status() == PHP_SESSION_NONE) session_start();

        // Получаем параметры из GET запроса
        $getFilters = [
            'from_city' => $_GET['from_city'] ?? '',
            'to_city' => $_GET['to_city'] ?? '',
            'dates' => $_GET['dates'] ?? ''
        ];

        // Объединяем с переданными фильтрами (приоритет у GET параметров)
        $filters = array_merge($filters, $getFilters);

        // Отладка
        error_log("🎯 TourController - GET parameters: " . print_r($_GET, true));
        error_log("🎯 TourController - Final filters: " . print_r($filters, true));

        // Инициализация модели
        $model = $this->initModel();
        $data = $model->loadData();

        if (!is_array($data)) return "Ошибка загрузки данных.";

        // Логика корзины
        $basketCount = 0;
        if (isset($_SESSION['basket']) && is_array($_SESSION['basket'])) {
            foreach ($_SESSION['basket'] as $item) {
                $basketCount += (int)($item['count_item'] ?? $item['quantity'] ?? 1);
            }
        }

        // Если указан ID тура — возвращаем одну карточку
        if ($id !== null) {
            $record = $data[$id - 1] ?? null;
            return TourTemplate::getCardTemplate($record);
        }

        // Применяем фильтры поиска
        if (!empty(array_filter($filters))) {
            $data = $this->applyFilters($data, $filters);
            error_log("🎯 TourController - After filtering: " . count($data) . " tours");
        }

        // Передаём фильтры в шаблон для отображения
        return TourTemplate::getAllTemplate(array_values($data), $basketCount, $filters);
    }

    // API: /api/tours или /api/tours/{id}
    public function searchTours(): void {
        if (session_status() == PHP_SESSION_NONE) session_start();

        header('Content-Type: application/json');

        $fromCity = $_GET['from_city'] ?? null;
        $toCity = $_GET['to_city'] ?? null;
        $dates = $_GET['dates'] ?? null;
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        $model = $this->initModel();
        $data = $model->loadData();
        if (!is_array($data)) {
            echo json_encode(['error' => 'Ошибка загрузки данных']);
            return;
        }

        if ($id !== null) {
            $tour = $data[$id - 1] ?? null;
            echo json_encode($tour ?? ['error' => 'Тур не найден']);
            return;
        }

        $filtered = $this->applyFilters($data, [
            'from_city' => $fromCity,
            'to_city' => $toCity,
            'dates' => $dates
        ]);

        echo json_encode(array_values($filtered));
    }

    // Добавление в корзину
    public function addToBasket(): void {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['basket'])) $_SESSION['basket'] = [];

        $TourId = $_POST['id'] ?? null;
        if ($TourId) {
            $_SESSION['basket'][$TourId] = [
                'id' => $TourId,
                'quantity' => ($_SESSION['basket'][$TourId]['quantity'] ?? 0) + 1
            ];
        }

        header('Location: /Tours');
        exit;
    }

    // Инициализация модели
    private function initModel(): Tour {
        if (Config::STORAGE_TYPE === Config::TYPE_DB) {
            $serviceStorage = new TourDBStorage();
            return new Tour($serviceStorage, Config::TABLE_TourS);
        } else {
            $serviceStorage = new \App\Services\FileStorage(Config::FILE_TOURS);
            return new Tour($serviceStorage, '');
        }
    }

    // Применение фильтров
    private function applyFilters(array $data, array $filters): array {
        $fromCity = $filters['from_city'] ?? null;
        $toCity = $filters['to_city'] ?? null;
        $dates = $filters['dates'] ?? null;

        error_log("🔍 Applying filters - from_city: '{$fromCity}', to_city: '{$toCity}', dates: '{$dates}'");

        // Если нет фильтров - возвращаем все данные
        if (empty($fromCity) && empty($toCity) && empty($dates)) {
            return $data;
        }

        return array_filter($data, function($tour) use ($fromCity, $toCity, $dates) {
            $match = true;

            // Проверка города отправления - используем поле 'name'
            if ($fromCity && isset($tour['name'])) {
                $tourCity = $tour['name'];
                // Убираем страну из названия если есть
                if (strpos($tourCity, ' (') !== false) {
                    $tourCity = explode(' (', $tourCity)[0];
                }
                $match = $match && (stripos($tourCity, $fromCity) !== false);
                error_log("🔍 From city check: '{$tourCity}' vs '{$fromCity}' -> " . ($match ? 'MATCH' : 'NO MATCH'));
            }

            // Проверка города назначения - используем поле 'destination'
            if ($toCity && isset($tour['destination'])) {
                $tourDestination = $tour['destination'];
                // Убираем страну из названия если есть
                if (strpos($tourDestination, ' (') !== false) {
                    $tourDestination = explode(' (', $tourDestination)[0];
                }
                $match = $match && (stripos($tourDestination, $toCity) !== false);
                error_log("🔍 To city check: '{$tourDestination}' vs '{$toCity}' -> " . ($match ? 'MATCH' : 'NO MATCH'));
            }

            // Проверка дат
            if ($dates && isset($tour['departure_date'])) {
                $parts = explode(' - ', $dates);
                if (count($parts) === 2) {
                    $start = strtotime(str_replace('.', '-', $parts[0]));
                    $end   = strtotime(str_replace('.', '-', $parts[1]));
                    $tourDate = strtotime($tour['departure_date']);
                    
                    $dateMatch = ($tourDate >= $start && $tourDate <= $end);
                    $match = $match && $dateMatch;
                    
                    error_log("🔍 Date check: '{$tour['departure_date']}' in range '{$dates}' -> " . ($dateMatch ? 'MATCH' : 'NO MATCH'));
                }
            }

            error_log("🔍 Final match for tour {$tour['id']}: " . ($match ? 'INCLUDED' : 'EXCLUDED'));
            return $match;
        });
    }

    public function getAllTours(): string {
        return $this->get(); // просто вызывает существующий метод get()
    }
}