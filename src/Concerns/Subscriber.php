<?php

declare(strict_types=1);

namespace Zing\LaravelSubscribe\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelSubscribe\Subscription[] $subscriptions
 */
trait Subscriber
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function subscribe(Model $object): void
    {
        /** @var \Illuminate\Database\Eloquent\Model|\Zing\LaravelSubscribe\Concerns\Subscribable $object */
        if (! $this->hasSubscribed($object)) {
            $subscribe = app(config('subscribe.models.subscription'));
            $subscribe->{config('subscribe.column_names.user_foreign_key')} = $this->getKey();

            $object->subscriptions()->save($subscribe);
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function unsubscribe(Model $object): void
    {
        /** @var \Illuminate\Database\Eloquent\Model|\Zing\LaravelSubscribe\Concerns\Subscribable $object */
        $relation = $object->subscriptions()
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->where(config('subscribe.column_names.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function toggleSubscribe(Model $object): void
    {
        $this->hasSubscribed($object) ? $this->unsubscribe($object) : $this->subscribe($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasSubscribed(Model $object): bool
    {
        return tap($this->relationLoaded('subscriptions') ? $this->subscriptions : $this->subscriptions())
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->count() > 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscriptions(): BelongsToMany
    {
        return $this->hasMany(config('subscribe.models.subscription'), config('subscribe.column_names.user_foreign_key'), $this->getKeyName());
    }
}
