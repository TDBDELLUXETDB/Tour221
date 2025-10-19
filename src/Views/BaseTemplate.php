<?php
namespace App\Views;

class BaseTemplate
{
    /**
     * Возвращает базовый HTML-шаблон.
     *
     * @param string $title Заголовок страницы (sprintf %1$s)
     * @param string $content Основное содержимое страницы (sprintf %2$s)
     * @global int $user_id ID авторизованного пользователя
     * @global string $username Имя авторизованного пользователя
     * @global string $avatar Ссылка на аватар пользователя
     * @return string Полный HTML-код шаблона
     */
    public static function getTemplate()
    {
        global $user_id, $username, $avatar;
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $base_url = 'http://localhost';

        $template = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>%1\$s</title>
    <link rel="icon" type="image/x-icon" href="{$base_url}/assets/image/TT.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{$base_url}/assets/css/style.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0077B6, #00B4D8);
            --secondary-gradient: linear-gradient(135deg, #48CAE4, #90E0EF); 
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.3);
            --main-blue: #0077B6;
            --main-text: #212529;
        }

        body {
            font-family: 'Roboto', sans-serif !important;
            font-size: 16px;
            line-height: 1.6;
            background: 
                radial-gradient(circle at 20%% 80%%, rgba(72,202,228,0.15) 0%%, transparent 50%%),
                radial-gradient(circle at 80%% 20%%, rgba(0,119,182,0.15) 0%%, transparent 50%%),
                radial-gradient(circle at 40%% 40%%, rgba(0,180,216,0.1) 0%%, transparent 50%%),
                linear-gradient(135deg, #F8F9FA 0%%, #E9ECEF 100%%);
            background-size: cover;
            background-attachment: fixed;
            color: var(--main-text);
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%%;
            height: 100%%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-bubble {
            position: absolute;
            border-radius: 50%%;
            background: rgba(255, 255, 255, 0.1);
            animation: float-bubble linear infinite;
            backdrop-filter: blur(5px);
        }
        .bubble-1 {
            width: 120px;
            height: 120px;
            top: 10%%;
            left: 5%%;
            animation-duration: 25s;
            background: radial-gradient(circle at 30%% 30%%, rgba(72,202,228,0.2), transparent 70%%);
        }
        .bubble-2 {
            width: 80px;
            height: 80px;
            top: 70%%;
            left: 85%%;
            animation-duration: 30s;
            animation-delay: 5s;
            background: radial-gradient(circle at 30%% 30%%, rgba(0,119,182,0.2), transparent 70%%);
        }
        .bubble-3 {
            width: 60px;
            height: 60px;
            top: 80%%;
            left: 15%%;
            animation-duration: 20s;
            animation-delay: 10s;
            background: radial-gradient(circle at 30%% 30%%, rgba(0,180,216,0.2), transparent 70%%);
        }
        .bubble-4 {
            width: 100px;
            height: 100px;
            top: 25%%;
            left: 90%%;
            animation-duration: 35s;
            animation-delay: 15s;
            background: radial-gradient(circle at 30%% 30%%, rgba(72,202,228,0.15), transparent 70%%);
        }

        @keyframes float-bubble {
            0%%, 100%% { transform: translateY(0) translateX(0) rotate(0deg); }
            25%% { transform: translateY(-15px) translateX(8px) rotate(3deg); }
            50%% { transform: translateY(8px) translateX(-12px) rotate(-2deg); }
            75%% { transform: translateY(-10px) translateX(-8px) rotate(2deg); }
        }

        header {
            margin-bottom: 0;
        }

        .navbar {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            z-index: 1030;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 900;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }
        .navbar-brand img {
            filter: drop-shadow(0 0 5px rgba(0, 119, 182, 0.5));
        }

        .navbar-nav .nav-link {
            font-size: 1.05rem;
            color: var(--main-text) !important;
            margin-right: 1.5rem;
            font-weight: 500;
        }

        .btn-register {
            background: var(--primary-gradient);
            box-shadow: 0 4px 15px rgba(0, 119, 182, 0.3);
        }

        .btn-register:hover {
            box-shadow: 0 6px 20px rgba(0, 119, 182, 0.4);
        }
        
        main.container-fluid {
            background: transparent;
            backdrop-filter: none;
            border-radius: 0;
            padding: 0;
            margin-bottom: 2rem;
            box-shadow: none;
            border: none;
            z-index: 1;
            position: relative;
        }
        
        .main-content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        footer {
            padding: 2rem 0;
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            color: #ffffff;
            font-size: 0.9rem;
        }
        
        footer h5 {
            color: #90E0EF;
            font-weight: 700;
        }
        
        .social-icon-footer:hover {
            color: #48CAE4;
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="bg-bubble bubble-1"></div>
        <div class="bg-bubble bubble-2"></div>
        <div class="bg-bubble bubble-3"></div>
        <div class="bg-bubble bubble-4"></div>
    </div>
    
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid container-lg">
                <a class="navbar-brand" href="{$base_url}/">
                    <img src="{$base_url}/assets/image/TT.png" alt="Логотип компании" width="40" height="40" class="d-inline-block align-text-top rounded-circle">
                    TRAVEL DREAM
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{$base_url}/">
                                <i class="fas fa-home"></i>Главная
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_url}/Tours">
                                <i class="fas fa-map-marked-alt"></i>Туры
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_url}/Booking">
                                <i class="fas fa-percent"></i>Акции
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_url}/about">
                                <i class="fas fa-phone-alt"></i>Контакты
                            </a>
                        </li>
                    </ul>
HTML;

if ($user_id > 0) {
    $template .= <<<HTML
<ul class="navbar-nav ms-auto">
    <li class="nav-item dropdown">
        <a class="nav-link d-flex align-items-center user-dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{$avatar}" alt="Аватар пользователя" class="rounded-circle avatar-preview">
            <span class="d-none d-md-inline username-text ms-2">{$username}</span>
            <i class="fas fa-chevron-right transition-arrow ms-2" id="dropdownArrow"></i>
        </a>
        <ul class="dropdown-menu user-dropdown-menu dropdown-menu-end animate__animated animate__fadeIn" aria-labelledby="navbarDropdown">
            <li>
                <a class="user-dropdown-item" href="{$base_url}/profile">
                    <div class="icon-wrapper">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="item-text">
                        <span class="fw-semibold">Профиль</span>
                        <small class="text-muted">Настройки аккаунта</small>
                    </div>
                </a>
            </li>
            <li>
                <a class="user-dropdown-item" href="{$base_url}/history">
                    <div class="icon-wrapper">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="item-text">
                        <span class="fw-semibold">Мои бронирования</span>
                        <small class="text-muted">История поездок</small>
                    </div>
                </a>
            </li>
HTML;

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $template .= <<<HTML
            <li>
                <a class="user-dropdown-item" href="{$base_url}/admin">
                    <div class="icon-wrapper">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="item-text">
                        <span class="fw-semibold">Админ-панель</span>
                        <small class="text-muted">Управление сайтом</small>
                    </div>
                </a>
            </li>
HTML;
    }

    $template .= <<<HTML
            <li><hr class="dropdown-divider my-2"></li>
            <li>
                <a class="user-dropdown-item logout-item" href="{$base_url}/logout">
                    <div class="icon-wrapper">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="item-text">
                        <span class="fw-semibold">Выход</span>
                        <small class="text-muted">Завершить сеанс</small>
                    </div>
                </a>
            </li>
        </ul>
    </li>
</ul>
HTML;
} else {
    $template .= <<<HTML
<ul class="navbar-nav ms-auto">
    <li class="nav-item dropdown">
        <button class="btn btn-register dropdown-toggle" id="registerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-sign-in-alt"></i>Войти / Регистрация
        </button>
        <ul class="dropdown-menu dropdown-menu-end animate__animated animate__fadeIn" aria-labelledby="registerDropdown">
            <li><a class="dropdown-item" href="{$base_url}/register">
                <i class="fas fa-user-plus"></i>Зарегистрироваться
            </a></li>
            <li><a class="dropdown-item" href="{$base_url}/login">
                <i class="fas fa-sign-in-alt"></i>Войти
            </a></li>
        </ul>
    </li>
</ul>
HTML;
}

$template .= <<<HTML
                </div>
            </div>
        </nav>
    </header>
HTML;

        if (isset($_SESSION['flash'])) {
            $template .= <<<HTML
<div id="liveAlertBtn" class="container alert alert-custom alert-dismissible fade show" role="alert" style="margin-top: 20px;">
    <div>{$_SESSION['flash']}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
    onclick="this.parentNode.style.display='none';"></button>
</div>
HTML;
            unset($_SESSION['flash']);
        }

        $template .= <<<HTML
<main class="container-fluid p-0">
    %2\$s
</main>
<footer class="mt-5">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-md-4 mb-4">
                <h5 class="text-uppercase fw-bold">О нас</h5>
                <p class="small text-light">Мы - ваш проводник в мир незабываемых путешествий. Наша миссия — сделать поиск и бронирование туров максимально удобным и выгодным.</p>
            </div>
            <div class="col-md-4 mb-4 text-center">
                <h5 class="text-uppercase fw-bold">Свяжитесь с нами</h5>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-map-marker-alt me-2"></i><span class="text-light">Адрес: г. Москва, ул. Пушкина, д. Колотушкина 10</span></li>
                    <li><i class="fas fa-phone me-2"></i><span class="text-light">Телефон: 8 (800) 555-35-35</span></li>
                    <li><i class="fas fa-envelope me-2"></i><span class="text-light">Email: support@traveldream.ru</span></li>
                </ul>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <a href="#" target="_blank" class="social-icon-footer">
                        <i class="fab fa-vk fa-2x"></i>
                    </a>
                    <a href="#" target="_blank" class="social-icon-footer">
                        <i class="fab fa-telegram fa-2x"></i>
                    </a>
                    <a href="#" target="_blank" class="social-icon-footer">
                        <i class="fab fa-whatsapp fa-2x"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="text-uppercase fw-bold">Подписка</h5>
                <p class="small text-light">Получайте лучшие предложения на почту!</p>
                <form>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Ваш Email" aria-label="Email" style="border-radius: 8px 0 0 8px;">
                        <button class="btn btn-light" type="submit" style="border-radius: 0 8px 8px 0;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-4 pt-3 border-top border-light-subtle">
            <div class="col text-center">
                <p class="mb-0 small text-light">&copy; 2025 «Travel Dream» | Все права защищены</p>
                <p class="mb-0 small text-light">Разработано с душой для лучших путешествий</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggle = document.getElementById('navbarDropdown');
    const dropdownArrow = document.getElementById('dropdownArrow');
    if (dropdownToggle && dropdownArrow) {
        dropdownToggle.addEventListener('show.bs.dropdown', function () {
            dropdownArrow.classList.add('open');
        });
        dropdownToggle.addEventListener('hide.bs.dropdown', function () {
            dropdownArrow.classList.remove('open');
        });
    }
});
</script>
</body>
</html>
HTML;

        return $template;
    }
}