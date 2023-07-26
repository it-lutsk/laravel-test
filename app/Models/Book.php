<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'isbn', 'published_at', 'copies'];

    public function checkouts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Checkout::class);
    }
}
