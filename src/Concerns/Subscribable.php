<?php


namespace Zing\LaravelSubscribe\Concerns;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelSubscribe\Subscription[] $subscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelSubscribe\Concerns\Subscriber[] $subscribers
 */
trait Subscribable
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $user
     *
     * @return bool
     */
    public function isSubscribedBy(Model $user): bool
    {
        if (is_a($user, config('subscribe.models.user'))) {
            if ($this->relationLoaded('subscribers')) {
                return $this->subscribers->contains($user);
            }

            return tap($this->relationLoaded('subscriptions') ? $this->subscriptions : $this->subscriptions())
                    ->where(config('subscribe.column_names.user_foreign_key'), $user->getKey())->count() > 0;
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(config('subscribe.models.subscription'), 'subscribable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            config('subscribe.models.user'),
            config('subscribe.table_names.subscriptions'),
            'subscribable_id',
            config('subscribe.column_names.user_foreign_key')
        )
            ->where('subscribable_type', $this->getMorphClass());
    }
}