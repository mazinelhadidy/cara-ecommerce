<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $this->addCascadeForeignKey($table, 'users', 'user_id');
                $this->addCascadeForeignKey($table, 'products', 'product_id');
                $table->unsignedInteger('quantity');
                $table->timestamps();

                $table->unique(['user_id', 'product_id']);
            });
        }
    }

    private function addUserForeignKey(Blueprint $table): void
    {
        $this->addCascadeForeignKey($table, 'users', 'user_id');
    }

    private function addCascadeForeignKey(Blueprint $table, string $relatedTable, string $column): void
    {
        if (Schema::getColumnType($relatedTable, 'id') === 'bigint') {
            $table->foreignId($column)->constrained($relatedTable)->cascadeOnDelete();

            return;
        }

        $table->integer($column)->index();
        $table->foreign($column)->references('id')->on($relatedTable)->cascadeOnDelete();
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
