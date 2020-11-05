<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelSubscribe\Concerns\Subscribable;

/**
 * @method static \Zing\LaravelSubscribe\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Subscribable;
}
