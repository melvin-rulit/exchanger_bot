<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperMessage
 */
class Message extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['chat_id', 'order_id', 'user_id', 'client_id', 'sender_type', 'message', 'message_group', 'created_at'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('chat_screenshot');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('screenshot')
            ->width(368)
            ->height(232);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function getChatId($messageId)
    {
        return self::where('id', $messageId)
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->value('chat_id');
    }

    public function getImageUrl(): ?string
    {
        $media = $this->getFirstMedia('chat_screenshot');
        return $media ? $media->getUrl() : null;
    }
}
