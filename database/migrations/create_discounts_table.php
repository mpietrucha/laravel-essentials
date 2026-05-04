<?php

use Facades\Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;
use Facades\Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount\Quota;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Quota::getTable(), static function (Blueprint $table): void {
            $table->id();

            $table->string('name')->nullable();
            $table->text('notes')->nullable();

            $table->unsignedTinyInteger('limit_total')->nullable();
            $table->unsignedTinyInteger('limit_used')->nullable();

            self::timestamps($table);
        });

        Schema::create(Discount::getTable(), static function (Blueprint $table): void {
            $table->id();

            $table->foreignIdFor(Quota::getModel())->nullable();

            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedTinyInteger('discount_percentage')->nullable();

            Discount::getMorphName() |> $table->mixedMorphs(...);

            self::timestamps($table);
        });
    }

    public function down(): void
    {
        Quota::getTable() |> Schema::dropIfExists(...);

        Discount::getTable() |> Schema::dropIfExists(...);
    }

    protected static function timestamps(Blueprint $table): void
    {
        $table->timestamp('active_from')->nullable();
        $table->timestamp('active_to')->nullable();

        $table->timestamp('finished_at')->nullable();

        $table->softDeletes();
        $table->timestamps();
    }
};
