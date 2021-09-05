<?php

use FiftySq\Commerce\Data\CommerceMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommerceDiscountsTable extends CommerceMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        tap(static::prefix('discounts'), function ($table) {
            if (! Schema::hasTable($table)) {
                Schema::create($table, function (Blueprint $table) {
                    $table->id();
                    $table->string('code');
                    $table->string('type');
                    $table->integer('amount');
                    $table->timestamps();
                });

                Schema::create(static::prefix('discount_order'), function (Blueprint $table) {
                    $table->id();
                    $table->foreignId(static::prefix('discount_id'))->index()->constrained();
                    $table->foreignId(static::prefix('order_id'))->index()->constrained();
                    $table->timestamps();
                });
            }
        });
    }
}
