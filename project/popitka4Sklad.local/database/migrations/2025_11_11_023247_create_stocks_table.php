<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('position', 45)->nullable();
            $table->timestamp('last_update')->useCurrent();
            $table->timestamps();
            
            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};