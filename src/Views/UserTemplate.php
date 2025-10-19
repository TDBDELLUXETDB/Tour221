<?php

namespace App\Views;

use App\Config\Config;
use App\Views\BaseTemplate;

class UserTemplate extends BaseTemplate
{
    /**
     * Формирование страницы "Вход"
     */
    public static function getUserTemplate(): string
    {
        $template = parent::getTemplate();
        $title = 'Вход - Travel Dream';

        $content = <<<HTML
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        .login-container {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--glass-border);
            margin: 1rem auto;
            max-width: 450px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .form-input-group {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            padding: 0.5rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .form-input-group:focus-within {
            border-color: var(--main-blue);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .form-input-group .input-icon {
            padding: 0 1rem;
            color: var(--main-blue);
            font-size: 1.1rem;
            min-width: 50px;
            text-align: center;
        }

        .form-input-group .form-control {
            border: none;
            background: transparent;
            padding: 0.75rem 0;
            font-size: 1rem;
            color: var(--main-text);
        }

        .form-input-group .form-control::placeholder {
            color: #999;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            width: 100%;
            margin-top: 0.5rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--main-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* Анимация */
        .fade-in-up {
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        </style>

        <div class="container login-wrapper">
            <div class="row justify-content-center align-items-center w-100 mx-0">
                <div class="col-xl-4 col-lg-5 col-md-6">
                    <div class="login-container fade-in-up">
                        <div class="login-header fade-in-up">
                            <h3>Добро пожаловать!</h3>
                            <p>Войдите в свой аккаунт Travel Dream</p>
                        </div>
                        
                        <div class="login-form">
HTML;
        $content .= self::getFormLogin();
        $content .= <<<HTML
                            <div class="register-link fade-in-up">
                                <p>Нет аккаунта? <a href="/register">Зарегистрируйтесь</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
HTML;

        return sprintf($template, $title, $content);
    }

    /**
     * Форма входа (логин, пароль)
     */
    public static function getFormLogin(): string
    {
        $html = <<<FORMA
                <form action="/login" method="POST" class="fade-in-up">
                    <div class="form-input-group">
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="text" name="username" class="form-control" placeholder="Логин или email" required>
                    </div>

                    <div class="form-input-group">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="Пароль" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Войти в аккаунт
                    </button>
                </form>
FORMA;
        return $html;
    }

    /**
     * Форма редактирования профиля
     */
    public static function getProfileForm(array $userData = []): string
    {
        $template = parent::getTemplate();
        $title = 'Профиль - Travel Dream';

        $username = htmlspecialchars($userData['username'] ?? '');
        $email = htmlspecialchars($userData['email'] ?? '');
        $address = htmlspecialchars($userData['address'] ?? '');
        $phone = htmlspecialchars($userData['phone'] ?? '');
        $avatar = htmlspecialchars($userData['avatar'] ?? '/assets/image/default-avatar.png');

        $content = <<<HTML
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        .profile-container {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--glass-border);
            max-width: 600px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .avatar-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(102, 126, 234, 0.2);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .avatar-preview:hover {
            transform: scale(1.05);
            border-color: rgba(102, 126, 234, 0.4);
        }

        .avatar-upload-btn {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            position: relative;
        }

        .avatar-upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .avatar-upload-btn input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .btn-save {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            width: 100%;
            margin-top: 0.5rem;
            cursor: pointer;
        }

        .btn-save::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-save:hover::before {
            left: 100%;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* Анимация */
        .fade-in-up {
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        </style>

        <div class="container profile-wrapper">
            <div class="profile-container fade-in-up">
                <div class="profile-header fade-in-up">
                    <h3>Мой профиль</h3>
                    <p>Управление настройками аккаунта</p>
                </div>
                
                <form action="/profile" method="POST" enctype="multipart/form-data" id="profileForm">
                    <div class="avatar-section fade-in-up">
                        <img src="{$avatar}" alt="Аватар пользователя" class="avatar-preview" id="avatarPreview">
                        <div class="avatar-upload-btn">
                            <i class="fas fa-camera me-2"></i>Сменить аватар
                            <input type="file" name="avatar" accept="image/*" id="avatarInput">
                        </div>
                    </div>
                    
                    <div class="form-input-group fade-in-up">
                        <div class="input-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <input type="text" name="username" class="form-control" value="{$username}" placeholder="Имя пользователя" required>
                    </div>
                    
                    <div class="form-input-group fade-in-up">
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="email" class="form-control" value="{$email}" placeholder="Email" required>
                    </div>
                    
                    <div class="form-input-group fade-in-up">
                        <div class="input-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <input type="text" name="address" class="form-control" value="{$address}" placeholder="Адрес доставки">
                    </div>
                    
                    <div class="form-input-group fade-in-up">
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <input type="text" name="phone" class="form-control" value="{$phone}" placeholder="Номер телефона">
                    </div>
                    
                    <button type="submit" class="btn-save fade-in-up">
                        <i class="fas fa-save me-2"></i>Сохранить изменения
                    </button>
                </form>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreview = document.getElementById('avatarPreview');
            const profileForm = document.getElementById('profileForm');

            // Обработка предпросмотра аватарки
            if (avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Проверяем размер файла (максимум 5MB)
                        if (file.size > 5 * 1024 * 1024) {
                            alert('Файл слишком большой. Максимальный размер: 5MB');
                            this.value = '';
                            return;
                        }

                        // Проверяем тип файла
                        if (!file.type.match('image.*')) {
                            alert('Пожалуйста, выберите изображение');
                            this.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            avatarPreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Обработка отправки формы
            if (profileForm) {
                profileForm.addEventListener('submit', function (e) {
                    const avatarFile = avatarInput.files[0];
                    if (avatarFile) {
                        // Дополнительная проверка перед отправкой
                        if (avatarFile.size > 5 * 1024 * 1024) {
                            e.preventDefault();
                            alert('Файл слишком большой. Максимальный размер: 5MB');
                            return;
                        }

                        if (!avatarFile.type.match('image.*')) {
                            e.preventDefault();
                            alert('Пожалуйста, выберите изображение');
                            return;
                        }
                    }

                    // Показываем индикатор загрузки
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Сохранение...';
                    submitBtn.disabled = true;
                });
            }
        });
        </script>
HTML;

        return sprintf($template, $title, $content);
    }

    /**
     * Страница истории заказов
     */
    public static function getHistoryTemplate(?array $data): string
    {
        $template = parent::getTemplate();
        $title = 'История заказов - Travel Dream';

        $content = <<<HTML
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        .history-container {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--glass-border);
            margin: 1rem auto;
        }

        .history-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .history-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .booking-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .booking-number {
            font-size: 1.2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .booking-status {
            padding: 0.4rem 0.8rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            color: white;
        }

        .booking-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .booking-detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .booking-detail-item i {
            color: var(--main-blue);
            width: 16px;
        }

        .booking-total {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2ecc71;
            text-align: right;
        }

        .btn-details {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .empty-history {
            text-align: center;
            padding: 3rem 2rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .empty-history i {
            font-size: 3.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        /* Анимация */
        .fade-in-up {
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        </style>

        <div class="container history-wrapper">
            <div class="history-container fade-in-up">
                <div class="history-header fade-in-up">
                    <h2><i class="fas fa-history me-3"></i>История заказов</h2>
                    <p class="text-muted">Все ваши заказы в одном месте</p>
                </div>
HTML;

        if (empty($data)) {
            $content .= <<<HTML
                <div class="empty-history fade-in-up">
                    <i class="fas fa-shopping-bag"></i>
                    <h4 class="text-muted mb-3">Заказов пока нет</h4>
                    <p class="text-muted mb-4">Сделайте свой первый заказ и он появится здесь</p>
                    <a href="/Tours" class="btn-details">
                        <i class="fas fa-pizza-slice me-2"></i>Перейти к заказу
                    </a>
                </div>
HTML;
        } else {
            $content .= '<div class="row">';
            foreach ($data as $row) {
                $bookingDate = date("d.m.Y H:i", strtotime($row['created'] ?? $row['created_at'] ?? ''));
                $nameStatus = Config::getStatusName($row['status']);

                // Исправление: проверяем разные возможные ключи для адреса
                $address = htmlspecialchars($row['address'] ?? $row['addres'] ?? 'Адрес не указан');
                $bookingId = htmlspecialchars($row['id'] ?? '');
                $totalSum = htmlspecialchars($row['all_sum'] ?? $row['total_sum'] ?? '0');

                $statusConfig = match ($row['status']) {
                    1 => ['color' => 'linear-gradient(135deg, #f39c12, #e67e22)', 'icon' => 'fa-clock'],
                    2 => ['color' => 'linear-gradient(135deg, #27ae60, #2ecc71)', 'icon' => 'fa-check-circle'],
                    3 => ['color' => 'linear-gradient(135deg, #3498db, #2980b9)', 'icon' => 'fa-truck'],
                    4 => ['color' => 'linear-gradient(135deg, #e74c3c, #c0392b)', 'icon' => 'fa-times-circle'],
                    default => ['color' => 'linear-gradient(135deg, #95a5a6, #7f8c8d)', 'icon' => 'fa-question-circle']
                };

                $content .= <<<HTML
                <div class="col-lg-6 mb-3">
                    <div class="booking-card fade-in-up">
                        <div class="booking-header">
                            <div class="booking-number">Заказ #{$bookingId}</div>
                            <span class="booking-status" style="background: {$statusConfig['color']}">
                                <i class="fas {$statusConfig['icon']} me-2"></i>{$nameStatus}
                            </span>
                        </div>
                        
                        <div class="booking-details">
                            <div class="booking-detail-item">
                                <i class="fas fa-calendar"></i>
                                <span>{$bookingDate}</span>
                            </div>
                            <div class="booking-detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{$address}</span>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="booking-total">
                                <i class="fas fa-ruble-sign me-1"></i>{$totalSum}
                            </div>
                            <a href="/booking/{$bookingId}" class="btn-details">
                                <i class="fas fa-eye me-2"></i>Подробнее
                            </a>
                        </div>
                    </div>
                </div>
HTML;
            }
            $content .= '</div>';
        }

        $content .= '</div></div>';

        return sprintf($template, $title, $content);
    }
}