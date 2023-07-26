<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $book = Book::find($request->book_id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        if ($book->copies <= 0) {
            return response()->json(['message' => 'No available copies of the book'], 400);
        }

        $checkedOutByUser = Checkout::where('user_id', $request->user_id)
            ->where('book_id', $request->book_id)
            ->whereNull('return_date')
            ->first();

        if ($checkedOutByUser) {
            return response()->json(['message' => 'The book is already checked out by the user'], 400);
        }

        $book->decrement('copies');

        $checkout = Checkout::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            'checkout_date' => now(),
        ]);

        return response()->json($checkout, 201);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'return_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $checkout = Checkout::find($id);

        if (!$checkout) {
            return response()->json(['message' => 'Checkout not found'], 404);
        }

        if ($checkout->return_date) {
            return response()->json(['message' => 'The book is already marked as returned'], 400);
        }

        $book = Book::find($checkout->book_id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->increment('copies');

        $checkout->update([
            'return_date' => $request->return_date,
        ]);

        return response()->json($checkout, 200);
    }
}
