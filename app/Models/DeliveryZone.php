<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeliveryZone extends Model {
    protected $guarded = [];
    protected $casts = ['same_day_enabled'=>'boolean','is_active'=>'boolean'];
}
