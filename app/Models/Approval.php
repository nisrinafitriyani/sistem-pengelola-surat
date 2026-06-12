<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\ApprovalObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Traits\HasUserstamps;

#[ObservedBy(ApprovalObserver::class)]
class Approval extends Model
{
    use SoftDeletes, HasUserstamps;

    protected $fillable = [
        'quotation_id',
        'reference_number',
        'approval_date',
        'client_pic_name',
        'attachment_path',
        'notes',
        'status',
    ];

    protected $casts = [
        'approval_date' => 'date',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function handover(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Handover::class);
    }

    public function deliveryNote(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DeliveryNote::class);
    }

    public function invoice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
