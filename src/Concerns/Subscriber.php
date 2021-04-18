<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelInteraction\Subscribe\Subscription;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Subscribe\Subscription[] $subscriberSubscriptions
 * @property-read int|null $subscriber_subscriptions_count
 */
trait Subscriber
{
    public function hasNotSubscribed(Model $object): bool
    {
        return ! $this->hasSubscribed($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasSubscribed(Model $object): bool
    {
        return ($this->relationLoaded(
            'subscriberSubscriptions'
        ) ? $this->subscriberSubscriptions : $this->subscriberSubscriptions())
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->count() > 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return \LaravelInteraction\Subscribe\Subscription
     */
    public function subscribe(Model $object): Subscription
    {
        $attributes = [
            'subscribable_id' => $object->getKey(),
            'subscribable_type' => $object->getMorphClass(),
        ];

        return $this->subscriberSubscriptions()
            ->where($attributes)
            ->firstOr(function () use ($attributes) {
                $subscriberSubscriptionsLoaded = $this->relationLoaded('subscriberSubscriptions');
                if ($subscriberSubscriptionsLoaded) {
                    $this->unsetRelation('subscriberSubscriptions');
                }

                return $this->subscriberSubscriptions()
                    ->create($attributes);
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriberSubscriptions(): HasMany
    {
        return $this->hasMany(
            config('subscribe.models.subscription'),
            config('subscribe.column_names.user_foreign_key'),
            $this->getKeyName()
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool|\LaravelInteraction\Subscribe\Subscription
     */
    public function toggleSubscribe(Model $object)
    {
        return $this->hasSubscribed($object) ? $this->unsubscribe($object) : $this->subscribe($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function unsubscribe(Model $object): bool
    {
        $hasNotSubscribed = $this->hasNotSubscribed($object);
        if ($hasNotSubscribed) {
            return true;
        }
        $subscriberSubscriptionsLoaded = $this->relationLoaded('subscriberSubscriptions');
        if ($subscriberSubscriptionsLoaded) {
            $this->unsetRelation('subscriberSubscriptions');
        }

        return (bool) $this->subscribedItems(get_class($object))
            ->detach($object->getKey());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function subscribedItems(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'subscribable',
            config('subscribe.models.subscription'),
            config('subscribe.column_names.user_foreign_key')
        )
            ->withTimestamps();
    }
}
