<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver_id');
            $table->dropConstrainedForeignId('truck_id');
        });
    }

    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('truck_id')->nullable()->constrained('trucks');
        });
    }
};
