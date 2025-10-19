<?php

namespace App\Views;

use App\Views\BaseTemplate;

class HomeTemplate extends BaseTemplate
{
    public static function getTemplate(): string
    {
        $template = parent::getTemplate();

        // Защита от ошибок sprintf
        $template = str_replace('%1$s', '___PLACEHOLDER_TITLE___', $template);
        $template = str_replace('%2$s', '___PLACEHOLDER_CONTENT___', $template);
        $template = str_replace('%', '%%', $template);
        $template = str_replace('___PLACEHOLDER_TITLE___', '%1$s', $template);
        $template = str_replace('___PLACEHOLDER_CONTENT___', '%2$s', $template);

       $content = <<<'HTML'

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap');

body {
    font-family: 'Montserrat', sans-serif;
    /* Улучшенный, более глубокий фон */
    background: linear-gradient(135deg, #00B4D8 0%%, #023E8A 100%%);
    background-size: cover;
    background-attachment: fixed;
    color: #212529;
    margin: 0;
    padding: 0;
}

/* Общий контейнер для контента */
.container-lg {
    max-width: 1200px;
    padding: 0 15px;
}

/* ===== HERO (Glassmorphism) ===== */
.hero {
    position: relative;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    padding: 5rem 2.5rem;
    border-radius: 20px;
    margin-bottom: 3rem;
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    text-align: center;
    overflow: hidden;
    animation: fadeInDown 1s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.hero-title {
    font-size: clamp(2.5rem, 5vw, 4.5rem);
    font-weight: 900;
    background: linear-gradient(90deg,#0077B6,#00B4D8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: textGlow 2s ease-in-out infinite alternate;
    margin-bottom: 1rem;
}

.hero-subtitle {
    font-size: clamp(1rem, 2vw, 1.5rem);
    font-weight: 500;
    color: #495057;
    margin-bottom: 3rem;
}

/* Светящаяся анимация заголовка */
@keyframes textGlow {
    from { text-shadow: 0 0 1px rgba(0,119,182,0.3); }
    to { text-shadow: 0 0 8px rgba(0,180,216,0.8); }
}

/* ===== Поисковая форма (Новая структура) ===== */
.search-form-group {
    background-color: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    max-width: 1000px;
    margin: 0 auto;
    transition: box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.search-form-group:hover { box-shadow: 0 10px 35px rgba(0,119,182,0.3); }

/* Ряды ввода для группировки */
.input-row {
    display: flex;
    gap: 15px;
}

/* форма элементы */
.search-form-group .form-control {
    border: 1px solid #ced4da;
    border-radius: 10px;
    padding: 15px 20px;
    flex: 1;
    min-width: 150px;
    transition: border-color 0.3s, box-shadow 0.3s;
}
.search-form-group .form-control:focus {
    border-color: #00B4D8;
    box-shadow: 0 0 0 0.25rem rgba(0,180,216,0.3);
}

.btn-search {
    background: linear-gradient(90deg,#0077B6,#00B4D8);
    border:none;
    color:white;
    padding:15px 40px;
    border-radius:10px;
    font-weight:700;
    font-size: 1.1rem;
    transition:transform 0.3s,box-shadow 0.3s;
    width: 100%;
    max-width: 350px;
    align-self: center;
}
.btn-search:hover {
    background: linear-gradient(90deg,#00B4D8,#0077B6);
    transform:translateY(-3px) scale(1.01);
    box-shadow:0 12px 25px rgba(0,119,182,0.5);
}

/* ===== Стили для автокомплита городов ===== */
.form-group.position-relative {
    flex: 1;
    position: relative;
}

.cities-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-radius: 0 0 10px 10px;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.cities-dropdown.show {
    display: block;
}

.city-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.2s;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.city-item:hover {
    background-color: #f8f9fa;
}

.city-item:last-child {
    border-bottom: none;
}

.city-name {
    font-weight: 500;
    color: #212529;
}

.city-country {
    font-size: 0.85rem;
    color: #6c757d;
}

.city-code {
    font-size: 0.8rem;
    color: #0077B6;
    font-weight: 600;
    background: #e3f2fd;
    padding: 2px 6px;
    border-radius: 4px;
}

.input-loader {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Подсветка совпадения в поиске */
.highlight {
    background-color: #fff3cd;
    font-weight: 600;
    padding: 1px 2px;
    border-radius: 2px;
}

/* Секции */
.section {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 4rem 3rem;
    margin-bottom: 3rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    animation: fadeInUp 1s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    animation-play-state: paused;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.section h2 {
    font-size: clamp(1.8rem, 3vw, 2.8rem);
    font-weight: 800;
    color: #023E8A;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 15px;
}
.section h2::after {
    content: '';
    position: absolute;
    left: 0; bottom: 0;
    width: 80px; height: 5px;
    background: linear-gradient(90deg,#00B4D8,#0077B6);
    border-radius: 3px;
}

/* ===== Эксклюзивные предложения (Hit Cards) ===== */
.hits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    text-align: left;
}
.hit {
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.1);
    transition: transform 0.4s, box-shadow 0.4s;
    position: relative;
    overflow: hidden;
    color: #fff;
    animation: fadeIn 1s ease-out;
    animation-play-state: paused;
}
.hit:before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 1;
    opacity: 0.95;
    border-radius: 16px;
}
.hit > * { position: relative; z-index: 2; }
.hit h3 { font-size: 2rem; font-weight: 800; margin-bottom: 5px; }
.hit p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 5px; }
.hit .price { font-size: 1.7rem; font-weight: 900; color: #FFC300; margin-top: 15px; }
.hit:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,119,182,0.4); }

.hit-1:before { background: linear-gradient(135deg, #023E8A, #0077B6); }
.hit-2:before { background: linear-gradient(135deg, #48CAE4, #00B4D8); }
.hit-3:before { background: linear-gradient(135deg, #D62828, #F77F00); }

/* ===== Карточки туров ===== */
.tours-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
    gap: 30px;
}
.card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: transform 0.4s, box-shadow 0.4s;
    animation: fadeIn 1s ease-out;
    animation-play-state: paused;
}
.card:hover { transform: translateY(-10px); box-shadow: 0 20px 45px rgba(0,119,182,0.3); }
.card-img-top { height: 250px; object-fit: cover; transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
.card:hover .card-img-top { transform: scale(1.1); }
.card-body { padding: 25px; }
.card-body h5 { font-size: 1.6rem; font-weight: 700; color: #0077B6; margin-bottom: 5px; }
.price { font-size: 1.7rem; font-weight: 900; color: #D62828; margin-top: 15px; display: block; }

/* ===== Отзывы ===== */
.reviews { display: grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap: 25px; }
.review {
    background-color: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border-bottom: 5px solid #00B4D8;
    transition: transform 0.3s, box-shadow 0.3s;
    animation: fadeIn 1s ease-out;
    animation-play-state: paused;
}
.review:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,180,216,0.3); }
.review .stars { color:#FFC300; font-size:1.6rem; margin-bottom:10px; }
.review p { font-style: italic; color: #495057; }
.review .author { margin-top: 15px; font-weight: 600; color: #023E8A; }

/* ===== Медиа-запросы (Адаптивность) ===== */
@media (max-width: 992px) {
    .hero { padding: 4rem 1.5rem; }
    .section { padding: 3rem 1.5rem; }
}

@media (max-width: 768px) {
    .hero-title { font-size: 2.5rem; }
    .hero-subtitle { font-size: 1.1rem; }
    
    .input-row {
        flex-direction: column;
        gap: 0;
    }
    .search-form-group .form-control {
        margin-bottom: 15px;
    }
    .search-form-group .btn-search {
        max-width: 100%;
        font-size: 1rem;
    }
    
    .form-group.position-relative {
        margin-bottom: 15px;
    }
}

/* ===== Анимации ===== */
@keyframes fadeInDown { from{opacity:0;transform:translate3d(0,-30px,0);} to{opacity:1;transform:translate3d(0,0,0);} }
@keyframes fadeInUp { from{opacity:0;transform:translate3d(0,30px,0);} to{opacity:1;transform:translate3d(0,0,0);} }
@keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
</style>

<!-- HERO -->
<div class="container-fluid">
  <div class="hero">
    <h1 class="hero-title">Путешествие мечты начинается здесь</h1>
    <p class="hero-subtitle">Исследуйте мир с комфортом, уверенностью и лучшими ценами</p>

    <!-- Форма с автокомплитом городов -->
    <form class="search-form-group" id="searchForm">
        <!-- Первый ряд: Откуда и Куда -->
        <div class="input-row">
            <div class="form-group position-relative">
                <input type="text" class="form-control city-input" id="fromCity" 
                       placeholder="Откуда" required autocomplete="off"
                       data-target="fromCity">
                <div class="cities-dropdown" id="fromCityDropdown"></div>
                <div class="input-loader" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                </div>
            </div>
            <div class="form-group position-relative">
                <input type="text" class="form-control city-input" id="toCity" 
                       placeholder="Куда" required autocomplete="off"
                       data-target="toCity">
                <div class="cities-dropdown" id="toCityDropdown"></div>
                <div class="input-loader" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Второй ряд: Даты и Ночи -->
        <div class="input-row">
            <input type="text" class="form-control" id="dateRange" placeholder="Выберите даты" autocomplete="off" required>
            <input type="text" class="form-control" id="nightsCount" placeholder="Количество ночей" readonly>
        </div>
        
        <button type="submit" class="btn btn-search"><i class="fas fa-paper-plane me-2"></i>Найти тур</button>
    </form>
  </div>
</div>

<!-- КОНТЕНТ -->
<div class="container-lg">
  <section class="section">
    <h2>✨ Эксклюзивные предложения</h2>
    <div class="hits-grid">
      <div class="hit hit-1">
        <h3>Мальдивы</h3>
        <p>Скидка 30%% на ноябрь</p>
        <p class="price">от 215 000 руб.</p>
      </div>
      <div class="hit hit-2">
        <h3>Греция</h3>
        <p>Круиз по островам Эгейского моря</p>
        <p class="price">от 120 000 руб.</p>
      </div>
      <div class="hit hit-3">
        <h3>Сейшелы</h3>
        <p>Неделя в 5* отеле по специальной цене</p>
        <p class="price">от 188 000 руб.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <h2>🗺️ Самые востребованные направления</h2>
    <div class="tours-grid">
      <div class="card">
        <img src="https://placehold.co/600x400/0077B6/FFFFFF?text=Турция" class="card-img-top" alt="Турция">
        <div class="card-body">
          <h5>Турция</h5>
          <p class="card-text">Пляжи Средиземноморья и древние руины</p>
          <p class="price">от 49 000 руб.</p>
        </div>
      </div>
      <div class="card">
        <img src="https://placehold.co/600x400/00B4D8/FFFFFF?text=ОАЭ" class="card-img-top" alt="ОАЭ">
        <div class="card-body">
          <h5>ОАЭ</h5>
          <p class="card-text">Роскошь, шопинг и сафари в пустыне</p>
          <p class="price">от 85 000 руб.</p>
        </div>
      </div>
      <div class="card">
        <img src="https://placehold.co/600x400/48CAE4/000000?text=Египет" class="card-img-top" alt="Египет">
        <div class="card-body">
          <h5>Египет</h5>
          <p class="card-text">Красное море, дайвинг и пирамиды</p>
          <p class="price">от 58 000 руб.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <h2>Отзывы клиентов 🗣️</h2>
    <div class="reviews">
      <div class="review">
        <div class="stars">★★★★★</div>
        <p>"Лучший отпуск! Всё идеально организовано, от бронирования до возвращения."</p>
        <div class="author">— Елена В.</div>
      </div>
      <div class="review">
        <div class="stars">★★★★☆</div>
        <p>"Очень понравилось, менеджер быстро подобрал тур под наш бюджет!"</p>
        <div class="author">— Светлана Р.</div>
      </div>
    </div>
  </section>
</div>

<!-- Flatpickr календарь -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/ru.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = 'http://localhost';
    
    // Инициализация Flatpickr
    flatpickr("#dateRange", {
        mode: "range",
        locale: "ru",
        dateFormat: "d.m.Y",
        minDate: "today",
        onClose: function(selectedDates) {
            if (selectedDates.length === 2) {
                const diff = Math.ceil((selectedDates[1] - selectedDates[0]) / (1000 * 60 * 60 * 24));
                document.getElementById("nightsCount").value = diff + " ночей";
            } else {
                document.getElementById("nightsCount").value = "";
            }
        },
        onChange: function(selectedDates) {
            if (selectedDates.length === 0 || selectedDates.length === 1) {
                document.getElementById("nightsCount").value = "";
            }
        }
    });

    // Автокомплит для городов
    class CityAutocomplete {
        constructor(inputElement, dropdownElement) {
            this.input = inputElement;
            this.dropdown = dropdownElement;
            this.loader = this.input.parentNode.querySelector('.input-loader');
            this.timeout = null;
            this.selectedCity = null;
            
            this.init();
        }
        
        init() {
            this.input.addEventListener('input', (e) => {
                this.handleInput(e.target.value);
            });
            
            this.input.addEventListener('focus', () => {
                if (this.input.value.length >= 2) {
                    this.handleInput(this.input.value);
                }
            });
            
            this.input.addEventListener('blur', () => {
                setTimeout(() => {
                    this.hideDropdown();
                }, 200);
            });
            
            document.addEventListener('click', (e) => {
                if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                    this.hideDropdown();
                }
            });
        }
        
        handleInput(query) {
            clearTimeout(this.timeout);
            
            if (query.length < 2) {
                this.hideDropdown();
                return;
            }
            
            this.showLoader();
            
            this.timeout = setTimeout(() => {
                this.searchCities(query);
            }, 300);
        }
        
        async searchCities(query) {
            try {
                console.log('🔍 Search started for:', query);
                
                const url = baseUrl + '/api/cities?query=' + encodeURIComponent(query) + '&limit=8';
                console.log('🌐 Fetch URL:', url);
                
                this.showLoader();
                
                const response = await fetch(url);
                console.log('📡 Response status:', response.status);
                console.log('📡 Response ok:', response.ok);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const cities = await response.json();
                console.log('🏙️ Found cities:', cities);
                console.log('🏙️ Cities length:', cities.length);
                
                this.displayCities(cities, query);
                this.hideLoader();
                
            } catch (error) {
                console.error('❌ Ошибка поиска городов:', error);
                console.error('Error details:', error.message);
                this.hideLoader();
                this.hideDropdown();
                
                // Показываем ошибку пользователю
                this.dropdown.innerHTML = '<div class="city-item text-muted">Ошибка загрузки городов</div>';
                this.showDropdown();
            }
        }
        
        displayCities(cities, query) {
            console.log('🖥️ Displaying cities:', cities);
            
            if (!cities || cities.length === 0) {
                console.log('📭 No cities to display');
                this.dropdown.innerHTML = '<div class="city-item text-muted">Городы не найдены</div>';
                this.showDropdown();
                return;
            }
            
            console.log('🎨 Rendering', cities.length, 'cities');
            
            this.dropdown.innerHTML = cities.map(city => {
                const highlightedName = this.highlightText(city.name, query);
                console.log('📝 City:', city.name, '->', highlightedName);
                
                return `
                    <div class="city-item" data-city-id="${city.id}" 
                         data-city-name="${city.name}" 
                         data-city-country="${city.country}">
                        <div>
                            <div class="city-name">${highlightedName}</div>
                            <div class="city-country">${city.country}</div>
                        </div>
                        ${city.iata_code ? `<div class="city-code">${city.iata_code}</div>` : ''}
                    </div>
                `;
            }).join('');
            
            console.log('🎯 Adding event listeners to', cities.length, 'items');
            this.addCityEventListeners();
            this.showDropdown();
        }
        
        highlightText(text, query) {
            const regex = new RegExp('(' + this.escapeRegex(query) + ')', 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }
        
        escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        addCityEventListeners() {
            const cityItems = this.dropdown.querySelectorAll('.city-item');
            cityItems.forEach(item => {
                item.addEventListener('click', () => {
                    const cityName = item.getAttribute('data-city-name');
                    const cityCountry = item.getAttribute('data-city-country');
                    
                    this.input.value = cityName + ' (' + cityCountry + ')';
                    this.selectedCity = {
                        id: item.getAttribute('data-city-id'),
                        name: cityName,
                        country: cityCountry
                    };
                    
                    this.hideDropdown();
                    this.input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        }
        
        showDropdown() {
            this.dropdown.classList.add('show');
        }
        
        hideDropdown() {
            this.dropdown.classList.remove('show');
        }
        
        showLoader() {
            if (this.loader) {
                this.loader.style.display = 'block';
            }
        }
        
        hideLoader() {
            if (this.loader) {
                this.loader.style.display = 'none';
            }
        }
        
        getSelectedCity() {
            return this.selectedCity;
        }
    }
    
    // Инициализация автокомплита
    const fromCityInput = document.getElementById('fromCity');
    const fromCityDropdown = document.getElementById('fromCityDropdown');
    const toCityInput = document.getElementById('toCity');
    const toCityDropdown = document.getElementById('toCityDropdown');
    
    const fromCityAutocomplete = new CityAutocomplete(fromCityInput, fromCityDropdown);
    const toCityAutocomplete = new CityAutocomplete(toCityInput, toCityDropdown);
    
    // Обработка отправки формы
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const fromCity = fromCityAutocomplete.getSelectedCity();
        const toCity = toCityAutocomplete.getSelectedCity();
        const dateRange = document.getElementById('dateRange').value.trim();

        if (!fromCity || !toCity || !dateRange) {
            alert('Пожалуйста, заполните все поля корректно');
            return;
        }

        // Берем только название города без страны
        const fromCityName = fromCity.name.split(' (')[0];
        const toCityName = toCity.name.split(' (')[0];

        const searchParams = new URLSearchParams();
        searchParams.append('from_city', fromCityName);
        searchParams.append('to_city', toCityName);
        searchParams.append('dates', dateRange);

        console.log('🔍 Search params:', {
            from_city: fromCityName,
            to_city: toCityName,
            dates: dateRange
        });

        window.location.href = baseUrl + '/Tours?' + searchParams.toString();
    });
    
    // Анимация появления элементов при скролле
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, {threshold: 0.1, rootMargin: '0px 0px -50px 0px'});
    
    document.querySelectorAll('.hero, .section, .card, .hit, .review').forEach(el => observer.observe(el));
});
</script>
HTML;

        return sprintf($template, "Главная страница - Travel Dream", $content);
    }
}