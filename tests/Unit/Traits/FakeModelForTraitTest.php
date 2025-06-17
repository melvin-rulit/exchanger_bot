<?php

namespace Tests\Unit\Traits;

use Spatie\MediaLibrary\HasMedia;
use App\Telegram\Traits\HandlesFile;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class FakeModelForTraitTest extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HandlesFile;

    protected $table = 'fake_models';
    public $timestamps = false;
}
