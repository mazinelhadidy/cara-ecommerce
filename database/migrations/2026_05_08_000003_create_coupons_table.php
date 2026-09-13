<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('type')->default('fixed');
                $table->decimal('value', 10, 2);
                $table->decimal('minimum_order_amount', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('coupons', function (Blueprint $table) {
            if (! Schema::hasColumn('coupons', 'code')) {
                $table->string('code')->nullable()->after('id');
            }

            if (! Schema::hasColumn('coupons', 'type')) {
                $table->string('type')->default('fixed')->after('code');
            }

            if (! Schema::hasColumn('coupons', 'value')) {
                $table->decimal('value', 10, 2)->default(0)->after('type');
            }

            if (! Schema::hasColumn('coupons', 'minimum_order_amount')) {
                $table->decimal('minimum_order_amount', 10, 2)->default(0)->after('value');
            }

            if (! Schema::hasColumn('coupons', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('minimum_order_amount');
            }

            if (! Schema::hasColumn('coupons', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('is_active');
            }

            if (! Schema::hasColumn('coupons', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('starts_at');
            }
        });

        if (Schema::hasColumn('coupons', 'name') && Schema::hasColumn('coupons', 'code')) {
            DB::table('coupons')->whereNull('code')->update(['code' => DB::raw('UPPER(name)')]);
        }

        if (Schema::hasColumn('coupons', 'discount_type') && Schema::hasColumn('coupons', 'type')) {
            DB::statement("update coupons set type = case when discount_type = 'percentage' then 'percent' else 'fixed' end");
        }

        if (Schema::hasColumn('coupons', 'discount_value') && Schema::hasColumn('coupons', 'value')) {
            DB::table('coupons')->where('value', 0)->update(['value' => DB::raw('discount_value')]);
        }

        if (Schema::hasColumn('coupons', 'expired_at') && Schema::hasColumn('coupons', 'expires_at')) {
            DB::table('coupons')->whereNull('expires_at')->update(['expires_at' => DB::raw('expired_at')]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
