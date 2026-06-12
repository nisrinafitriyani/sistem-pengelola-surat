<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUserstamps;

class DeliveryNote extends Model
{
    use SoftDeletes, HasUserstamps;

    protected $fillable = [
        'approval_id',
        'delivery_number',
        'date',
        'vehicle_type',
        'vehicle_plate',
        'driver_name',
        'receiver_name',
        'notes',
        'signature_image',
        'signature_name',
        'signature_role',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }
}
