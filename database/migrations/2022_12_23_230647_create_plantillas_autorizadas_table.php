<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plantillas_autorizadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puesto_id')->constrained();
            $table->foreignId('ubicacione_id')->constrained();
            $table->smallInteger('cantidad')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plantillas_autorizadas');
    }
};
