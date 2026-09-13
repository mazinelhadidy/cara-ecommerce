<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $this->addCascadeForeignKey($table, 'users', 'user_id');
                $this->addNullableForeignKey($table, 'coupons', 'coupon_id');
                $table->string('coupon_code')->nullable();
                $table->decimal('subtotal', 10, 2);
                $table->decimal('discount_total', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('status')->default('pending');
                $table->string('customer_name');
                $table->string('customer_email');
                $table->string('customer_phone')->nullable();
                $table->string('shipping_address')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'coupon_code')) {
                    $table->string('coupon_code')->nullable()->after('coupon_id');
                }

                if (! Schema::hasColumn('orders', 'subtotal')) {
                    $table->decimal('subtotal', 10, 2)->default(0)->after('coupon_code');
                }

                if (! Schema::hasColumn('orders', 'discount_total')) {
                    $table->decimal('discount_total', 10, 2)->default(0)->after('subtotal');
                }

                if (! Schema::hasColumn('orders', 'status')) {
                    $table->string('status')->default('pending')->after('total');
                }

                if (! Schema::hasColumn('orders', 'customer_name')) {
                    $table->string('customer_name')->nullable()->after('status');
                }

                if (! Schema::hasColumn('orders', 'customer_email')) {
                    $table->string('customer_email')->nullable()->after('customer_name');
                }

                if (! Schema::hasColumn('orders', 'customer_phone')) {
                    $table->string('customer_phone')->nullable()->after('customer_email');
                }

                if (! Schema::hasColumn('orders', 'shipping_address')) {
                    $table->string('shipping_address')->nullable()->after('customer_phone');
                }
            });

            if (Schema::hasColumn('orders', 'total') && Schema::hasColumn('orders', 'subtotal')) {
                DB::table('orders')->where('subtotal', 0)->update(['subtotal' => DB::raw('total')]);
            }

            if (Schema::hasColumn('orders', 'phone') && Schema::hasColumn('orders', 'customer_phone')) {
                DB::table('orders')->whereNull('customer_phone')->update(['customer_phone' => DB::raw('phone')]);
            }

            if (Schema::hasColumn('orders', 'address') && Schema::hasColumn('orders', 'shipping_address')) {
                DB::table('orders')->whereNull('shipping_address')->update(['shipping_address' => DB::raw('address')]);
            }
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $this->addCascadeForeignKey($table, 'orders', 'order_id');
                $this->addNullableForeignKey($table, 'products', 'product_id');
                $table->string('product_name');
                $table->decimal('unit_price', 10, 2);
                $table->unsignedInteger('quantity');
                $table->decimal('line_total', 10, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
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

    private function addNullableForeignKey(Blueprint $table, string $relatedTable, string $column): void
    {
        if (Schema::getColumnType($relatedTable, 'id') === 'bigint') {
            $table->foreignId($column)->nullable()->constrained($relatedTable)->nullOnDelete();

            return;
        }

        $table->integer($column)->nullable()->index();
        $table->foreign($column)->references('id')->on($relatedTable)->nullOnDelete();
    }
};
