<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Topic::class, function (Faker $faker) {
    $sentence  =$faker->sentence();
    $updated_at = $faker->dateTimeThisMonth();
    $ceated_at = $faker->dateTimeThisMonth($updated_at);
    return [
        'title'=>$sentence,
        'body'=>$faker->text(),
        'created_at'=>$ceated_at,
        'updated_at'=>$updated_at,
    ];
});
