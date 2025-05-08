<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperTemplateMessage
 */
class TemplateMessage extends Model
{
    protected $fillable = ['user_id', 'title', 'text', 'category'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
