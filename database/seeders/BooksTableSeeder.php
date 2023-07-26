<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BooksTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            Book::create([
                'title' => $faker->sentence(3),
                'author' => $faker->name,
                'isbn' => $faker->isbn13,
                'published_at' => $faker->date,
                'copies' => $faker->numberBetween(0, 10),
            ]);
        }
    }
}