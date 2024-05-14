<?php

namespace App;

use App\Repository\AllAnimal;

class CatAnimal extends AllAnimal
{
    public function meow()
    {
        return 'meow';
    }
    
}