<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
  protected $table = 'region';
  
  public function shares_participants() {
      
      return $this->hasMany("App\Shares_participants","region_id","id");
      
      
  }
}
