<?php

namespace App\Models;

use App\Observers\CountryObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([CountryObserver::class])]

/**
 * @mixin IdeHelperCountry
 */
class Country extends Model
{
    protected $fillable = ['name_ru', 'name_en', 'code', 'flag', 'is_used'];
    public function banks(): BelongsToMany
    {
        return $this->belongsToMany(Bank::class, 'country_bank');
    }

    use HasFactory;

    public function credentials(): HasMany
    {
        return $this->hasMany(CountriesCredential::class, 'country_id');
    }

    public function getFirstCredential(): ?CountriesCredential
    {
        /** @var CountriesCredential|null $credential */
        $credential = $this->credentials()->first();

        return $credential;
    }

    public function getCredentialValueAttribute(): ?string
    {
        return $this->getFirstCredential()?->value;
    }

    public function getLocalizedName(string $language = 'ru'): string
    {
        return $language === 'ru' ? ($this->name_ru ?? '') : ($this->name_en ?? '');
    }
}
