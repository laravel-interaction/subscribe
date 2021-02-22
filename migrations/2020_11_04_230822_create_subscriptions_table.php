<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            config('subscribe.table_names.subscriptions'),
            function (Blueprint $table): void {
                config('subscribe.uuids') ? $table->uuid('uuid') : $table->bigIncrements('id');
                $table->unsignedBigInteger(config('subscribe.column_names.user_foreign_key'))->index()->comment('user_id');
                $table->morphs('subscribable');
                $table->timestamps();
                $table->unique([config('subscribe.column_names.user_foreign_key'), 'subscribable_type', 'subscribable_id']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('subscribe.table_names.subscriptions'));
    }
}
