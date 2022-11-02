<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Events;

use Illuminate\Database\Eloquent\Model;

class Subscribed
{
    public function __construct(public Model $model)
    {
    }
}
