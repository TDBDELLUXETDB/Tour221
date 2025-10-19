<?php

namespace App\Services;

class ValidateBookingData
{
    public static function validate(array $data): bool
    {
        // Проверка ФИО
        if (empty($data['fio'])) {
            $_SESSION['flash'] = "Незаполнено поле ФИО.";
            return false;
        }

        // Проверка адреса
        if (empty($data['address']) || mb_strlen(trim($data['address']), 'UTF-8') < 10) {
            $_SESSION['flash'] = "Поле адреса должно быть более 10 символов (но не более 200).";
            return false;
        }

        // Проверка телефона
        if (empty($data['phone'])) {
            $_SESSION['flash'] = "Незаполнено поле Телефон.";
            return false;
        }
        
        $phone = trim($data['phone']);
        if (!preg_match('/^[78]\d{10}$/', $phone)) {
            $_SESSION['flash'] = "Неверный номер телефона.";
            return false;
        }

        // Проверка email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = "Неправильно заполнено поле Емайл.";
            return false;
        }

        // ПРОВЕРКА ПАСПОРТНЫХ ДАННЫХ
        if (empty($data['passport'])) {
            $_SESSION['flash'] = "Незаполнено поле Паспортные данные.";
            return false;
        }
        
        $passport = trim($data['passport']);
        $passportClean = preg_replace('/\p{Z}+/u', '', $passport); // Удаляем все пробельные символы
        
        // Проверяем длину строки и формат
        if (strlen($passportClean) !== 10 || !preg_match('/^[0-9]{10}$/', $passportClean)) {
            $_SESSION['flash'] = "Неверный формат паспортных данных.";
            return false;
        }

        return true;
    }
}