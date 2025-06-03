<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperClient
 */
class Client extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['first_name'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('client_avatar');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('screenshot')
            ->width(368)
            ->height(232);
    }

    public function settings(): BelongsToMany
    {
        return $this->belongsToMany(Setting::class, 'client_settings');
    }

    public function getImageUrl(): ?string
    {
        $media = $this->getFirstMedia('client_avatar');
        return $media ? $media->getUrl() : null;
    }
}
