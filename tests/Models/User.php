<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelSubscribe\Concerns\Subscriber;

/**
 * @method static \Zing\LaravelSubscribe\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Subscriber;
}
