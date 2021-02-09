<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Subscribe\Concerns\Subscribable;

/**
 * @method static \LaravelInteraction\Subscribe\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Subscribable;
}
