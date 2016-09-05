<?php

namespace App;
use App\Dealer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Margin extends Model
{
  protected $table = 'margins';
      protected $fillable = [
        'name', 'user_id', 'default', 'type'
    ];
  static $TYPE = array("wholesale","retail");
  static $TYPE_RUS = array("wholesale" => "Оптовая","retail" => "Розничная");
  static $validate_fields = array("name");
  
  public function brands() {
           return $this->hasMany("App\MarginBrand","margin_id","id");
  }
  
  public static function saveFormData($margin, $brand_id_values, $brand_margin_values, $default) {

        $validator = Validator::make($margin,array('name' => array('required', 'min:5')));

        if($validator->fails()){
            return false;
        }
        //var_dump($validator->fails());
        
        if( isset( $margin['id'] ) ) {
            $margin_ob = self::find( intval($margin['id']) );
            $margin_ob->name = $margin['name'];
            $margin_ob->type = $margin['type'];
            $margin_ob->brands()->delete();
            if($default)
            {
                $margin_ob->setDefault();
            }
        }
        else {
        $margin_ob = self::create($margin);
        $margin_ob->user_id = Dealer::getLoginDealer()->id;
        }
        
        $margin_ob->save();

       /// dd($brand_id_values);
      // dd_not_die($brand_id_values);
      // dd_not_die($brand_margin_values);
       
        foreach ($brand_id_values as $key => $val) {
            
            
            //dd_not_die($val);
            
            $val = floatval( str_replace(",", ".", $val));
            //dd_not_die($val);
            
            $margin_brand = new \App\MarginBrand(array(
                "margin_id" => $margin_ob->id,
                "brand_id" => $brand_margin_values[$key],
                "margin" => $val
                    ));
            
            $margin_ob->brands()->save($margin_brand);
        }
        
        return true;
        //dd();
    }
    

    
    public static function formatPostMarginFormData(&$post) {
        $brand_margin = isset($post['brand_margin']) ? $post['brand_margin'] : array();
        $brand_ids = isset($post['brand_id']) ? $post['brand_id'] : array();
        unset($post['brand_margin']);
        unset($post['margin_for_all']);
        unset($post['_token']);
        $format_data['default'] = isset($post['default']) ? 1 : 0;
        $format_data['margin'] = $post;
        $format_data['brand_margin'] = $brand_margin;
        $format_data['brand_ids'] = $brand_ids;
        return $format_data;
        
        
    }
    
    
    private static function setDefaultCallback(&$margin) {
        if ($margin->default == 1) {
            $auth_dealer = Dealer::getLoginDealer();
            $auth_dealer->setDefaultMargin($margin->type,$margin->id);
            
        }
        //var_dump($margin);
    }
    public function setDefault() {
        $dealer = Dealer::getLoginDealer();
        $dealer->setDefaultMargin($this->type,$this->id);
    }

    public static function boot() {
        parent::boot();

        static::created(function($margin) {
            self::setDefaultCallback($margin);
        });
        static::updated(function($margin) {
            self::setDefaultCallback($margin);
        });
        static::deleting(function($margin) {
            $margin->brands()->delete();
        });
    }

}
