<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $fillable = [
        'service_id', 'staff_id', 'customer_id',
        'starts_at', 'ends_at', 'status', 'payment_status',
    ];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    public static function staffConflict(int $staffId, string $start, string $end, ?int $ignoreId = null): bool
    {
        $q = self::where('staff_id', $staffId)
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start);
        if ($ignoreId) $q->where('id', '!=', $ignoreId);
        return $q->exists();
    }
}
