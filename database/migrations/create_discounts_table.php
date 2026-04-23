<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;
use Mpietrucha\Laravel\Essentials\Enums\DiscountType;

return new class extends Migration
{
    public function up(): void
    {
        $model = Discount::getModelFacade();

        /** @phpstan-ignore argument.type */
        Schema::create($model::getTable(), static function (Blueprint $table) use ($model): void {
            $table->id();

            $table->enum('type', DiscountType::cases());

            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('quantity_used')->nullable();

            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedTinyInteger('discount_percentage')->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('active_from')->nullable();
            $table->timestamp('active_to')->nullable();

            $model::getMorphName() |> $table->mixedMorphs(...);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        /** @phpstan-ignore argument.type */
        Discount::getModelFacade()::getTable() |> Schema::dropIfExists(...);
    }
};
