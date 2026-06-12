<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUserstamps;

class Invoice extends Model
{
    use SoftDeletes, HasUserstamps;

    protected $fillable = [
        'approval_id',
        'invoice_number',
        'date',
        'reff_po_number',
        'contract_sum',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_name',
        'signature_name',
        'signature_role',
        'signature_path',
        'stamp_path',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'contract_sum' => 'decimal:2',
    ];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }
}
