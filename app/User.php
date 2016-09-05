<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Illuminate\Support\Facades\Auth;


class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use Authenticatable, CanResetPassword;
    protected $table = 'users';
    protected $remember_token_name      = 'remember_token';
    protected $fillable = [
        'name', 'email', 'password','adress','type','sns',
        'phone','region_id'
    ];
    private static $auth_guard = "web";

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function dealer_orders() {
        return $this->hasMany("App\Orders", "user_id", "id");
    }
    public function region() {
        return $this->belongsTo("App\Region", "region_id", "id");

    }

    public static function getLoginCustomerOrUser() {
        if( is_null(Auth::guard("web")->user()) && is_null(Auth::guard("dealer")->user()) ) return null;
        return is_null(Auth::guard("web")->user())?Auth::guard("dealer")->user():Auth::guard("web")->user();
    }
    public static function getLoginUserType() {
       if( is_null(Auth::guard("web")->user()) && is_null(Auth::guard("dealer")->user()) ) return null;  
       return is_null(Auth::guard("web")->user())?"dealer":"customer";
    }
    
    public static function getLoginCustomer() {

        return Auth::guard("web")->user();
    }
    
    public static function createAndLoginEmptyCustomer() {
        
        $max_id = \App\User::max("id") ;
        $user = self::create( array("type" => "user","email" => time()."_".($max_id+1)."_customer" ) );
        Auth::guard("web")->login($user, true);

    }
    
    
    public function createNewCustomerSession() {
    
    }
    
    public function cart() {
        
    }
    //возвращает объект заказа, в который будут добавляться новые позиции

    
    public function getCurentOrder() {


        $order = $this->dealer_orders()->where("status", 0);
        //var_dump($order->count());
        if ($order->count() == 0) {
            $order = null;
        } else {
            $order = $order->get()->first();
        }
        return $order;
        
    }
    public function createNewOrder() {


        $order = $this->dealer_orders()->where("status", 0);
        //var_dump($order->count());
        if ($order->count() == 0) {
            $order = Orders::create(['user_id' => $this->id,"order_step" => 1]);
            $order->order_step = 1;
            $order->save();

            
        } else {
            $order = $order->get()->first();
        }
        return $order;
    }

    //Проверка на существование пользователя по email
    public static function hasCustomerByEmail($email) {

        return self::where("email",$email)->count() == 0;
        
    }
    //Проверка на существование пользователя по email
    public static function hasUserByEmail($email) {

        return self::where("email", $email)->count() > 0;
    }

    public static function getCustomerByEmail($email) {

        return self::where("email", $email)->get()->first();
    }
    
    public static function logoutAll() {
        if(!is_null(Auth::guard("dealer")->user())) Auth::guard("dealer")->logout();
        if(!is_null(Auth::guard("web")->user())) Auth::guard("web")->logout();
    }
    
    public function is_md5_password(){
        return (preg_match('#^([A-F0-9]|[a-f0-9]){32}$#', $this->password));
    }

    /* public function login() {
        Auth::guard("web")->login($this);
    }*/
   /* public static function logout() {
        Auth::guard("web")->logout();
    }*/
    


}
