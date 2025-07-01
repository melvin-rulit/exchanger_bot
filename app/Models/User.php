<?php

namespace App\Models;

use App\Observers\UserObserver;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Database\Factories\UserFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([UserObserver::class])]

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    public mixed $first_name;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('user_avatar');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('screenshot')
            ->width(368)
            ->height(232);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'password_show',
        'enabled',
        'last_login_at',
        'is_locked',
        'lock_password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function templateMessages(): HasMany
    {
        return $this->hasMany(TemplateMessage::class);
    }

    public function settings(): BelongsToMany
    {
        return $this->belongsToMany(Setting::class, 'user_settings')->withPivot('is_active');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getImageUrl(): ?string
    {
        $media = $this->getFirstMedia('user_avatar');
        return $media ? $media->getUrl() : null;
    }
}
