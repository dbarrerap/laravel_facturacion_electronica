<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'sma_sales';

    function detalles() {
        return $this->hasMany(FacturaDet::class, 'sale_id');
    }

    function cliente() {
        return $this->belongsTo(Cliente::class, 'customer_id');
    }

    function pos_settings() {
        return $this->belongsTo(PosSettings::class, 'pos', 'pos_id');
    }

    function payment() {
        return $this->hasOne(Payment::class, 'sale_id');
    }
}
