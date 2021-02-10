<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Subscribe\Concerns\Subscribable;
use LaravelInteraction\Subscribe\Concerns\Subscriber;

/**
 * @method static \LaravelInteraction\Subscribe\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Subscriber;
    use Subscribable;
}
