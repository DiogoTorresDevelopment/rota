<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->string('placa', 8)->after('chassi');
            $table->date('ultima_revisao')->nullable()->after('quilometragem');
        });
    }

    public function down()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn(['placa', 'ultima_revisao']);
        });
    }
};
