<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUserstamps;

class Handover extends Model
{
    use SoftDeletes, HasUserstamps;

    protected $fillable = [
        'approval_id',
        'reference_number',
        'date',
        'attachment_path',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class);
    }
}
