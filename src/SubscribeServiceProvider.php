<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class SubscribeServiceProvider extends InteractionServiceProvider
{
    protected $interaction = InteractionList::SUBSCRIBE;
}
