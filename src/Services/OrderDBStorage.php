<?php

namespace App\Services;

use PDO;

class BookingDBStorage extends DBStorage implements ISaveStorage
{
    public function saveData(string $name, array $data): bool
    {
        global $user_id;
        $sql = "INSERT INTO `Bookings`
        (`fio`, `addres`, `phone`, `email`, `all_sum`, `payment_method`, `user_id`, `status`) 
        VALUES (:fio, :addres, :phone, :email, :sum, :payment_method, :idUser, 1 )";

        $sth = $this->connection->prepare($sql);

        $result = $sth->execute([
            'fio' => $data['fio'],
            'addres' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'sum' => $data['all_sum'],
            'payment_method' => $data['payment_method'],
            'idUser' => $user_id,
        ]);

        // получаем идентификатор добавленного заказа
        $idBooking = $this->connection->lastInsertId();
        // добавляем позиции заказа (заказанные товары)
        $this->saveItems($idBooking, $data['Tours']);

        return $result;
    }

    /*
    добавляет позиции заказа в таблицу Booking_item
    */
    public function saveItems(int $idBooking, array $Tours): bool
    {
        foreach ($Tours as $Tour) {
            $id = $Tour['id'];
            $price = $Tour['price'];
            $quantity = $Tour['quantity'];
            $sum = $price * $quantity;

            // Обрати внимание на исправление: УБРАНА ЗАПЯТАЯ ПОСЛЕ `sum_item`
            $sql = "INSERT INTO `Booking_item`
                (`Booking_id`, `Tour_id`, `count_item`, `price_item`, `sum_item`) 
                VALUES 
                (:id_Booking, :id_Tour, :count, :price, :sum)";

            $sth = $this->connection->prepare($sql);

            $sth->execute([
                'id_Booking' => $idBooking,
                'id_Tour' => $id,
                'count' => $quantity,
                'price' => $price,
                'sum' => $sum
            ]);
        }
        return true;
    }

   public function getBookingById(int $BookingId): ?array
    {
        // Шаг 1: Получаем основные данные о заказе
        $stmt = $this->connection->prepare("SELECT * FROM Bookings WHERE id = ?");
        $stmt->execute([$BookingId]);
        $Booking = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$Booking) {
            return null; // Заказ не найден
        }

        // Шаг 2: Получаем список товаров в заказе,
        // ИСПРАВЛЕНИЕ: Используем JOIN, чтобы получить название товара из таблицы `Tours`
        $stmt = $this->connection->prepare("
            SELECT oi.*, p.name 
            FROM Booking_item oi
            JOIN Tours p ON oi.Tour_id = p.id
            WHERE oi.Booking_id = ?
        ");
        $stmt->execute([$BookingId]);
        $Tours = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Код для получения истории статусов (если он у вас есть)

        // Объединяем данные
        $Booking['Tours'] = $Tours;
        $Booking['history'] = []; // или ваш код для истории

        return $Booking;
    }
}