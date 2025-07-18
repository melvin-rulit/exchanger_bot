<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use App\Observers\OrderObserver;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([OrderObserver::class])]
/**
 * @mixin IdeHelperOrder
 */
class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Notifiable, Searchable;

    protected $fillable = ['chat_id', 'client_id', 'user_id', 'bank_id', 'amount', 'currency_name', 'status', 'is_requisite', 'close_at', 'end_at'];

    /**
     * Индекс в Elasticsearch
     */
    public function searchableAs(): string
    {
        return 'orders';
    }
    public static function makeAllSearchableUsing($query)
    {
        return $query->with('messages');
    }

    /**
     * Данные для индексации
     */
    public function toSearchableArray(): array
    {
        return [
            'amount' => $this->amount,
            'messages_text' => $this->messages->pluck('message')->join(' '),
        ];
    }

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
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
    public function pinnedMessages(): HasMany
    {
        return $this->hasMany(UserPinnedMessage::class, 'order_id');
    }
    public function getImageUrl(): ?string
    {
        $media = $this->getFirstMedia('amount_check');
        return $media ? $media->getUrl() : null;
    }

    public function setIsRequisite(): void
    {
        self::update([
            'is_requisite' => true
        ]);
    }
}
