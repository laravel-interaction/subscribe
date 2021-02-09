<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Events;

use Illuminate\Database\Eloquent\Model;

class Unsubscribed
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $subscription;

    /**
     * Liked constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $subscription
     */
    public function __construct(Model $subscription)
    {
        $this->subscription = $subscription;
    }
}
