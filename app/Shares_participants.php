<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class Shares_participants extends Model implements AuthenticatableContract, CanResetPasswordContract{
  
    use Authenticatable, CanResetPassword;
  protected $table = 'shares_participants';
  protected $fillable = ['name','password','salon','adress','region_id','email','type'];
  public function region() {
        return $this->belongsTo("App\Region", "region_id", "id");
    }
    
    
    
    public function getPoints($share_id) {
        $points = $this->shares_point()->where("share_id",$share_id)->get();
        if($points->count() > 0) {
           return $points->first()->points; 
        }
        
       // d($this->shares_point()->where("share_id",$share_id)->get() );
//        if(!is_null($this->shares_point)) {
//            
//        return $this->shares_point->first()->points;
//        }
        return null;
    }
    
    public function shares_point() {
        return $this->hasMany("App\Shares_point", "shares_participant_id", "id");
    }

    public function getRegion() {
        return !is_null($this->region) ? $this->region->name : "";
    }
    public static function auth($data) {
        $error_data = array();
        session_start();
        $captcha_hash = $_SESSION['captcha_hash'];
        $is_human = \Illuminate\Support\Facades\Hash::check(strtolower($data['captcha']), $captcha_hash);
        if(!$is_human) {
        $error_data['errors'] = array("captcha" => "" );
        return $error_data;
        }
        
        $rules = array(
            'password' => 'required',
            'email' => 'required',
        );
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            $data['errors'] = $validator->errors()->keys();
            return $data;
        };
        if (Auth::guard("share_p")->attempt(['email' => $data['email'], 'password' => $data['password'] ])) {
            return true;
        }
        return false;
    }
    
    public static function saveNewUserFrom($data) {
        session_start();
        $captcha_hash = $_SESSION['captcha_hash'];
        $is_human = \Illuminate\Support\Facades\Hash::check(strtolower($data['captcha']), $captcha_hash);
        if (!$is_human) {
            $error_data['errors'] = array("captcha" => "");
            return $error_data;
        }
        $rules = array(
            'name' => 'required',
            'salon' => 'required',
            'email' => 'required|email|unique:shares_participants',
            'password' => 'required',
            'adress' => 'required',
        );
        
        $validator = Validator::make($data,$rules);

        if($validator->fails()) {
            $data['errors'] = $validator->errors()->keys();
            $errors = array();
            foreach($data['errors'] as $e=>$v) {
               $errors[$v] = ""; 
            }
            $data['errors'] = $errors;
            return $data;
        };
        $data['password'] = bcrypt($data['password']);
        $sp = self::create($data);
        Auth::guard("share_p")->login($sp);
        
        return true;
        
        
    }

}

?>