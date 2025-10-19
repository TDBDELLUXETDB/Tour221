<?php
namespace App\Controllers;
use App\Services\BookingDBStorage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Services\TourFactory;
use App\Services\BookingFactory;
use App\Services\ValidateBookingData;
use App\Views\BookingTemplate;
use App\Services\Mailer;
use App\Services\UserDBStorage;

class BookingController
{
    public function get(): string
    {
        // Получаем данные пользователя из сессии
        $userData = [];
        if (isset($_SESSION['user_id'])) {
            $userStorage = new UserDBStorage();
            $userData = $userStorage->getUserById((int)$_SESSION['user_id']);
        }

        $model = TourFactory::createTour();
        $data = $model->getBasketData();

        $BookingTemplate = new BookingTemplate();
        return $BookingTemplate->getBookingTemplate($data, $userData);
    }

    public function getDetails(int $BookingId): string
    {
        $BookingStorage = new BookingDBStorage();
        
        // Получаем данные заказа по его ID
        $BookingData = $BookingStorage->getBookingById($BookingId);
        
        // Проверяем, существует ли заказ и принадлежит ли он текущему пользователю
        global $user_id;
        if (empty($BookingData) || $BookingData['user_id'] != $user_id) {
            // Если заказ не найден или принадлежит другому пользователю, 
            // возвращаем страницу с ошибкой 404
            header("HTTP/1.0 404 Not Found");
            return "<h1>Заказ не найден или у вас нет прав для его просмотра.</h1>";
        }
        
        // Если все проверки пройдены, передаем данные заказа в шаблон для отображения
        return BookingTemplate::getBookingDetailsTemplate($BookingData);
    }

    public function create(): void
    {
        if (!ValidateBookingData::validate($_POST)) {
            $_SESSION['flash'] = "Пожалуйста, заполните все обязательные поля";
            header("Location: /Booking");
            exit();
        }

        $BookingData = $this->prepareBookingData($_POST);
        $BookingModel = BookingFactory::createBooking();
        $BookingId = $BookingModel->saveData($BookingData);

        if ($BookingId) {
            $emailSent = $this->sendBookingConfirmation($BookingData, $BookingId);
            
            // Очищаем корзину
            $_SESSION['basket'] = [];
            
            // Сообщение пользователю
            if ($emailSent) {
                $_SESSION['flash'] = "Спасибо! Ваш заказ №{$BookingId} успешно создан. Подтверждение отправлено на email.";
            } else {
                $_SESSION['flash'] = "Спасибо! Ваш заказ №{$BookingId} успешно создан, но не удалось отправить подтверждение на email.";
            }
            
            header("Location: /history");
            exit();
        } else {
            $_SESSION['flash'] = "Ошибка при создании заказа. Пожалуйста, попробуйте еще раз.";
            header("Location: /Booking");
            exit();
        }
    }

    private function prepareBookingData(array $postData): array
    {
        $model = TourFactory::createTour();
        $Tours = $model->getBasketData();

        $totalSum = array_reduce($Tours, function($sum, $Tour) {
            return $sum + ($Tour['price'] * $Tour['quantity']);
        }, 0);

        return [
            'fio' => htmlspecialchars(urldecode($postData['fio'] ?? '')),
            'address' => htmlspecialchars(urldecode($postData['address'] ?? '')),
            'phone' => htmlspecialchars($postData['phone'] ?? ''),
            'email' => filter_var($postData['email'] ?? '', FILTER_SANITIZE_EMAIL),
            'payment_method' => htmlspecialchars($postData['payment_method'] ?? 'Не указан'),
            'created_at' => date("d-m-Y H:i:s"),
            'Tours' => $Tours,
            'all_sum' => $totalSum
        ];
    }

    private function sendBookingConfirmation(array $BookingData, $BookingId): bool
{
    if (empty($BookingData['email']) || !filter_var($BookingData['email'], FILTER_VALIDATE_EMAIL)) {
        error_log("Email не указан для заказа #{$BookingId}");
        return false;
    }

    // Проверяем валидность email
    if (!filter_var($BookingData['email'], FILTER_VALIDATE_EMAIL)) {
        error_log("Невалидный email: {$BookingData['email']} для заказа #{$BookingId}");
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration для Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Изменили на gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'soborovets@gmail.com'; // Ваш Gmail
        $mail->Password = 'djhc mmnm kfdr jrdd'; // Ваш пароль приложения
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Временно включаем дебаг для диагностики
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer [Level $level]: $str");
        };

        // Важно: для локальной разработки отключаем проверку SSL
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Email content - ИСПОЛЬЗУЕМ ВАШ Gmail В setFrom!
        $mail->setFrom('soborovets@gmail.com', 'PIZZA-221'); // Тот же email что в Username
        $mail->addAddress($BookingData['email']);
        $mail->addReplyTo('soborovets@gmail.com', 'PIZZA-221 Support');
        $mail->isHTML(true);
        $mail->Subject = 'Ваш заказ #' . $BookingId . ' в PIZZA-221';
        $mail->Body = $this->buildEmailBody($BookingData, $BookingId);
        $mail->AltBody = $this->buildTextEmailBody($BookingData, $BookingId);

        $result = $mail->send();
        
        if ($result) {
            error_log("✅ Email успешно отправлен с Gmail для заказа #{$BookingId} на адрес: {$BookingData['email']}");
        } else {
            error_log("❌ Не удалось отправить email для заказа #{$BookingId}");
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("🚨 Mailer Error для заказа #{$BookingId}: " . $e->getMessage());
        error_log("Email: {$BookingData['email']}");
        return false;
    }
}

    private function buildEmailBody(array $BookingData, $BookingId): string
{
    $ToursHtml = '';
    foreach ($BookingData['Tours'] as $Tour) {
        $totalPrice = number_format($Tour['price'] * $Tour['quantity'], 2);
        $ToursHtml .= <<<HTML
        <tr style="bBooking-bottom: 1px solid #e9ecef;">
            <td style="padding: 12px 8px; text-align: left;">
                <strong style="color: #343a40;">{$Tour['name']}</strong>
            </td>
            <td style="padding: 12px 8px; text-align: center; color: #6c757d;">
                {$Tour['quantity']} шт.
            </td>
            <td style="padding: 12px 8px; text-align: right; color: #6c757d;">
                {$Tour['price']} руб.
            </td>
            <td style="padding: 12px 8px; text-align: right; color: #667eea; font-weight: 600;">
                {$totalPrice} руб.
            </td>
        </tr>
        HTML;
    }

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f8f9fa 100%);
            background-attachment: fixed;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            bBooking-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            bBooking: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
        }
        
        .Booking-title {
            font-size: 24px;
            margin: 20px 0 10px;
            font-weight: 600;
        }
        
        .Booking-number {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .email-content {
            padding: 30px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #667eea;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            bBooking-bottom: 2px solid #667eea;
        }
        
        .Booking-table {
            width: 100%;
            bBooking-collapse: collapse;
            margin: 20px 0;
            background: white;
            bBooking-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .Booking-table th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px 8px;
            text-align: left;
            font-weight: 600;
            color: #343a40;
            bBooking-bottom: 2px solid #667eea;
        }
        
        .Booking-table tfoot tr {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
        }
        
        .Booking-table tfoot td {
            padding: 15px 8px;
            text-align: right;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        
        .info-item {
            background: rgba(248, 249, 250, 0.8);
            padding: 15px;
            bBooking-radius: 12px;
            bBooking-left: 4px solid #667eea;
        }
        
        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #343a40;
            font-weight: 500;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            bBooking-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .email-footer {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }
        
        .social-link {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            color: #667eea;
            transform: translateY(-2px);
        }
        
        .contact-info {
            margin: 20px 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .bubble {
            position: absolute;
            bBooking-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite linear;
        }
        
        .bubble-1 {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .bubble-2 {
            width: 60px;
            height: 60px;
            top: 70%;
            left: 85%;
            animation-delay: 5s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0) rotate(0deg);
            }
            25% {
                transform: translateY(-15px) translateX(8px) rotate(3deg);
            }
            50% {
                transform: translateY(8px) translateX(-12px) rotate(-2deg);
            }
            75% {
                transform: translateY(-10px) translateX(-8px) rotate(2deg);
            }
        }
        
        @media (max-width: 480px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .email-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="bubble bubble-1"></div>
    <div class="bubble bubble-2"></div>
    
    <div class="email-container">
        <div class="email-header">
            <div class="logo">
                <img src="/assets/image/TT.png" alt="Bubble Pizza" class="logo-icon">
                BUBBLE PIZZA
            </div>
            <h1 class="Booking-title">Спасибо за ваш заказ!</h1>
            <p class="Booking-number">Номер заказа: <strong>#{$BookingId}</strong></p>
            <div style="margin-top: 15px;">
                <span class="status-badge">✓ Принят в обработку</span>
            </div>
        </div>
        
        <div class="email-content">
            <div class="section">
                <h2 class="section-title">📋 Детали заказа</h2>
                <p style="color: #6c757d; margin-bottom: 20px;">Дата заказа: {$BookingData['created_at']}</p>
                
                <table class="Booking-table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Товар</th>
                            <th style="text-align: center;">Кол-во</th>
                            <th style="text-align: right;">Цена</th>
                            <th style="text-align: right;">Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$ToursHtml}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; padding-right: 15px;">Итого:</td>
                            <td style="text-align: right; padding-right: 15px;">
                                <strong>{$BookingData['all_sum']} руб.</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="section">
                <h2 class="section-title">🚚 Информация о доставке</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ФИО</div>
                        <div class="info-value">{$BookingData['fio']}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Телефон</div>
                        <div class="info-value">{$BookingData['phone']}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Адрес доставки</div>
                        <div class="info-value">{$BookingData['address']}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Способ оплаты</div>
                        <div class="info-value">{$BookingData['payment_method']}</div>
                    </div>
                </div>
            </div>
            
            <div style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); padding: 20px; bBooking-radius: 12px; bBooking-left: 4px solid #ffc107;">
                <h3 style="color: #856404; margin: 0 0 10px; font-size: 16px;">💡 Важная информация</h3>
                <p style="color: #856404; margin: 0; font-size: 14px;">
                    Мы уже начали готовить ваш заказ! Ожидайте звонка от курьера для подтверждения времени доставки.
                </p>
            </div>
        </div>
        
        <div class="email-footer">
            <h3 style="margin: 0 0 20px;">BUBBLE PIZZA</h3>
            
            <div class="contact-info">
                <div>📍 г. Кемерово, ул. Тухочевского, 32</div>
                <div>📞 +7 (999) 777-99-71</div>
                <div>✉️ info@bubblepizza.ru</div>
            </div>
            
            <div class="social-links">
                <a href="https://vk.com" class="social-link" target="_blank">
                    <span style="font-size: 18px;">VK</span>
                </a>
                <a href="https://instagram.com" class="social-link" target="_blank">
                    <span style="font-size: 18px;">Instagram</span>
                </a>
                <a href="https://telegram.org" class="social-link" target="_blank">
                    <span style="font-size: 18px;">Telegram</span>
                </a>
            </div>
            
            <div style="margin-top: 20px; padding-top: 20px; bBooking-top: 1px solid rgba(255,255,255,0.2);">
                <p style="margin: 0; font-size: 12px; opacity: 0.8;">
                    &copy; 2025 «Bubble Pizza» | Все права защищены<br>
                    Разработано студентами группы ИС-221
                </p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

    private function buildTextEmailBody(array $BookingData, $BookingId): string
    {
        $ToursText = '';
        foreach ($BookingData['Tours'] as $Tour) {
            $totalPrice = $Tour['price'] * $Tour['quantity'];
            $ToursText .= "{$Tour['name']} - {$Tour['quantity']} x {$Tour['price']} руб. = {$totalPrice} руб.\n";
        }

        return <<<TEXT
Спасибо за ваш заказ в PIZZA-221!

Номер заказа: #{$BookingId}
Дата заказа: {$BookingData['created_at']}

Состав заказа:
{$ToursText}

Итого: {$BookingData['all_sum']} руб.

Информация о доставке:
ФИО: {$BookingData['fio']}
Адрес: {$BookingData['address']}
Телефон: {$BookingData['phone']}
Способ оплаты: {$BookingData['payment_method']}

Если у вас есть вопросы, свяжитесь с нами.

С уважением,
Команда Bubble Pizza!
TEXT;
    }
}