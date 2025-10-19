<?php
namespace App\Router;

use App\Controllers\AboutController;
use App\Controllers\HomeController;
use App\Controllers\TourController;
use App\Controllers\BasketController;
use App\Controllers\BookingController;
use App\Controllers\RegisterController;
use App\Controllers\UserController;
use App\Controllers\AdminController;
use App\Controllers\ApiController; 
use App\Services\UserDBStorage;

class Router {
    public function route(string $url): string {
        global $user_id, $username, $avatar, $user_role;

        if (isset($_SESSION['user_id'])) {
            $userStorage = new UserDBStorage();
            $userData = $userStorage->getUserById((int)$_SESSION['user_id']);
            $user_id = $_SESSION['user_id'];
            $username = $userData['username'] ?? '';
            $avatar = $userData['avatar'] ?? '/assets/image/default-avatar.png';
            $user_role = $_SESSION['role'] ?? $userData['role'] ?? 'user';
        } else {
            $user_id = 0;
            $username = '';
            $avatar = '/assets/image/default-avatar.png';
            $user_role = 'guest';
        }

        $path = parse_url($url, PHP_URL_PATH);
        $pieces = explode("/", $path);
        $resource = $pieces[1] ?? '';

        $userController = new UserController();
        $basketController = new BasketController();
        $BookingController = new BookingController();

        // POST-запросы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            switch ($resource) {
                case 'basket': $basketController->add(); break;
                case 'basket_remove': $basketController->remove(); break;
                case 'basket_increase': $basketController->increase(); break;
                case 'basket_decrease': $basketController->decrease(); break;
                case 'basket_clear': $basketController->clear(); break;
                case 'profile': $userController->updateProfile(); break;
                case 'register': (new RegisterController())->post(); break;
                case 'login': $userController->login(); break;
                case 'Booking': $BookingController->create(); break;
                case 'admin':
                    if ($user_role !== 'admin') {
                        $_SESSION['flash'] = "Доступ запрещен: недостаточно прав";
                        header("Location: /");
                        exit();
                    }
                    $adminController = new AdminController();
                    $subroute = $pieces[2] ?? null;
                    if ($subroute === 'update_user') $adminController->updateUser();
                    elseif ($subroute === 'delete_user' && isset($pieces[3])) {
                        $_GET['id'] = (int)$pieces[3];
                        $adminController->delete();
                    }
                    header("Location: /admin");
                    exit();
            }
            exit();
        }

        // GET-запросы
        switch ($resource) {
            case "api":
                $apiRoute = $pieces[2] ?? '';
                $TourController = new TourController();

                if ($apiRoute === 'tours') {
                    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
                    $TourController->searchTours(); // вывод JSON
                    exit();
                }

                if ($apiRoute === 'cities') {
                    (new ApiController())->searchCities();
                    exit();
                }

                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'API endpoint not found']);
                exit();

            case "Tours":
                $TourController = new \App\Controllers\TourController();

                $searchParams = [
                    'from_city' => $_GET['from_city'] ?? '',
                    'to_city' => $_GET['to_city'] ?? '',
                    'dates' => $_GET['dates'] ?? ''
                ];

                echo $TourController->getAllTours($searchParams); // вызываем метод, который отдаёт TourTemplate
                break;


            case "about": return (new AboutController())->get();
            case "Booking":
                if (isset($pieces[2]) && is_numeric($pieces[2])) {
                    $BookingId = (int)$pieces[2];
                    return $BookingController->getDetails($BookingId);
                }
                return $BookingController->get();

            case "register":
                $registerController = new RegisterController();
                if (isset($pieces[2])) {
                    switch ($pieces[2]) {
                        case "google": $registerController->googleAuth(); exit;
                        case "vk": $registerController->vkAuth(); exit;
                        case "yandex": $registerController->yandexAuth(); exit;
                        case "steam": $registerController->steamAuth(); exit;
                    }
                }
                return $registerController->get();

            case "verify":
                $token = $pieces[2] ?? null;
                return (new RegisterController())->verify($token);

            case "history": return $userController->getBookingsHistory();
            case "login": return $userController->get();
            case "logout":
                unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role']);
                session_destroy();
                header("Location: /");
                exit();

            case "profile": return $userController->profile();

            case "admin":
                if ($user_role !== 'admin') {
                    $_SESSION['flash'] = "Доступ запрещен: недостаточно прав";
                    header("Location: /");
                    exit();
                }
                $adminController = new AdminController();
                $subroute = $pieces[2] ?? null;
                if ($subroute === 'edit_user' && isset($pieces[3]) && is_numeric($pieces[3])) {
                    $_GET['id'] = (int)$pieces[3];
                    return $adminController->editUser();
                }
                return $adminController->index();

            default:
                return (new HomeController())->get();
        }
    }
}
