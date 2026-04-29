<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Request extends Model
{
    protected $fillable = [
        'title',
        'description',
        'quantity',
        'unit_price',
        'total',
        'status',
        'user_id',
        'admin_note',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the user that owns the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate total automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($request) {
            $request->total = $request->quantity * $request->unit_price;
        });
    }
}
