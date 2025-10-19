<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Services\ValidateBookingData;

class BookingDataTest extends TestCase 
{
    private array $validData; // Изменим название на "validData", чтобы было яснее

    public function setUp(): void
    {
        parent::setUp(); // Вызов родительского setUp - хорошая практика
        session_start(); // Запускаем сессию
        $_SESSION = [];  // Очищаем сессию перед каждым тестом
        
        // Определяем базовый набор ВАЛИДНЫХ данных
        $this->validData = [
            'fio' => "Иванов Иван Иванович", // Более полные ФИО
            'address' => "Кемерово, ул. Тухачевского 32, кв 15", // Длиннее 10
            'phone' => "89007009911", // Валидный телефон
            'email' => "ivanov@example.com", // Валидный email
            'passport' => "1234 567890", // Валидные паспортные данные
        ];
    }

    // Тест: Все данные валидны
    public function testValidateBookingDataWithValidData(): void
    {
        $this->assertTrue(ValidateBookingData::validate($this->validData));
        $this->assertFalse(isset($_SESSION['flash']));
    }

    // Тест: ФИО не заполнено
    public function testFioMissing(): void
    {
        $data = $this->validData;
        unset($data['fio']); // Удаляем ключ
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Незаполнено поле ФИО.", $_SESSION['flash']);
    }

    // Тест: Адрес не заполнен (или пустая строка)
    public function testAddressEmpty(): void
    {
        $data = $this->validData;
        $data['address'] = ''; // Пустая строка после trim() будет 0 символов
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Поле адреса должно быть более 10 символов (но не более 200).", $_SESSION['flash']);
    }
    
    // Тест: Адрес слишком короткий
    public function testAddressTooShort(): void
    {
        $data = $this->validData;
        $data['address'] = 'Короткий'; // 8 символов
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Поле адреса должно быть более 10 символов (но не более 200).", $_SESSION['flash']);
    }

    // Тест: Телефон отсутствует
    public function testPhoneMissing(): void
    {
        $data = $this->validData;
        unset($data['phone']);
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Незаполнено поле Телефон.", $_SESSION['flash']);
    }

    // Тест: Неверный формат телефона (не 11 цифр)
    public function testInvalidPhoneLength(): void
    {
        $data = $this->validData;
        $data['phone'] = "8923650992"; // 10 цифр
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Неверный номер телефона.", $_SESSION['flash']);
    }

    // Тест: Неверный формат телефона (не начинается с 7 или 8)
    public function testInvalidPhoneStart(): void
    {
        $data = $this->validData;
        $data['phone'] = "19007009911"; // Начинается с 1
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Неверный номер телефона.", $_SESSION['flash']);
    }
    
    // Тест: Email отсутствует
    public function testEmailMissing(): void
    {
        $data = $this->validData;
        unset($data['email']);
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Неправильно заполнено поле Емайл.", $_SESSION['flash']);
    }

    // Тест: Невалидный формат Email
    public function testInvalidEmailFormat(): void
    {
        $data = $this->validData;
        $data['email'] = "invalid"; // Невалидный email
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Неправильно заполнено поле Емайл.", $_SESSION['flash']);
    }

    // Тест: Пустой Email
    public function testEmailEmpty(): void
    {
        $data = $this->validData;
        $data['email'] = ""; // Пустой email
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Неправильно заполнено поле Емайл.", $_SESSION['flash']);
    }

    // Тест: Паспортные данные отсутствуют
    public function testPassportMissing(): void
    {
        $data = $this->validData;
        unset($data['passport']);
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Незаполнено поле Паспортные данные.", $_SESSION['flash']);
    }

    // Тест: Паспортные данные слишком короткие
    public function testPassportTooShort(): void
    {
        $data = $this->validData;
        $data['passport'] = "1234 123"; // 7 цифр вместо 10
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Неверный формат паспортных данных.", $_SESSION['flash']);
    }

    // Тест: Паспортные данные содержат буквы
    public function testPassportWithLetters(): void
    {
        $data = $this->validData;
        $data['passport'] = "1234 AB1234"; // Содержит буквы
        $this->assertFalse(ValidateBookingData::validate($data));
        $this->assertSame("Неверный формат паспортных данных.", $_SESSION['flash']);
    }

    // Тест: Валидные паспортные данные
    public function testValidPassport(): void
    {
        $data = $this->validData;
        $data['passport'] = "1234 567890"; // Правильный формат
        $this->assertTrue(ValidateBookingData::validate($data));
        $this->assertFalse(isset($_SESSION['flash']));
    }

    public function tearDown(): void
    {
        if (isset($_SESSION['flash'])) {
            unset($_SESSION['flash']); // Очищаем flash-сообщение после каждого теста
        }
        session_write_close(); // Закрываем сессию
        parent::tearDown();
    }
}