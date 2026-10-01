<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['appointment_id', 'provider', 'amount_cents', 'status', 'reference'];

    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
}
