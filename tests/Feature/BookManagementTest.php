<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user using Sanctum
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    // Happy path test for creating a new book
    public function test_can_create_a_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $bookData = [
            'title' => 'Test Book',
            'author' => 'John Doe',
            'isbn' => '1234567890',
            'published_at' => '2023-01-01',
            'copies' => 5,
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(201)
            ->assertJson(['title' => 'Test Book']);
    }

    // Edge case test for creating a new book with invalid data
    public function test_cannot_create_a_book_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $bookData = [
            // Missing 'title' field, which is required
            'author' => 'John Doe',
            'isbn' => '1234567890',
            'published_at' => '2023-01-01',
            'copies' => 5,
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    // Happy path test for updating a book
    public function test_can_update_a_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $book = Book::factory()->create();

        $bookData = [
            'title' => 'Updated Book',
            'author' => 'Jane Doe',
            'isbn' => '0987654321',
            'published_at' => '2022-01-01',
            'copies' => 10,
        ];

        $response = $this->putJson("/api/books/{$book->id}", $bookData);

        $response->assertStatus(200)
            ->assertJson(['title' => 'Updated Book']);
    }

    // Edge case test for updating a non-existent book
    public function test_cannot_update_a_non_existent_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $bookData = [
            'title' => 'Updated Book',
            'author' => 'Jane Doe',
            'isbn' => '0987654321',
            'published_at' => '2022-01-01',
            'copies' => 10,
        ];

        $response = $this->putJson('/api/books/999', $bookData);

        $response->assertStatus(404);
    }

    // Happy path test for checking out a book
    public function test_can_checkout_a_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $book = Book::factory()->create(['copies' => 2]);

        $checkoutData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ];

        $response = $this->postJson('/api/checkouts', $checkoutData);

        $response->assertStatus(201)
            ->assertJson(['user_id' => $user->id, 'book_id' => $book->id]);
    }

    // Edge case test for checking out a book that is not available
    public function test_cannot_checkout_unavailable_book()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $book = Book::factory()->create(['copies' => 0]);

        $checkoutData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ];

        $response = $this->postJson('/api/checkouts', $checkoutData);

        $response->assertStatus(400);
    }

    // Happy path test for marking a book as returned
    public function test_can_mark_book_as_returned()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $book = Book::factory()->create(['copies' => 1]);
        $checkout = $book->checkouts()->create(['user_id' => $user->id]);

        $returnDate = '2023-07-26';

        $response = $this->putJson("/api/checkouts/{$checkout->id}", ['return_date' => $returnDate]);

        $response->assertStatus(200)
            ->assertJson(['return_date' => $returnDate]);
    }

    // Edge case test for marking a book as returned when it is already returned
    public function test_cannot_mark_book_as_already_returned()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $book = Book::factory()->create(['copies' => 1]);
        $checkout = $book->checkouts()->create(['user_id' => $user->id, 'return_date' => '2023-07-26']);

        $response = $this->putJson("/api/checkouts/{$checkout->id}", ['return_date' => '2023-07-26']);

        $response->assertStatus(400);
    }
}
