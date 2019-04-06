<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Request;
abstract class HelperServiceProvider extends ServiceProvider
{
	/* Convertit une date de 2016-01-31 a 01/2016 */
	public static function showMonth($sDate, $bJustDate = false)
	{
		if ($sDate == "" or $sDate == "1970-01-01"){
			return "";
		}else{
			if ($bJustDate){
				return substr($sDate,5,2)."/".substr($sDate,0,4);
			}else{
				return substr($sDate,5,2)."/".substr($sDate,0,4).substr($sDate,10);
			}
		}
	}
	
	/* Convertit une date de 2016-01-31 a 31/01/2016 */
	public static function formatDateFR($sDate, $bJustDate = false)
	{
		if ($sDate == "" or $sDate == "1970-01-01"){
			return "";
		}else{
			if ($bJustDate){
				return substr($sDate,8,2)."/".substr($sDate,5,2)."/".substr($sDate,0,4);
			}else{
				return substr($sDate,8,2)."/".substr($sDate,5,2)."/".substr($sDate,0,4).substr($sDate,10);
			}
		}
	}
	
	/* Convertit une date de 31/01/2016 a 2016-01-31*/
	public static function formatDateSQL($sDate)
	{
		return substr($sDate,6,4)."-".substr($sDate,3,2)."-".substr($sDate,0,2);
	}
	
	/* Renvoie une date pour les calendriers JS avec new Date(2018,12,31) a partir de 31-12-2018 */
	public static function formatDateCalendarJS($sDate){
		$iJour = substr($sDate,0,2);
		$iMois = substr($sDate,3,2);
		$iAnnee = substr($sDate,6,4);
		return "new Date(".$iAnnee.",".$iMois.",".$iJour.")" ;
	}
	
	public static function formatDureeHeureMin($iSecondes)
	{
		$iHeure = 0;
		$iMin = 0;
		$iSec = 0;
		while ($iSecondes>3600){
			$iHeure++;
			$iSecondes  = $iSecondes - 3600;
		}
		while ($iSecondes>60){
			$iMin++;
			$iSecondes  = $iSecondes - 60;
		}
		$iSec = $iSecondes;
		
		$sHeure = $iHeure;
		if (strlen($iHeure)<2){
			$sHeure = "0".$iHeure;
		}
		$sMin = $iMin;
		if (strlen($iMin)<2){
			$sMin = "0".$iMin;
		}
		
		return $sHeure.":".$sMin;
	}
	
	public static function formatDureeHeureMinSec($iSecondes)
	{
		$iHeure = 0;
		$iMin = 0;
		$iSec = 0;
		while ($iSecondes>3600){
			$iHeure++;
			$iSecondes  = $iSecondes - 3600;
		}
		while ($iSecondes>60){
			$iMin++;
			$iSecondes  = $iSecondes - 60;
		}
		$iSec = $iSecondes;
		
		$sHeure = $iHeure;
		if (strlen($iHeure)<2){
			$sHeure = "0".$iHeure;
		}
		$sMin = $iMin;
		if (strlen($iMin)<2){
			$sMin = "0".$iMin;
		}
		$sSec = $iSec;
		if (strlen($iSec)<2){
			$sSec = "0".$iSec;
		}
		return $sHeure.":".$sMin.":".$sSec;
	}
	
	
	/* Renvoie le chiffre avec les bons separateurs */
	public static function showNumber($sNumber, $sCurrency, $iVirgule = 0){	
		$r = number_format($sNumber, $iVirgule, ',', ' ');
		if ($sCurrency != ""){
			$r .= " " .$sCurrency;
		}
		return $r;
	}
	
	 /**
     * Affiche un nombre avec les bons séparateurs (>FR) 10 000.00
     * @param unknown_type $s
     */
    public static function num($number, $bEuro = true, $iDecimale = 2){
    	if ($number == ""){ 
			$number = 0;
		}
		if (round($number,$iDecimale) == 0){
			$number = 0;
		}
		$s = number_format($number, $iDecimale, ',', ' ');
		if ($bEuro){
			$s .= " &euro;";
		}
		return $s;
    }
	
	/**
	 * Renvoie le nom du mois
	 * @param unknown_type $iMois
	 */
	public static function getMois($iMois, $bPrefixe = false) {
		$iMois = (int) $iMois;
		$sMois = "";
		$sPrefix = "de ";
		switch ($iMois){
			case 0:
				$sMois = "Décembre";
				break;
			case 1:
				$sMois = "Janvier";
				break;
			case 2:
				$sMois = "Février";
				break;
			case 3:
				$sMois = "Mars";
				break;
			case 4:
				$sPrefix = "d'";
				$sMois = "Avril";
				break;
			case 5:
				$sMois = "Mai";
				break;
			case 6:
				$sMois = "Juin";
				break;
			case 7:
				$sMois = "Juillet";
				break;
			case 8:
				$sPrefix = "d'";
				$sMois = "Août";
				break;
			case 9:
				$sMois = "Septembre";
				break;
			case 10:
				$sPrefix = "d'";
				$sMois = "Octobre";
				break;
			case 11:
				$sMois = "Novembre";
				break;
			case 12:
				$sMois = "Décembre";
				break;
			case 13:
				$sMois = "Janvier";
				break;
		}	

		if (!$bPrefixe){
			return $sMois;
		}else{
			return $sPrefix . $sMois;
		}
	}
}
