<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function details(): HasMany
    {
        return $this->hasMany(SalesOrderDetail::class, 'sales_order_id');
    }
}
