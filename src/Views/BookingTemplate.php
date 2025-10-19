<?php

namespace App\Views;

use App\Views\BaseTemplate;

class BookingTemplate extends BaseTemplate
{
    /**
     * @param array $tours
     * @param array $userData
     * @return string
     */
    public static function getBookingTemplate(array $tours, array $userData = []): string
    {
        $template = parent::getTemplate();
        
        // Защита от ошибок sprintf
        $template = str_replace('%1$s', '___PLACEHOLDER_TITLE___', $template);
        $template = str_replace('%2$s', '___PLACEHOLDER_CONTENT___', $template);
        $template = str_replace('%', '%%', $template);
        $template = str_replace('___PLACEHOLDER_TITLE___', '%1$s', $template);
        $template = str_replace('___PLACEHOLDER_CONTENT___', '%2$s', $template);

        $title = 'Оформление бронирования - Travel Dream';
        $username = htmlspecialchars($userData['username'] ?? '');
        $email = htmlspecialchars($userData['email'] ?? '');
        $address = htmlspecialchars($userData['address'] ?? '');
        $phone = htmlspecialchars($userData['phone'] ?? '');

        $content = <<<HTML
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap');
        
        .booking-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 2rem;
            font-family: 'Montserrat', sans-serif;
        }
        
        .booking-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .booking-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .booking-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .tour-item {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,119,182,0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .tour-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,119,182,0.15);
        }
        
        .tour-image {
            width: 120px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .quantity-controls {
            background: rgba(248, 249, 250, 0.8);
            border-radius: 50px;
            padding: 0.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .quantity-btn:hover {
            transform: scale(1.1);
        }
        
        .btn-decrease {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }
        
        .btn-increase {
            background: linear-gradient(135deg, #51cf66, #40c057);
            color: white;
        }
        
        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
            color: #333;
        }
        
        .total-card {
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            color: white;
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .payment-option {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            text-align: left;
            width: 100%;
            cursor: pointer;
        }
        
        .payment-option:hover {
            border-color: #0077B6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,119,182,0.2);
        }
        
        .payment-option.active {
            border-color: #0077B6;
            background: linear-gradient(135deg, rgba(0,119,182,0.1), rgba(0,180,216,0.1));
        }
        
        .payment-icon {
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(0,119,182,0.1);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
        }
        
        .form-control:focus {
            border-color: #0077B6;
            box-shadow: 0 0 0 0.2rem rgba(0,119,182,0.25);
        }
        
        .btn-booking {
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            width: 100%;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
        }
        
        .btn-booking:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-booking:disabled:hover {
            transform: none;
            box-shadow: none;
        }
        
        .btn-booking::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-booking:hover:not(:disabled)::before {
            left: 100%;
        }
        
        .btn-booking:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,119,182,0.4);
        }
        
        .btn-clear {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(108, 117, 125, 0.2);
            color: #6c757d;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
        }
        
        .btn-clear:hover {
            border-color: #dc3545;
            color: #dc3545;
            transform: translateY(-1px);
        }
        
        .empty-cart {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .empty-cart i {
            font-size: 4rem;
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .sticky-sidebar {
            position: sticky;
            top: 2rem;
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,119,182,0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .section-title {
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease;
            animation-play-state: paused;
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
        
        .travel-decoration {
            position: absolute;
            font-size: 1.5rem;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .travel-1 { top: 10%; left: 5%; animation-delay: 0s; }
        .travel-2 { top: 60%; right: 10%; animation-delay: 2s; }
        .travel-3 { bottom: 20%; left: 15%; animation-delay: 4s; }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-full-width {
            grid-column: 1 / -1;
        }
        
        .tour-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .tour-meta i {
            color: #0077B6;
            margin-right: 0.5rem;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .booking-container {
                padding: 1.5rem;
            }
            
            .tour-image {
                width: 80px;
                height: 60px;
            }
        }
        </style>

        <div class="booking-container position-relative">
            <div class="travel-decoration travel-1">✈️</div>
            <div class="travel-decoration travel-2">🏝️</div>
            <div class="travel-decoration travel-3">🏨</div>
            
            <div class="booking-header fade-in-up">
                <h1>Оформление бронирования</h1>
                <p>Завершите бронирование вашего путешествия</p>
            </div>
            
            <form action="/Booking" method="POST" id="booking-form">
                <div class="row">
                    <!-- Левая колонка - Корзина и данные -->
                    <div class="col-lg-8">
                        <!-- Корзина туров -->
                        <div class="info-card fade-in-up">
                            <h3 class="section-title">
                                <i class="fas fa-suitcase-rolling"></i>Ваши туры
                            </h3>
HTML;

        $all_sum = 0;

        if (!empty($tours)) {
            foreach ($tours as $tour) {
                $name = htmlspecialchars($tour['name'] ?? '');
                $destination = htmlspecialchars($tour['destination'] ?? '');
                $price = htmlspecialchars($tour['price'] ?? 0);
                $quantity = htmlspecialchars($tour['quantity'] ?? 1);
                $duration = htmlspecialchars($tour['duration_days'] ?? '');
                $departure_date = htmlspecialchars($tour['departure_date'] ?? '');

                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/{$tour['id']}.jpg";
                $image = file_exists($imagePath)
                    ? "/assets/images/{$tour['id']}.jpg"
                    : 'https://placehold.co/600x400/0077B6/FFFFFF?text=Тур';

                $sum = $price * $quantity;
                $all_sum += $sum;

                $decreaseAction = ($quantity > 1) ? '/basket_decrease' : '/basket_remove';
                $decreaseIcon = ($quantity > 1) ? 'fa-minus' : 'fa-trash-alt';

                $content .= <<<HTML
                <div class="tour-item fade-in-up">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="{$image}" class="tour-image" alt="{$name}">
                        </div>
                        <div class="col">
                            <h5 class="mb-1 fw-bold">{$name}</h5>
                            <div class="tour-meta">
                                <div><i class="fas fa-map-marker-alt"></i> {$destination}</div>
                                <div><i class="fas fa-calendar-alt"></i> {$departure_date}</div>
                                <div><i class="fas fa-clock"></i> {$duration} дней</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 text-success">{$sum} ₽</span>
                                <div class="quantity-controls">
                                    <form action="{$decreaseAction}" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="{$tour['id']}">
                                        <button type="submit" class="quantity-btn btn-decrease">
                                            <i class="fas {$decreaseIcon}"></i>
                                        </button>
                                    </form>
                                    <span class="quantity-display">{$quantity}</span>
                                    <form action="/basket_increase" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="{$tour['id']}">
                                        <button type="submit" class="quantity-btn btn-increase">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
HTML;
            }

            $content .= <<<HTML
                        <div class="total-card fade-in-up">
                            <h3 class="mb-0">
                                <i class="fas fa-receipt me-2"></i>
                                Итого: {$all_sum} ₽
                            </h3>
                        </div>
HTML;
        } else {
            $content .= <<<HTML
                        <div class="empty-cart fade-in-up">
                            <i class="fas fa-suitcase"></i>
                            <h4>Корзина пуста</h4>
                            <p class="text-muted">Добавьте туры в корзину, чтобы оформить бронирование</p>
                            <a href="/Tours" class="btn-booking mt-3">
                                <i class="fas fa-search me-2"></i>Найти туры
                            </a>
                        </div>
HTML;
        }

        $content .= <<<HTML
                        </div>
                        
                        <!-- Данные туриста -->
                        <div class="info-card fade-in-up">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i>Данные туриста
                            </h3>
                            <div class="form-grid">
                                <div class="form-full-width">
                                    <label class="form-label fw-semibold">ФИО:</label>
                                    <input type="text" class="form-control" value="{$username}" name="fio" placeholder="Введите ваше ФИО" required>
                                </div>
                                <div>
                                    <label class="form-label fw-semibold">Email:</label>
                                    <input type="email" class="form-control" value="{$email}" name="email" placeholder="Введите ваш email" required>
                                </div>
                                <div>
                                    <label class="form-label fw-semibold">Телефон:</label>
                                    <input type="tel" class="form-control" value="{$phone}" name="phone" placeholder="Введите номер телефона" required>
                                </div>
                                <div class="form-full-width">
                                    <label class="form-label fw-semibold">Адрес:</label>
                                    <input type="text" class="form-control" value="{$address}" name="address" placeholder="Введите ваш адрес" required>
                                </div>
                                <div class="form-full-width">
                                    <label class="form-label fw-semibold">Паспортные данные:</label>
                                    <input type="text" class="form-control" name="passport" placeholder="Серия и номер паспорта" required>
                                </div>
                                <div class="form-full-width">
                                    <label class="form-label fw-semibold">Дата рождения:</label>
                                    <input type="date" class="form-control" name="birth_date" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Правая колонка - Оплата и подтверждение -->
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="sticky-sidebar">
                            <!-- Способ оплаты -->
                            <div class="info-card fade-in-up">
                                <h3 class="section-title text-center">
                                    <i class="fas fa-credit-card"></i>Способ оплаты
                                </h3>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-light payment-option active" data-payment="card">
                                        <i class="fas fa-credit-card payment-icon"></i>
                                        <div>
                                            <strong>Банковская карта</strong>
                                            <br><small class="text-muted">Онлайн оплата</small>
                                        </div>
                                    </button>
                                    <button type="button" class="btn btn-light payment-option" data-payment="sbp">
                                        <i class="fas fa-qrcode payment-icon"></i>
                                        <div>
                                            <strong>СБП</strong>
                                            <br><small class="text-muted">Быстрый перевод</small>
                                        </div>
                                    </button>
                                    <button type="button" class="btn btn-light payment-option" data-payment="transfer">
                                        <i class="fas fa-university payment-icon"></i>
                                        <div>
                                            <strong>Банковский перевод</strong>
                                            <br><small class="text-muted">По счету</small>
                                        </div>
                                    </button>
                                    <button type="button" class="btn btn-light payment-option" data-payment="installment">
                                        <i class="fas fa-calendar-plus payment-icon"></i>
                                        <div>
                                            <strong>Рассрочка</strong>
                                            <br><small class="text-muted">Оплата частями</small>
                                        </div>
                                    </button>
                                </div>
                                
                                <input type="hidden" id="selected-payment" name="payment_method" value="card" required>
                                
                                <button type="submit" class="btn-booking" id="create-booking-button" 
HTML;

        // Добавляем атрибут disabled если корзина пуста
        if (empty($tours)) {
            $content .= ' disabled';
        }

        $content .= <<<HTML
>
                                    <i class="fas fa-check me-2"></i>
                                    Подтвердить бронирование
                                </button>
                            </div>
                            
                            <!-- Дополнительные действия -->
HTML;

        // Кнопка очистки корзины только если есть туры
        if (!empty($tours)) {
            $content .= <<<HTML
                            <div class="info-card fade-in-up">
                                <div class="text-center">
                                    <form action="/basket_clear" method="POST">
                                        <button type="submit" class="btn-clear">
                                            <i class="fas fa-eraser me-2"></i> Очистить корзину
                                        </button>
                                    </form>
                                </div>
                            </div>
HTML;
        }

        $content .= <<<HTML
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentButtons = document.querySelectorAll('.payment-option');
            const selectedPaymentInput = document.getElementById('selected-payment');
            const bookingForm = document.getElementById('booking-form');
            const createBookingButton = document.getElementById('create-booking-button');

            // Обработка выбора способа оплаты
            paymentButtons.forEach(button => {
                button.addEventListener('click', function () {
                    paymentButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-light');
                    });
                    this.classList.remove('btn-light');
                    this.classList.add('btn-primary', 'active');
                    
                    const paymentMethod = this.getAttribute('data-payment');
                    selectedPaymentInput.value = paymentMethod;
                    console.log('Selected payment method:', paymentMethod);
                });
            });

            // Валидация формы
            bookingForm.addEventListener('submit', function (event) {
                if (!selectedPaymentInput.value) {
                    event.preventDefault();
                    const paymentSection = document.querySelector('.sticky-sidebar .info-card');
                    let alertDiv = paymentSection.querySelector('.alert-danger');
                    
                    if (!alertDiv) {
                        alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger mt-3 fade-in-up';
                        alertDiv.innerHTML = `
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Пожалуйста, выберите способ оплаты
                        `;
                        paymentSection.querySelector('.btn-booking').before(alertDiv);
                        
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 5000);
                    }
                    
                    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            // Анимация появления элементов при скролле
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.fade-in-up').forEach(el => {
                observer.observe(el);
            });
        });
        </script>
HTML;

        return sprintf($template, $title, $content);
    }

    /**
     * @param array $booking
     * @return string
     */
    public static function getBookingDetailsTemplate(array $booking): string
    {
        $template = parent::getTemplate();
        
        // Защита от ошибок sprintf
        $template = str_replace('%1$s', '___PLACEHOLDER_TITLE___', $template);
        $template = str_replace('%2$s', '___PLACEHOLDER_CONTENT___', $template);
        $template = str_replace('%', '%%', $template);
        $template = str_replace('___PLACEHOLDER_TITLE___', '%1$s', $template);
        $template = str_replace('___PLACEHOLDER_CONTENT___', '%2$s', $template);

        $title = 'Детали бронирования - Travel Dream';

        if (empty($booking)) {
            return "Детали бронирования не найдены.";
        }

        // Форматируем данные бронирования
        $bookingId = htmlspecialchars($booking['id'] ?? $booking['booking_id'] ?? '');
        $fio = htmlspecialchars($booking['fio'] ?? '');
        $address = htmlspecialchars($booking['address'] ?? '');
        $phone = htmlspecialchars($booking['phone'] ?? '');
        $email = htmlspecialchars($booking['email'] ?? '');
        $passport = htmlspecialchars($booking['passport'] ?? '');
        $birthDate = htmlspecialchars($booking['birth_date'] ?? '');
        $paymentMethod = htmlspecialchars($booking['payment_method'] ?? '');
        
        $totalSum = htmlspecialchars($booking['all_sum'] ?? $booking['total_sum'] ?? $booking['sum'] ?? '0');
        $createdAt = htmlspecialchars($booking['created_at'] ?? $booking['created'] ?? $booking['date'] ?? '');
        
        // Преобразуем способ оплаты в читаемый вид
        $paymentMethodName = match($paymentMethod) {
            'card' => 'Банковская карта',
            'sbp' => 'СБП',
            'transfer' => 'Банковский перевод',
            'installment' => 'Рассрочка',
            default => $paymentMethod ?: 'Не указан'
        };

        // Генерируем HTML для туров
        $toursHtml = '';
        $calculatedTotal = 0;
        
        $tours = $booking['tours'] ?? $booking['items'] ?? [];
        
        if (!empty($tours)) {
            foreach ($tours as $tour) {
                $tourName = htmlspecialchars($tour['name'] ?? $tour['tour_name'] ?? 'Неизвестный тур');
                $destination = htmlspecialchars($tour['destination'] ?? '');
                $duration = htmlspecialchars($tour['duration_days'] ?? '');
                $departureDate = htmlspecialchars($tour['departure_date'] ?? '');
                $quantity = intval($tour['quantity'] ?? $tour['count_item'] ?? $tour['count'] ?? 1);
                $price = floatval($tour['price'] ?? $tour['price_item'] ?? $tour['item_price'] ?? 0);
                $total = $quantity * $price;
                $calculatedTotal += $total;
                
                $formattedPrice = number_format($price, 0, '.', ' ');
                $formattedTotal = number_format($total, 0, '.', ' ');
                
                $toursHtml .= <<<HTML
                <div class="tour-item fade-in-up">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-1 fw-bold">{$tourName}</h6>
                            <div class="tour-meta">
                                <div><i class="fas fa-map-marker-alt"></i> {$destination}</div>
                                <div><i class="fas fa-calendar-alt"></i> {$departureDate}</div>
                                <div><i class="fas fa-clock"></i> {$duration} дней</div>
                            </div>
                            <small class="text-muted">{$formattedPrice} ₽ × {$quantity} чел.</small>
                        </div>
                        <div class="col-auto">
                            <span class="fw-bold text-success">{$formattedTotal} ₽</span>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
HTML;
            }
        } else {
            $toursHtml = '<div class="text-muted text-center">Туры не найдены</div>';
        }

        // Используем рассчитанную сумму, если оригинальная не найдена
        if ($totalSum == '0' && $calculatedTotal > 0) {
            $totalSum = number_format($calculatedTotal, 0, '.', ' ');
        } else {
            $totalSum = number_format(floatval($totalSum), 0, '.', ' ');
        }

        // Если дата пустая, ставим текущую
        if (empty($createdAt)) {
            $createdAt = date('d.m.Y H:i:s');
        }

        $content = <<<HTML
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap');
        
        .booking-details-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 2rem;
            font-family: 'Montserrat', sans-serif;
        }
        
        .booking-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .booking-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .booking-number {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,119,182,0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .section-title {
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .total-card {
            background: linear-gradient(135deg, #0077B6, #00B4D8);
            color: white;
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .tour-item {
            padding: 0.75rem 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            align-items: center;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 150px;
        }
        
        .info-value {
            color: #333;
            text-align: right;
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease;
            animation-play-state: paused;
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
        
        .btn-back {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(0,119,182,0.2);
            color: #0077B6;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Montserrat', sans-serif;
        }
        
        .btn-back:hover {
            border-color: #0077B6;
            transform: translateY(-1px);
            color: #0077B6;
        }
        
        .tour-meta {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .tour-meta i {
            color: #0077B6;
            margin-right: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .booking-details-container {
                padding: 1.5rem;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-value {
                text-align: left;
                margin-top: 0.25rem;
            }
        }
        </style>

        <div class="booking-details-container">
            <div class="booking-header fade-in-up">
                <h1>Детали бронирования</h1>
                <div class="booking-number">
                    <i class="fas fa-receipt me-2"></i>
                    Номер бронирования: #{$bookingId}
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <div class="info-card fade-in-up">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>Данные туриста
                        </h3>
                        <div class="info-row">
                            <span class="info-label">ФИО:</span>
                            <span class="info-value">{$fio}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Адрес:</span>
                            <span class="info-value">{$address}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Телефон:</span>
                            <span class="info-value">{$phone}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{$email}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Паспорт:</span>
                            <span class="info-value">{$passport}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Дата рождения:</span>
                            <span class="info-value">{$birthDate}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Способ оплаты:</span>
                            <span class="info-value">{$paymentMethodName}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Дата бронирования:</span>
                            <span class="info-value">{$createdAt}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="info-card fade-in-up">
                        <h3 class="section-title">
                            <i class="fas fa-suitcase-rolling"></i>Забронированные туры
                        </h3>
                        <div class="tours-list">
                            {$toursHtml}
                        </div>
                    </div>
                    
                    <div class="total-card fade-in-up">
                        <h3 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Итого: {$totalSum} ₽
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4 fade-in-up">
                <a href="/history" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>
                    Назад к истории бронирований
                </a>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Анимация появления элементов при скролле
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.fade-in-up').forEach(el => {
                observer.observe(el);
            });
        });
        </script>
HTML;
        
        return sprintf($template, $title, $content);
    }
}