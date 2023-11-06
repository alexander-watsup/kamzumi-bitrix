<?php

namespace Reklamafia\Iiko;

class IikoOrder
{

    public $id;
    public $date;
    public $phone;
    public $comment;
    public $isSelfService = false;
    public $customer;
    public $products = [];
    public $address = [
        "city" => "",
        "street" => "",
        "home" => "",
        "housing" => "",
        "apartment" => "",
        "comment" => ""
    ];
    public $paymentItems = [];
    public $personsCount = 1;

    /**
     * Order constructor.
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->date = date('Y-m-d H:i:s');
    }

    /**
     * @param Product $product
     */
    public function setProduct(IikoProduct $product)
    {
        array_push($this->products, $product);
    }

    /**
     * @param array $products
     */
    public function setProducts(array $products)
    {
        foreach ($products as $key => $product) {
            $this->setProduct($product);
        }
    }

    public function setCustomer(IikoCustomer $customer)
    {
        $this->customer = $customer;
    }

    public function setPaymentItems(array $paymentItem)
    {
        $this->paymentItems[] = $paymentItem;
    }

    /**
     * @param array $address
     */
    public function setAddress(array $address)
    {
        if (isset($address['city']))
            $this->address['city'] = $address['city'];

        if (isset($address['street']))
            $this->address['street'] = $address['street'];

        if (isset($address['home']))
            $this->address['home'] = $address['home'];

        if (isset($address['housing']))
            $this->address['housing'] = $address['housing'];

        if (isset($address['apartment']))
            $this->address['apartment'] = $address['apartment'];
    }

    /**
     * @param $comment
     */
    public function setComment($comment)
    {
        $this->address['comment'] = (string)$comment;
    }

    public function toText()
    {

        /*
        $postParams = [
            /'customer' => $this->customer,
            'order' => [
                /'id' => '',
                /'date' =>  $this->date,
                /'phone' => $this->customer->phone,
                /'comment' => $this->comment,
                /'isSelfService' => $this->isSelfService,
                /'personsCount' => $this->personsCount,
                'items' => $this->products,
                /'address' => $this->address,
                'paymentItems' => $this->paymentItems
            ]
        ];
        */

        $result = "Клиент: " . $this->customer->name . " " . $this->customer->phone . "\r\n";

        $result .= "Заказ " . $this->personsCount . " перс.: " . "\r\n";
        foreach ($this->products as &$product) {
            $result .= round($product->amount) . " " . $product->name. "  ";
        }
        $result .=  "\r\n";

        if ($this->isSelfService) {
            $result .= "Самовывоз\r\n";
        } else {
            $result .= "Доставка: " . $this->address['city'] . ", ул. " . $this->address['street'] . ", д. " . $this->address['home'] . ", корп. " . $this->address['housing'] . ", кв\оф. " . $this->address['apartment'] . "\r\n";
        }

        $result .= "Комментарий клиента: " . $this->comment . "\r\n";

        //$result .= "\r\n" . json_encode($postParams, JSON_UNESCAPED_UNICODE);
        return $result;
    }
}
