<?php
namespace App\Views;

use App\Views\BaseTemplate;

class TourTemplate extends BaseTemplate
{
    public static function getAllTemplate(array $tours, int $basketCount, array $searchParams = []): string
    {
        $template = parent::getTemplate();

        // Параметры поиска
        $fromCityName = $searchParams['from_city'] ?? '';
        $toCityName = $searchParams['to_city'] ?? '';
        $dates = $searchParams['dates'] ?? '';

        // Отладочная информация
        error_log("🎯 TourTemplate - Search params: " . print_r($searchParams, true));
        error_log("🎯 TourTemplate - Total tours: " . count($tours));

        $toursCount = count($tours);
        $title = "Туры" . 
                ($fromCityName ? " из {$fromCityName}" : "") . 
                ($toCityName ? " в {$toCityName}" : "") . 
                " - Travel Dream";

        $content = <<<HTML
<div class="search-results-hero text-center">
    <h1 class="results-title">Найденные туры</h1>
    <div class="search-params">
        <span><strong>Откуда:</strong> {$fromCityName}</span>
        <span><strong>Куда:</strong> {$toCityName}</span>
        <span><strong>Даты:</strong> {$dates}</span>
    </div>
    <p class="results-count">Найдено туров: <span class="count-badge">{$toursCount}</span></p>
</div>
<div class="tours-list-section">
    <div class="tours-container">
HTML;

        if (empty($tours)) {
            $content .= <<<HTML
<div class="no-tours-found">
    <h3>😔 Туры не найдены</h3>
    <p>Попробуйте изменить параметры поиска или <a href="/">вернуться на главную</a></p>
</div>
HTML;
        } else {
            foreach ($tours as $tour) {
                $id = $tour['id'] ?? 0;
                $name = htmlspecialchars($tour['name'] ?? '—');
                $destination = htmlspecialchars($tour['destination'] ?? '—');
                $description = htmlspecialchars($tour['description'] ?? '');
                $price = number_format($tour['price'] ?? 0, 0, '.', ' ');
                $duration = $tour['duration_days'] ?? '—';
                $departureDate = $tour['departure_date'] ?? '—';
                $image = $tour['image'] ?? 'default.jpg';
                $rating = $tour['rating'] ?? 0;
                $reviews = $tour['reviews'] ?? 0;
                $stars = str_repeat('★', floor($rating)) . str_repeat('☆', 5 - floor($rating));

                $content .= <<<HTML
<div class="tour-card">
    <div class="tour-image">
        <img src="/assets/images/{$image}" alt="{$name}">
    </div>
    <div class="tour-info">
        <h3 class="tour-name">{$name}</h3>
        <p class="tour-destination">{$destination}</p>
        <p class="tour-description">{$description}</p>
        <div class="tour-meta">
            <span class="duration"><i class="fas fa-clock"></i> {$duration} дней</span>
            <span class="departure"><i class="fas fa-plane-departure"></i> {$departureDate}</span>
            <span class="rating">{$stars} ({$reviews})</span>
        </div>
        <div class="tour-price">
            <span class="price">{$price} ₽</span>
            <form action="/basket" method="POST">
                <input type="hidden" name="id" value="{$id}">
                <button type="submit" class="btn-book"><i class="fas fa-shopping-cart"></i> В корзину</button>
            </form>
        </div>
    </div>
</div>
HTML;
            }
        }

        $content .= '</div></div>';

        // CSS стиль
        $content .= <<<HTML
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
body { font-family: 'Arial', sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
.search-results-hero { 
    padding: 40px 20px; 
    background: linear-gradient(135deg, #0077B6, #00B4D8); 
    color: #fff; 
    text-align: center; 
    border-radius: 0 0 20px 20px; 
    margin-bottom: 40px; 
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.search-results-hero .results-title { 
    font-size: 2.5rem; 
    font-weight: 700; 
    margin-bottom: 20px; 
}
.search-params { 
    display: flex; 
    justify-content: center; 
    flex-wrap: wrap; 
    gap: 20px; 
    margin-bottom: 20px;
}
.search-params span { 
    background: rgba(255,255,255,0.2); 
    padding: 8px 16px; 
    border-radius: 20px; 
    font-size: 0.9rem;
}
.results-count { 
    font-size: 1.2rem; 
    margin: 0;
}
.count-badge { 
    background: #FFC300; 
    color: #000; 
    padding: 4px 12px; 
    border-radius: 15px; 
    font-weight: bold;
}
.tours-container { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
    gap: 30px; 
    padding: 0 20px 50px; 
    max-width: 1200px; 
    margin: 0 auto;
}
.tour-card { 
    background: #fff; 
    border-radius: 20px; 
    overflow: hidden; 
    box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
    display: flex; 
    flex-direction: column; 
    transition: transform 0.3s, box-shadow 0.3s; 
}
.tour-card:hover { 
    transform: translateY(-5px); 
    box-shadow: 0 15px 30px rgba(0,0,0,0.15); 
}
.tour-image img { 
    width: 100%; 
    height: 220px; 
    object-fit: cover; 
    transition: transform 0.6s ease;
}
.tour-card:hover .tour-image img { 
    transform: scale(1.05); 
}
.tour-info { 
    padding: 20px; 
    display: flex; 
    flex-direction: column; 
    justify-content: space-between; 
    flex: 1; 
}
.tour-name { 
    font-size: 20px; 
    font-weight: bold; 
    margin: 0 0 5px; 
    color: #0077B6;
}
.tour-destination { 
    font-size: 16px; 
    color: #555; 
    margin: 0 0 15px; 
    font-weight: 500;
}
.tour-description { 
    flex-grow: 1; 
    font-size: 14px; 
    color: #333; 
    margin-bottom: 15px; 
    line-height: 1.5;
}
.tour-meta { 
    display: flex; 
    justify-content: space-between; 
    flex-wrap: wrap; 
    font-size: 13px; 
    color: #777; 
    margin-bottom: 15px; 
    gap: 5px; 
}
.tour-meta i { 
    margin-right: 5px; 
    color: #0077B6; 
}
.tour-price { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-top: auto; 
    padding-top: 15px;
    border-top: 1px solid #eee;
}
.tour-price .price { 
    font-size: 18px; 
    font-weight: bold; 
    color: #00B4D8; 
}
.btn-book { 
    padding: 8px 14px; 
    border: none; 
    border-radius: 8px; 
    cursor: pointer; 
    background: #0077B6; 
    color: #fff; 
    font-size: 14px; 
    transition: background 0.3s; 
    display: flex;
    align-items: center;
    gap: 5px;
}
.btn-book:hover { 
    background: #005f87; 
}
.no-tours-found {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    grid-column: 1 / -1;
}
.no-tours-found h3 {
    color: #0077B6;
    margin-bottom: 15px;
}
.no-tours-found a {
    color: #00B4D8;
    text-decoration: none;
    font-weight: 500;
}
.no-tours-found a:hover {
    text-decoration: underline;
}
.floating-basket-btn { 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    position: fixed; 
    bottom: 20px; 
    right: 20px; 
    background: #00B4D8; 
    color: #fff; 
    padding: 12px 16px; 
    border-radius: 50px; 
    z-index: 1000; 
    font-size: 16px; 
    text-decoration: none; 
    box-shadow: 0 4px 15px rgba(0,180,216,0.3);
    transition: all 0.3s;
}
.floating-basket-btn:hover { 
    background: #0077B6; 
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,180,216,0.4);
    color: #fff;
}
.floating-basket-btn .basket-count { 
    margin-left: 8px; 
    font-weight: bold; 
}
</style>
HTML;

        $basketDisplayStyle = $basketCount > 0 ? 'flex' : 'none';
        $content .= "<a href='/Booking' class='floating-basket-btn' style='display: {$basketDisplayStyle};'>
                        <i class='fas fa-shopping-basket'></i>
                        <span class='basket-count'>{$basketCount}</span>
                     </a>";

        // Вставляем контент в шаблон
        $template = str_replace('%1$s', $title, $template);
        $template = str_replace('%2$s', $content, $template);

        return $template;
    }
}