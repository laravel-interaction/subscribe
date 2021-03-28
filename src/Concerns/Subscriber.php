<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
        return ($this->relationLoaded('subscriberSubscriptions') ? $this->subscriberSubscriptions : $this->subscriberSubscriptions())
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->count() > 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function subscribe(Model $object): void
    {
        if ($this->hasSubscribed($object)) {
            return;
        }

        $this->subscribedItems(get_class($object))->attach($object->getKey());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriberSubscriptions(): HasMany
    {
        return $this->hasMany(config('subscribe.models.subscription'), config('subscribe.column_names.user_foreign_key'), $this->getKeyName());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function toggleSubscribe(Model $object): void
    {
        $this->subscribedItems(get_class($object))->toggle($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function unsubscribe(Model $object): void
    {
        if ($this->hasNotSubscribed($object)) {
            return;
        }

        $this->subscribedItems(get_class($object))->detach($object->getKey());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function subscribedItems(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'subscribable', config('subscribe.models.subscription'), config('subscribe.column_names.user_foreign_key'))->withTimestamps();
    }
}
