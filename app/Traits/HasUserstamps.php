<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static void creating(\Closure|string|array $callback)
 * @method static void updating(\Closure|string|array $callback)
 * @method static void deleting(\Closure|string|array $callback)
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasUserstamps
{
    /**
     * Boot the trait and register model event listeners.
     */
    public static function bootHasUserstamps(): void
    {
        static::creating(function (Model $model) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $model->created_by = \Illuminate\Support\Facades\Auth::id();
                $model->updated_by = \Illuminate\Support\Facades\Auth::id();
            }
        });

        static::updating(function (Model $model) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $model->updated_by = \Illuminate\Support\Facades\Auth::id();
            }
        });

        static::deleting(function (Model $model) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                // If the model uses SoftDeletes, we want to update the deleted_by column
                // before it gets logically deleted.
                if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model))) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), 'deleted_by')) {
                        $model->deleted_by = \Illuminate\Support\Facades\Auth::id();
                        $model->saveQuietly(); // save without triggering other update events
                    }
                }
            }
        });
    }

    /**
     * Get the user who created the record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the record.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
