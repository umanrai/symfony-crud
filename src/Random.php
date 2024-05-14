<?php

namespace App;

class Random
{

    public function hello()
    {
        $cat = new CatAnimal;
        $cat->meow();

        $dog = new DogAnimal('dasdsa');

        $dog->bark2();
    }

}