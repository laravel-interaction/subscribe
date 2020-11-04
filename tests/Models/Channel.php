<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelSubscribe\Concerns\Subscribable;

class Channel extends Model
{
    use Subscribable;
}
