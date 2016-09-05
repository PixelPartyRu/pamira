<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarginBrand extends Model
{
  protected $table = 'margin_brands';
        protected $fillable = [
        'margin_id', 'brand_id', 'margin'
    ];
        
  public function brand() {
    return  $this->belongsTo("App\Brand","brand_id","id");
  }      
  
  public static function getByMarginId($id) {
      return self::where("margin_id",$id)->get();
      
      
  }
        
        

}
