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
        Schema::create('conf_empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_comercial', 250);
            $table->string('razon_social', 250);
            $table->string('ruc', 15);
            $table->text('direccion');
            $table->string('telefono', 32);
            $table->string('email', 128);
            $table->string('fe_firma_p12', 250)->nullable();
            $table->string('fe_clave', 250)->nullable();
            $table->string('obligado_contabilidad')->default('NO');
            $table->string('estado', 2)->default('A');
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
        Schema::dropIfExists('conf_empresa');
    }
};
