<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperBank
 */
class Bank extends Model
{
    use HasFactory;

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_bank');
    }
}
