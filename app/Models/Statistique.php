<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statistique extends Model
{
    protected $table = 'statistiques';

    protected $fillable = [
        'resource_id', 'scanned_at', 'user_agent', 'ip_address', 'country', 'city',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
