<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Observers\QuotationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Traits\HasUserstamps;

#[ObservedBy(QuotationObserver::class)]
class Quotation extends Model
{
    use SoftDeletes, HasUserstamps;

    protected $fillable = [
        'client_id',
        'reference_number',
        'date',
        'project_name',
        'project_subname',
        'service_type',
        'work_category',
        'subject_description',
        'type',
        'status',
        'items',
        'total_amount',
        'signature_name',
        'signature_role',
        'signature_path',
        'stamp_path',
    ];

    protected $casts = [
        'date' => 'date',
        'items' => 'array',
        'total_amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(Approval::class);
    }


    /**
     * Get label for type
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'po' => 'Purchase Order',
            'wo' => 'Work Order',
            default => strtoupper($this->type),
        };
    }
}
