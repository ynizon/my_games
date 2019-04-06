<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Card extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('gam_cards', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('game_id')->default(0);
			$table->string('name')->default("");
			$table->string('lang')->default("fr");
			$table->string('country')->default("-");
			$table->integer('status')->default(1);
			$table->integer('difficulty')->default(1);
			$table->text('description');
			$table->date('created')->default("1970-01-01");
			$table->timestamps(); 
			$table->string('created_by')->default("");
			$table->string('updated_by')->default("");
			$table->string('deleted_by')->default("");			
		});

		$sRep = "database/xml";
		$tabLanguages = array("fr_FR"=>"Français","en_GB"=>"English");
		foreach ($tabLanguages as $sLangue => $sLanguage){ 
			$tabFiles = scandir($sRep); 
			foreach ($tabFiles as $sFile){
				if ($sFile != ".." and $sFile != "."){
					if (strpos($sFile,$sLangue."_") !== false and strpos($sFile,".xml") !== false){
						if (file_exists($sRep."/".$sFile)){
							$docXML = new \DOMDocument();			
							$sContenu = file_get_contents($sRep."/".$sFile);
							try{
								if ($docXML->loadXML($sContenu)){
									$sXpath = "//CARTES/*";
									$xpath = new \DOMXPath($docXML);
									$lNodes = $xpath->query($sXpath);
								
									$name = "";
									$difficulty = 1;
									$language = "fr";
									$description = array();
									foreach ($lNodes as $oNode){
										foreach ($oNode->childNodes as $oNodeKeywords) {
											if ($oNodeKeywords->nodeType == XML_ELEMENT_NODE) {
												switch ($oNodeKeywords->nodeName){
													case "NOM":
														$name = $oNodeKeywords->nodeValue;									
														break;
													case "MODE":
														$mode = $oNodeKeywords->nodeValue;									
														break;
													case "PERSONS":
														$description["persons"] = $oNodeKeywords->nodeValue;									
														break;
													case "DESCRIPTION":
														$description["description"] = $oNodeKeywords->nodeValue;									
														break;
													case "DIFFICULTE":
														$difficulty = $oNodeKeywords->nodeValue;
														break;
													case "LANGUE":
														$language = substr($oNodeKeywords->nodeValue,0,2);
														break;
													case "MOT1":
														$description["word1"] = $oNodeKeywords->nodeValue;
														break;
													case "MOT2":
														$description["word2"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT3":
														$description["word3"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT4":
														$description["word4"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT5":
														$description["word5"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT6":
														$description["word6"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT7":
														$description["word7"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT8":
														$description["word8"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT9":
														$description["word9"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT10":
														$description["word10"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT11":
														$description["word11"]  = $oNodeKeywords->nodeValue;
														break;
													case "MOT12":
														$description["word12"]  = $oNodeKeywords->nodeValue;
														break;											
												}
											}
										}

										switch ($mode){
											case 1:
											case 7:
												$description = array();												
											case 2:
												if (isset($description["persons"])){
													unset($description["persons"]);
												}
												DB::table('gam_cards')->insert(
													array(
														'difficulty' => $difficulty,
														'name' => $name,
														'lang' => $language,
														'country' => "-",
														'description' => json_encode($description),
														'game_id'=>$mode,
														'status'=>1,
													)
												);  
												break;
												
											case 3:
												for( $k=6;$k<=12;$k++ ){
													unset($description["word".$k]);	
												}
												
												DB::table('gam_cards')->insert(
													array(
														'difficulty' => $difficulty,
														'name' => $name,
														'lang' => $language,
														'country' => "-",
														'description' => json_encode($description),
														'game_id'=>$mode,
														'status'=>1,
													)
												);  
												break;
												
											case 4:
												for( $k=2;$k<=12;$k++ ){
													unset($description["word".$k]);	
												}
												
												DB::table('gam_cards')->insert(
													array(
														'difficulty' => $difficulty,
														'name' => $name,
														'lang' => $language,
														'country' => "-",
														'description' => json_encode($description),
														'game_id'=>$mode,
														'status'=>1,
													)
												);  
												break;
												
											case 6:												
												DB::table('gam_cards')->insert(
													array(
														'difficulty' => $difficulty,
														'name' => $name,
														'lang' => $language,
														'country' => "-",
														'description' => json_encode($description),
														'game_id'=>$mode,
														'status'=>1,
													)
												);  
												break;
										}
									}
								}
							}catch(Exception $e){
								//echo $e->getMessages();
							}
						}
					}
				} 
			}
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //		
		Schema::drop('gam_cards'); 
    }
} 
