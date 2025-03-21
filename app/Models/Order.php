<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['chat_id', 'client_id', 'user_id', 'amount', 'currency_name', 'status'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('amount_check');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('screenshot')
            ->width(368)
            ->height(232);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
