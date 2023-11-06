<?php

namespace Reklamafia\Iiko;

class IikoCustomer
{
    public $id;
    public $name;
    public $phone;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
