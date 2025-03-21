<?php

namespace App\Models;

use App\Observers\CountryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([CountryObserver::class])]
class Country extends Model
{
    use HasFactory;

    public function banks(): BelongsToMany
    {
        return $this->belongsToMany(Bank::class, 'country_bank');
    }

}
