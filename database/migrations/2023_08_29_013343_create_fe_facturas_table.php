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
        Schema::create('fe_facturas', function (Blueprint $table) {
            $table->id();
            $table->integer('fk_factura');
            $table->string('num_documento', 255);
            $table->string('fecha_emision', 10);
            $table->string('establecimiento', 3);
            $table->string('pto_emision', 3);
            $table->string('clave_acceso', 128);
            $table->string('estado', 2);
            $table->text('observaciones')->nullable();
            $table->date('fecha_autorizacion')->nullable();
            $table->string('clave_autorizacion')->nullable();
            $table->integer('identificador')->nullable();
            $table->string('mensaje_error')->nullable();
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
        Schema::dropIfExists('fe_facturas');
    }
};
