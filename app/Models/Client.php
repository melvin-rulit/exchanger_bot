<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperClient
 */
class Client extends Model
{
    use HasFactory;
    protected $fillable = ['first_name'];

    public function settings(): BelongsToMany
    {
        return $this->belongsToMany(Setting::class, 'client_settings');
    }
}
