<?php

namespace App;

use App\Repository\AllAnimal;
use App\Repository\DemoApp2;

class DogAnimal extends AllAnimal implements AnimalInterface
{

    public function __construct(string $p)
    {
    }

    public function bark()
    {
        $demoApp2 = new DemoApp2('sachin');
        return $demoApp2->hey($this);
    }

    public function bark2()
    {
        $demoApp2 = new DemoApp2('sachin');
        return $demoApp2->hey($this);
    }
    
}