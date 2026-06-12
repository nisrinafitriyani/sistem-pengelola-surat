<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasUserstamps;

class Client extends Model
{
    use SoftDeletes, HasUserstamps;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'pic_name',
    ];

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function getAbbreviationAttribute(): string
    {
        $name = strtoupper($this->name);
        // Hapus awalan PT / CV
        $name = trim(str_replace(['PT.', 'PT ', 'CV.', 'CV '], '', $name));
        
        $words = explode(' ', $name);
        $abbreviation = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $abbreviation .= substr($word, 0, 1);
            }
        }
        
        return $abbreviation ?: 'XX';
    }
}
