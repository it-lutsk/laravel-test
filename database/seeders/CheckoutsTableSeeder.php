<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Checkout;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CheckoutsTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::pluck('id')->toArray();
        $books = Book::pluck('id')->toArray();

        foreach (range(1, 30) as $index) {
            $randomUserId = array_rand($users);
            $randomBookId = array_rand($books);

            while (!User::find($randomUserId) || !Book::find($randomBookId)) {
                $randomUserId = array_rand($users);
                $randomBookId = array_rand($books);
            }

            Checkout::create([
                'user_id' => $randomUserId,
                'book_id' => $randomBookId,
                'checkout_date' => now()->subDays(rand(1, 365)),
                'return_date' => now()->addDays(rand(1, 365)),
            ]);
        }
    }
}