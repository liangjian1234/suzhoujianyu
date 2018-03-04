<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Book::class, function (Faker $faker) {
    return [
        'number'=>$faker->numberBetween(1000000,9000000),
        'name'=>str_random(6),
        'author'=>$faker->name(),
        'price'=>$faker->randomFloat(),
        'publish_year'=>rand(2000,2018).'年',
        'publish_type'=>'第'.rand(1,10).'版',
        'image'=>str_random(10).'.jpg',
    ];
});
