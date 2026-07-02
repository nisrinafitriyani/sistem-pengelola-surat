<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Observers\QuotationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'total_amount' => 'decimal:2',
    ];

    protected function items(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $decoded = is_string($value) ? json_decode($value, true) : $value;
                if (!is_array($decoded)) return [];

                if (count($decoded) > 0 && !isset($decoded[0]['type']) && !isset($decoded[0]['data'])) {
                    return array_map(function ($item) {
                        return [
                            'type' => 'item_row',
                            'data' => $item
                        ];
                    }, $decoded);
                }
                return $decoded;
            },
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }

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

    public function getServiceTypeAttribute($value)
    {
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : (empty($value) ? [] : [$value]);
    }

    public function setServiceTypeAttribute($value)
    {
        $this->attributes['service_type'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getFormattedServiceTypeAttribute(): string
    {
        $types = $this->service_type;
        return is_array($types) ? implode(' & ', $types) : ($types ?? '');
    }
}
