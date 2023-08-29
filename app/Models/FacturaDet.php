<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaDet extends Model
{
    use HasFactory;

    protected $table = 'sma_sale_items';

    function factura() {
        return $this->belongsTo(Factura::class, 'sale_id');
    }
}
