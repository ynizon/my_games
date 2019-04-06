<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Repositories\UserRepository;
use App\Repositories\ProjectRepository;
use App\Project;
use App;
use App\User;
use Session;
use Illuminate\Http\Request;
use Auth;
use Mail;
use DB;


class FileController extends Controller
{

	protected $userRepository;

    public function __construct()
    {
		$this->userRepository = App::make('App\Repositories\UserRepository');
	}

	public function index(Request $request)
    {
		
	}
	
	//Upload de la piece jointe et suppression des anciennes
	public function replaceFile($request,$id,$object,$sFolder, $tabExt = array("zip,xls,xlsm,xlsx,csv,txt,pdf,msg")){
		$tabErrors = array();
		$file = $request->file('attachments');
		if ($file){
			//La validation par mime type est super galere, et mal gerer par Laravel
			if (!in_array(strtolower($file->getClientOriginalExtension()),$tabExt)){
				$sExt = "";
				foreach ($tabExt as $s){
					if ($sExt!=""){$sExt .= ",";}
					$sExt .= $s;
				}
				$tabErrors[] = "Le fichier n'est pas au bon format (".$sExt.")";
			}else{		
			
				if (!file_exists(config("filesystems.my_storage")."/app/".$sFolder."/".$id)){
					mkdir(config("filesystems.my_storage")."/app/".$sFolder."/".$id);
				}
				
				$destinationPath = config("filesystems.my_storage")."/app/".$sFolder."/".$id;
				$files = scandir($destinationPath);
				foreach ($files as $f){
					if ($f != "." and $f != ".."){
						unlink($destinationPath."/".$f);
					}
				}
				
				$sFileName = $file->getClientOriginalName();					
				$sExtension = strtolower($file->getClientOriginalExtension());

				$file->move($destinationPath, $sFileName);			
				$tabFiles = array();
				
				$tabFiles[$sFileName] = $destinationPath."/".$sFileName;
				$object->attachments = serialize($tabFiles);
				$object->save();
			}
		}
		
		return $tabErrors;
	}
	
	//Upload de la piece jointe
	public function uploadFile($request,$id,$object,$sFolder, $tabExt = array("zip,xls,xlsm,xlsx,csv,txt,pdf,msg")){
		$tabErrors = array();
		$file = $request->file('attachments');
		if ($file){
			//La validation par mime type est super galere, et mal gerer par Laravel
			if (!in_array(strtolower($file->getClientOriginalExtension()),$tabExt)){
				$sExt = "";
				foreach ($tabExt as $s){
					if ($sExt!=""){$sExt .= ",";}
					$sExt .= $s;
				}
				$tabErrors[] = "Le fichier n'est pas au bon format (".$sExt.")";
			}else{		
			
				if (!file_exists(config("filesystems.my_storage")."/app/".$sFolder."/".$id)){
					mkdir(config("filesystems.my_storage")."/app/".$sFolder."/".$id);
				}
				
				$destinationPath = config("filesystems.my_storage")."/app/".$sFolder."/".$id;
				
				$sFileName = $file->getClientOriginalName();					
				$sExtension = strtolower($file->getClientOriginalExtension());

				$file->move($destinationPath, $sFileName);			
				$tabFiles = array();
				if ($object->attachments != ""){
					$tabFiles = unserialize($object->attachments);
				}
				$tabFiles[$sFileName] = $destinationPath."/".$sFileName;
				$object->attachments = serialize($tabFiles);
				$object->save();
			}
		}
		
		return $tabErrors;
	}
	
	/* Supprime un fichier */
	public function removeFile(Request $request, $folder,$id){
		$file = $request->input("file");
		switch($folder){
			case "users":
				$object = $this->userRepository->getById($id);
				break;
		}
		$tab = array();
		if ($object->attachments != ""){
			$tab = unserialize($object->attachments); 	
		}
		if (isset($tab[$file])){
			unset($tab[$file]);
		}
		$object->attachments = serialize($tab);
		$object->save();
		
		if (file_exists(config("filesystems.my_storage")."/app/".$folder."/".$id."/".$file)){
			unlink(config("filesystems.my_storage")."/app/".$folder."/".$id."/".$file);
		}
				
		return redirect('/'.$folder.'/'.$id.'/edit')->withOk("Le fichier " . $file . " a été supprimé." );
	}
	
	/* Telecharge un fichier */
	public function getFile(Request $request,$folder,$id ){
		$file = $request->input("file");

		if (file_exists(config("filesystems.my_storage")."/app/".$folder."/".$id."/".$file)){
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
			echo file_get_contents(config("filesystems.my_storage")."/app/".$folder."/".$id."/".$file);
		}
		exit();
	}
}