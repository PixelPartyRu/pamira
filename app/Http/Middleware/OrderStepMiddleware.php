<?php

namespace App\Http\Middleware;
use App\Http\Requests\Request;
use Closure;

class OrderStepMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $cart_path = "dealer/cart";
    private $formalize_order_cart_path = "dealer/formalize_order_cart";
    private $formalize_order_completion_path = "dealer/formalize_order_completion" ;
    public function handle($request, Closure $next)
    {
               $dealer = \Illuminate\Support\Facades\Auth::guard("dealer")->user();
        $order = $dealer->getCurentOrder();

        //dd_not_die($order->order_step);
        //dd_not_die( $request->route()->getPrefix() ); 
       // dd_not_die( $request->getPathInfo() ); 
        $ob = new \stdClass();
        $ob->val = 1;
        //dd( $ob );
        switch ($order->order_step) {
            case 1:
                if($request->getPathInfo() !== "/".$this->cart_path ) return redirect($this->cart_path);
                break;
            case 2:
                if($request->getPathInfo() !== "/".$this->formalize_order_cart_path ) return redirect($this->formalize_order_cart_path);
                break;
            case 3:
                if($request->getPathInfo() !== "/".$this->formalize_order_completion_path ) return redirect($this->formalize_order_completion_path);
                break;
        }
        //if($order->order_step == )
        return $next($request);
    }
}
