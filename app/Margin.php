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


  // public static function saveFormData($margin, $brand_id_values, $brand_margin_values, $default) {
  public static function saveFormData($margin, $brand_id_values, $brand_margin_values, $default, $current_type_margin="", $add_text_for_name="", $rev=false) {

        $validator = Validator::make($margin,array('name' => array('required', 'min:5')));

        if($validator->fails()){
            return false;
        }
        //var_dump($validator->fails());

        if( isset( $margin['id'] ) ) {

            // if( $rev ){
            //     $type_margin = $current_type_margin;
            // }
            // else{
                $type_margin = $margin['type'];
            // }

            $margin_ob = self::find( intval($margin['id']) );
            $margin_ob->name = $margin['name'].$add_text_for_name;
            $margin_ob->type = $type_margin;
            $margin_ob->brands()->delete();
            if($default)
            {
                $margin_ob->setDefault();
            }
        }
        else {

            if( $rev ){
                $margin['type'] = $current_type_margin;
                $margin['name'] = $margin['name'].$add_text_for_name;
            }

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
        $mark_up_initially = isset($post['mark_up_initially']) ? $post['mark_up_initially'] : "";
        unset($post['brand_margin']);
        unset($post['margin_for_all']);
        unset($post['_token']);
        $format_data['default'] = isset($post['default']) ? 1 : 0;
        $format_data['margin'] = $post;
        $format_data['brand_margin'] = $brand_margin;
        $format_data['brand_ids'] = $brand_ids;
        $format_data['mark_up_initially'] = $mark_up_initially;
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
