<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public $timestamps = false;

    public static function getSettingValueByKey($key)
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            return $setting->value;
        }

        return null;
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_settings');
    }
}
