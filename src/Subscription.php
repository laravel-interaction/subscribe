<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LaravelInteraction\Subscribe\Events\Subscribed;
use LaravelInteraction\Subscribe\Events\Unsubscribed;
use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\Models\Interaction;

/**
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $subscriber
 * @property \Illuminate\Database\Eloquent\Model $subscribable
 *
 * @method static \LaravelInteraction\Subscribe\Subscription|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Subscribe\Subscription|\Illuminate\Database\Eloquent\Builder query()
 */
class Subscription extends Interaction
{
    protected $interaction = InteractionList::SUBSCRIBE;

    protected $tableNameKey = 'subscriptions';

    protected $morphTypeName = 'subscribable';

    protected $dispatchesEvents = [
        'created' => Subscribed::class,
        'deleted' => Unsubscribed::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber(): BelongsTo
    {
        return $this->user();
    }

    public function isSubscribedBy(Model $user): bool
    {
        return $user->is($this->subscriber);
    }

    public function isSubscribedTo(Model $object): bool
    {
        return $object->is($this->subscribable);
    }
}
