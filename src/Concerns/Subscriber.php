<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelSubscribe\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 */
trait Subscriber
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function subscribe(Model $object): void
    {
        $this->subscribedItems(get_class($object))->attach($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function unsubscribe(Model $object): void
    {
        $this->subscribedItems(get_class($object))->detach($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function toggleSubscribe(Model $object): void
    {
        $this->subscribedItems(get_class($object))->toggle($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasSubscribed(Model $object): bool
    {
        return ($this->relationLoaded('subscriptions') ? $this->subscriptions : $this->subscriptions())
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function hasNotSubscribed(Model $object): bool
    {
        return ! $this->hasSubscribed($object);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(config('subscribe.models.subscription'), config('subscribe.column_names.user_foreign_key'), $this->getKeyName());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function subscribedItems(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'subscribable', config('subscribe.models.subscription'), config('subscribe.column_names.user_foreign_key'), 'subscribable_id')->withTimestamps();
    }
}
