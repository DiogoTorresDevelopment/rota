<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_histories', function (Blueprint $table) {
            $table->boolean('is_initial')->default(false)->after('carroceria_ids');
        });
    }

    public function down()
    {
        Schema::table('delivery_histories', function (Blueprint $table) {
            $table->dropColumn('is_initial');
        });
    }
}; 