<?php 
namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Auth;
use Config;

class Locale {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {	
		$language = Config::get('app.locale');

		//Chgt de langue
		if ($request->get("lang") != null){
			$language = $request->get("lang");
			setcookie('locale', $language);
		}
		
		if (isset($_COOKIE["locale"])){
			$language = $_COOKIE["locale"];
		}
		App::setLocale($language);	
		
        return $next($request);
    }

}