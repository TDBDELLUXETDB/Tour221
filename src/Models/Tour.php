<?php 
namespace App\Models;

use App\Services\ILoadStorage;

class Tour {
    private ILoadStorage $dataStorage;
    private string $nameResource;
    
    public function __construct(ILoadStorage $service, string $name)
    {
        $this->dataStorage = $service;
        $this->nameResource = $name;
    }

    public function loadData(): ?array {
        return $this->dataStorage->loadData( $this->nameResource ); 
    }

    public function getBasketData(): array {
        if (!isset($_SESSION['basket'])) {
            $_SESSION['basket'] = [];
        }
        $Tours = $this->loadData();
        $basketTours= [];

        foreach ($Tours as $Tour) {
            $id = $Tour['id'];

            if (array_key_exists($id, $_SESSION['basket'])) {
                
                $quantity = $_SESSION['basket'][$id]['quantity'];

                $name = $Tour['name'];
                $price= $Tour['price'];


                $sum  = $price * $quantity;

                $basketTours[] = array( 
                    'id' => $id, 
                    'name' => $name, 
                    'quantity' => $quantity,
                    'price' => $price,
                    'sum' => $sum,
                );
            }
        }

        return $basketTours;
    }

        /* 
        Подсчет общей суммы заказа (товаров в корзине)
    */
    public function getAllSum(?array $Tours): float {
        $all_sum =0;
        foreach ($Tours as $Tour) {
            $price = $Tour['price'];
		    $quantity = $Tour['quantity'];

            $all_sum += $price * $quantity;
	    }
        return $all_sum;
    }
}