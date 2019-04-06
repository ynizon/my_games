<?php

namespace App\Http\Controllers;

use Artisan;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\UserRepository;
use App\Repositories\SiteRepository;
use App\Repositories\ViewRepository;
use App\Repositories\AccountRepository;
use Session;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Account;
use Mail;
use DB;


class CronController extends Controller
{

    protected $userRepository;
	protected $siteRepository;
	protected $viewRepository;
	protected $accountRepository;

    public function __construct(UserRepository $userRepository, SiteRepository $siteRepository, ViewRepository $viewRepository, AccountRepository $accountRepository)
    {
		$this->userRepository = $userRepository;
		$this->siteRepository = $siteRepository;
		$this->viewRepository = $viewRepository;
		$this->accountRepository = $accountRepository;
	}

	public function index(Request $request)
    {
		//On verifie les parametres
		if ($request->get("id_site") == null or $request->get("date") == null){exit();}
		
		$type_refresh = "informations";
		if ($request->get("type_refresh") != null){
			$type_refresh = $request->get("type_refresh");	
		}
		
		$site = $this->siteRepository->getById($request->get("id_site")); 
		$sDate = $request->get("date");
		
		$sAnneeMoisLastUpdate = substr($sDate,6,4).substr($sDate,3,2);		
		
		Artisan::call('refresh:informations',['--force'=>1,'--site_id'=>$site->id,'--date'=>$sAnneeMoisLastUpdate,'--type_refresh'=>$type_refresh]);
		
		
		$sContent = "";
		if (file_exists(config("filesystems.my_storage")."/cron/cron_".$site->id.".txt")){
			$sContent = file_get_contents(config("filesystems.my_storage")."/cron/cron_".$site->id.".txt");	
		}
		
		if (strpos($sContent,"ERREUR")!==false){
			return redirect('/sites/'.$site->user_id)->withError("Le site a été rafraichi mais des erreurs ont étés trouvées, consultez le <a href='/sites/".$site->id."/log' target='_blank'>fichier log</a> pour comprendre ce qui est mal paramétré.");	
		}else{
			return redirect('/sites/'.$site->user_id)->withOk("Le site a été rafraichi correctement.");
		}		
	}

}