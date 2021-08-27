<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Events;

use Illuminate\Database\Eloquent\Model;

class Subscribed
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $subscription;

    public function __construct(Model $subscription)
    {
        $this->subscription = $subscription;
    }
}
