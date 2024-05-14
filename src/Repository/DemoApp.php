<?php

namespace App\Repository;

class DemoApp
{

    private string $name;

    public function __construct(string $name )
    {
        $this->name = $name;
    }

    public function hey( mixed $caste )
    {
        return $this->name . $caste;
    }

}