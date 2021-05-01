<?php

declare(strict_types=1);

namespace LaravelInteraction\Subscribe;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use LaravelInteraction\Subscribe\Events\Subscribed;
use LaravelInteraction\Subscribe\Events\Unsubscribed;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $subscriber
 * @property \Illuminate\Database\Eloquent\Model $subscribable
 *
 * @method static \LaravelInteraction\Subscribe\Subscription|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Subscribe\Subscription|\Illuminate\Database\Eloquent\Builder query()
 */
class Subscription extends MorphPivot
{
    protected $dispatchesEvents = [
        'created' => Subscribed::class,
        'deleted' => Unsubscribed::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            function (self $like): void {
                if ($like->uuids()) {
                    $like->{$like->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    public function getIncrementing(): bool
    {
        if ($this->uuids()) {
            return true;
        }

        return parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    public function getTable()
    {
        return config('subscribe.table_names.subscriptions') ?: parent::getTable();
    }

    public function isSubscribedBy(Model $user): bool
    {
        return $user->is($this->subscriber);
    }

    public function isSubscribedTo(Model $object): bool
    {
        return $object->is($this->subscribable);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('subscribable_type', app($type)->getMorphClass());
    }

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('subscribe.models.user'), config('subscribe.column_names.user_foreign_key'));
    }

    protected function uuids(): bool
    {
        return (bool) config('subscribe.uuids');
    }
}
