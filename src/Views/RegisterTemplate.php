<?php

namespace App\Views;

use App\Views\BaseTemplate;

class RegisterTemplate extends BaseTemplate
{
    /**
     * Возвращает HTML-шаблон страницы регистрации.
     * @return string
     */
    public static function getRegisterTemplate(): string
    {
        $template = parent::getTemplate();
        $title = 'Начать Путешествие | Регистрация';

        // Основной контент страницы
        $content = <<<HTML
        <div class="main-content-wrapper">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-lg-5 col-md-7 col-sm-9">
                    <div class="text-center mb-4">
                        <img src="/assets/image/TT.png" alt="Логотип компании" width="80" height="80" class="rounded-circle mb-3">
                        <h1 class="display-5 fw-bold text-main-text">Регистрация</h1>
                        <p class="text-main-text opacity-85 fw-light">Начните свое незабываемое путешествие с нами!</p>
                    </div>

                    <form action="/register" method="POST" class="form-animated">
                        <div class="mb-4 input-group input-animate" data-delay="0.1">
                            <span class="input-group-text custom-input-group-text">
                                <i class="fas fa-user-tie" style="color: var(--main-blue);"></i>
                            </span>
                            <input type="text" name="username" class="form-control custom-form-control" placeholder="Имя пользователя" required>
                            <div class="input-focus-line"></div>
                        </div>

                        <div class="mb-4 input-group input-animate" data-delay="0.2">
                            <span class="input-group-text custom-input-group-text">
                                <i class="fas fa-envelope-open-text" style="color: var(--main-blue);"></i>
                            </span>
                            <input type="email" name="email" class="form-control custom-form-control" placeholder="Email" required>
                            <div class="input-focus-line"></div>
                        </div>

                        <div class="mb-4 input-group input-animate" data-delay="0.3">
                            <span class="input-group-text custom-input-group-text">
                                <i class="fas fa-key" style="color: var(--main-blue);"></i>
                            </span>
                            <input type="password" name="password" class="form-control custom-form-control" placeholder="Пароль" required>
                            <div class="input-focus-line"></div>
                        </div>

                        <div class="mb-4 input-group input-animate" data-delay="0.4">
                            <span class="input-group-text custom-input-group-text">
                                <i class="fas fa-lock" style="color: var(--main-blue);"></i>
                            </span>
                            <input type="password" name="confirm_password" class="form-control custom-form-control" placeholder="Подтвердите пароль" required>
                            <div class="input-focus-line"></div>
                        </div>

                        <button type="submit" class="btn w-100 mt-4 btn-custom-action btn-glow animate-pop" data-delay="0.5">
                            <i class="fas fa-plane me-2"></i>Зарегистрироваться
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-main-text opacity-85 fw-light">Уже есть аккаунт? <a href="/login" class="text-main-blue text-decoration-none fw-bold hover-glow">Войти</a></p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .custom-input-group-text {
                background: #E9ECEF !important;
                border: 1px solid rgba(0, 0, 0, 0.1) !important;
                border-right: none !important;
                border-radius: 10px 0 0 10px !important;
                color: var(--main-blue) !important;
            }

            .custom-form-control {
                background: #F8F9FA !important;
                border: 1px solid rgba(0, 0, 0, 0.1) !important;
                border-left: none !important;
                border-radius: 0 10px 10px 0 !important;
                color: var(--main-text) !important;
                padding: 0.8rem 1rem;
                font-weight: 400;
            }

            .custom-form-control::placeholder {
                color: rgba(33, 37, 41, 0.6) !important;
            }

            .custom-form-control:focus {
                background: #FFFFFF !important;
                border-color: var(--light-accent) !important;
                box-shadow: 0 0 0 0.2rem rgba(72, 202, 228, 0.5) !important;
            }

            .input-focus-line {
                background: var(--secondary-gradient);
                height: 3px;
            }

            .btn-custom-action {
                background: var(--primary-gradient);
                color: #FFFFFF;
                border: none;
                border-radius: 12px;
                font-size: 1.15rem;
                padding: 14px;
                font-weight: 700;
                box-shadow: 0 8px 25px rgba(0, 119, 182, 0.4);
                transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .btn-custom-action:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 40px rgba(0, 119, 182, 0.6);
            }

            .hover-glow {
                color: var(--main-blue) !important;
                transition: text-shadow 0.3s ease, color 0.3s ease;
            }

            .hover-glow:hover {
                text-shadow: 0 0 10px rgba(0, 119, 182, 0.5);
            }

            /* Анимации */
            .input-animate {
                opacity: 0;
                transform: translateY(20px);
            }

            .animate-slide-in {
                animation: slideInUp 0.8s ease forwards;
            }

            @keyframes slideInUp {
                0% {
                    opacity: 0;
                    transform: translateY(20px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-pop-in {
                animation: popIn 0.6s ease forwards;
            }

            @keyframes popIn {
                0% {
                    opacity: 0;
                    transform: scale(0.8);
                }
                70% {
                    transform: scale(1.05);
                }
                100% {
                    opacity: 1;
                    transform: scale(1);
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputs = document.querySelectorAll('.input-animate');
                if (inputs.length > 0) {
                    inputs.forEach(input => {
                        const delay = parseFloat(input.getAttribute('data-delay')) || 0;
                        setTimeout(() => {
                            input.classList.add('animate-slide-in');
                        }, delay * 1000);
                    });
                }

                const button = document.querySelector('.animate-pop');
                if (button) {
                    const delay = parseFloat(button.getAttribute('data-delay')) || 0;
                    setTimeout(() => {
                        button.classList.add('animate-pop-in');
                    }, delay * 1000);
                }
            });
        </script>
HTML;

        return sprintf($template, $title, $content);
    }

    /**
     * Возвращает HTML-шаблон страницы подтверждения.
     * @return string
     */
    public static function getVerifyTemplate(): string
    {
        $template = parent::getTemplate();
        $title = 'Путешествие Начинается | Подтверждение';

        $content = <<<HTML
        <main class="main-content-wrapper">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-lg-5 col-md-7 col-sm-9 text-center">
                    <div class="success-animation">
                        <img src="/assets/image/TT.png" alt="Логотип компании" width="100" height="100" class="rounded-circle mb-3">
                        <i class="fas fa-check-circle fa-5x mb-4" style="color: #0077B6;"></i>
                    </div>
                    <h3 class="mb-4 fw-bold text-main-text">Ваш Билет Оформлен!</h3>
                    <p class="lead text-main-text opacity-95 fw-light">Ваш аккаунт путешественника успешно активирован!</p>
                    <p class="text-main-text opacity-85 fw-light">Пора планировать свой следующий отпуск.</p>
                    <a href="/login" class="btn mt-4 px-4 py-2 btn-custom-action btn-glow" style="border-radius: 12px;">
                        <i class="fas fa-sign-in-alt me-2"></i>Начать Поиск Туров
                    </a>
                </div>
            </div>
        </main>
HTML;

        return sprintf($template, $title, $content);
    }
}