<?php

namespace Tests;
use PHPUnit\Framework\TestCase;
use App\Services\ValidateBookingData; // Убедитесь, что это правильный путь к вашему классу

class BookingDataTest extends TestCase 
{
    private array $validData; // Изменим название на "validData", чтобы было яснее

    public function setUp(): void
    {
        parent::setUp(); // Вызов родительского setUp - хорошая практика
        // Определяем базовый набор ВАЛИДНЫХ данных
        $this->validData = [
            'fio' => "Иванов Иван Иванович", // Более полные ФИО
            'address' => "Кемерово, ул. Тухачевского 32, кв 15", // Длиннее 10
            'phone' => "89007009911", // Валидный телефон
            'email' => "ivanov@example.com", // Валидный email
        ];
        
        // ВАЖНО: Так как ValidateBookingData::validate статический,
        // нам не нужно создавать экземпляр класса.
        // $_SESSION также будет очищаться между тестами PHPUnit, 
        // но лучше не полагаться на $_SESSION в юнит-тестах.
        // Для чистоты тестов, можно мокать $_SESSION или передавать ее в метод.
        // Но для этого примера, мы просто уберем создание объекта.
    }

    // Тест: Все данные валидны
    public function testValidateBookingDataWithValidData(): void
    {
        $this->assertTrue(ValidateBookingData::validate($this->validData));
        // Убедимся, что flash-сообщение не установлено при успехе (если это часть логики)
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
        $data['phone'] = "8900700991"; // 10 цифр
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

    public function tearDown(): void
    {
        if (isset($_SESSION['flash'])) {
            unset($_SESSION['flash']); // Очищаем flash-сообщение после каждого теста
        }
        parent::tearDown();
    }
}