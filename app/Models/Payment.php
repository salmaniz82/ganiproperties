<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
    protected $guarded = [];
    protected $casts = ['provider_data'=>'array'];
}
