<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTableForAddProductFeature extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {

            // Drop old columns if exist
            if (Schema::hasColumn('products', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('products', 'size')) {
                $table->dropColumn('size');
            }

            // Sizes
            if (!Schema::hasColumn('products', 'sizes')) {
                $table->json('sizes')->nullable()->after('name');
            }

            // Gender
            if (!Schema::hasColumn('products', 'gender')) {
                $table->string('gender')->nullable()->after('sizes');
            }

            // Price
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 12, 2)->after('gender');
            } else {
                $table->decimal('price', 12, 2)->change();
            }

            // Discount
            if (!Schema::hasColumn('products', 'discount')) {
                $table->decimal('discount', 12, 2)->nullable()->after('price');
            }

            // Stock
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('discount');
            } else {
                $table->integer('stock')->default(0)->change();
            }

            // Shipping Cost
            if (!Schema::hasColumn('products', 'shipping_cost')) {
                $table->decimal('shipping_cost', 12, 2)->nullable()->after('stock');
            }

            // Category ID
            // TEMPORARY: tanpa foreign key dulu supaya deployment aman
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('image');
            }

            // Description
            if (Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->change();
            } else {
                $table->text('description')->nullable()->after('shipping_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'sizes',
                'gender',
                'discount',
                'shipping_cost',
                'category_id',
            ]);

        });
    }
}