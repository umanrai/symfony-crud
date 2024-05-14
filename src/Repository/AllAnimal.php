<?php

namespace App\Repository;

use App\AnimalInterface;

class AllAnimal implements AnimalInterface
{

    public function eat()
    {
        return 'eat';
    }

    public function poop()
    {
        return 'poop';
    }

    public function breathe(): int
    {
        return "beathe air";
    }

}