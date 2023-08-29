<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaElectronica extends Model
{
    use HasFactory;

    protected $table = 'fe_facturas';

    function documento() {
        return $this->hasOne(Factura::class, 'id', 'fk_factura');
    }
}
