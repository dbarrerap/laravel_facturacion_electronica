<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'sma_companies';

    function facturas() {
        return $this->hasMany(Factura::class, 'customer_id');
    }
}
