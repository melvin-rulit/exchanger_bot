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

    public static function getTodayMessagesOnlyForConsultationByGroup(): LengthAwarePaginator
    {
        return self::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('messages')
                ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
                ->whereNull('order_id')
                ->where('sender_type', 'client')
                ->groupBy('chat_id');
        })
            ->orderBy('created_at', 'desc')
            ->paginate(16);
    }

    public static function getTodayMessagesOnlyForConsultationChat($chat_id): Collection
    {
        return self::where('chat_id', $chat_id)
            ->whereNull('order_id')
            ->whereDate('created_at', Carbon::today())
            ->with('media')
            ->get();
    }

    public static function getChatId($message_id)
    {
        return self::where('id', $message_id)
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->value('chat_id');
    }

    public function getImageUrl(): ?string
    {
        $media = $this->getFirstMedia('chat_screenshot');
        return $media ? $media->getUrl() : null;
    }
}
