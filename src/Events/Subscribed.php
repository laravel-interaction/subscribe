<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Events;

use Illuminate\Database\Eloquent\Model;

class Subscribed
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
