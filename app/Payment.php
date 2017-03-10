<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'client_id';
    protected $table = 'payment_payments';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [];

    protected $visible = ['payment_date_str', 'payment_sum', 'client_id'];

}
