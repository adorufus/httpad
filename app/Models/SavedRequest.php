<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SavedRequest
 *
 * @mixin Builder
 */
class SavedRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'method', 'url', 'headers', 'bodyType', 'body', 'formFields',
    ];

    protected $casts = [
        'headers' => 'array',
        'body' => 'array',
        'formFields' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
