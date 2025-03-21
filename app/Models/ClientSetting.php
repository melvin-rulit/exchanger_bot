<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSetting extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'setting_id'];
    public $timestamps = false;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
