<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('truck_id')->nullable()->constrained('trucks');
            $table->foreignId('trailer_id')->nullable()->constrained('trailers');
            $table->text('notes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver_id');
            $table->dropConstrainedForeignId('truck_id');
            $table->dropConstrainedForeignId('trailer_id');
            $table->dropColumn('notes');
        });
    }
};
