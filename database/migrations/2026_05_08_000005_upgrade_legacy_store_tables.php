<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('name');
            }

            if (! Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('price');
            }

            if (! Schema::hasColumn('products', 'image_path')) {
                $table->string('image_path')->nullable()->after('stock');
            }

            if (! Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image_path');
            }
        });

        if (Schema::hasColumn('products', 'title') && Schema::hasColumn('products', 'name')) {
            DB::table('products')->whereNull('name')->update(['name' => DB::raw('title')]);
        }

        if (Schema::hasColumn('products', 'quantity') && Schema::hasColumn('products', 'stock')) {
            DB::table('products')->where('stock', 0)->update(['stock' => DB::raw('quantity')]);
        }
    }

    public function down(): void
    {
        //
    }
};
