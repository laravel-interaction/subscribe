<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class SubscribeServiceProvider extends InteractionServiceProvider
{
    /**
     * @var string
     */
    protected $interaction = InteractionList::SUBSCRIBE;
}
