<?php

namespace App\Jobs;

use Mail;
use App\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MonitorUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $monitor;
	
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Monitor $monitor)
    {
        //
		$this->monitor = $monitor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
		try{
			$monitor = $this->monitor ;
			$timeout= 30;
			$ch = curl_init($monitor->url);	
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			
			if (preg_match('`^https://`i', $monitor->url))
			{
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			} 
			
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
			curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
			 
			$headers = array(
				'Keep-Alive: 300',
				'Connection: keep-alive'
			);
				   
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			 
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			// Simulation d'un Firefox 3.6.13	
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');
			//Cookies
			curl_setopt($ch, CURLOPT_COOKIESESSION, true );
			curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');   
			
			$tabInfo = array();
			$tabInfo["canonical"] = "";		
			$tabInfo["robots"] = "";
			$tabInfo["other"] = "";
			
			if ($myXMLString = curl_exec($ch)){		
				$docXML = new \DomDocument();		
				$internalErrors = libxml_use_internal_errors(true);
				if ($docXML->loadHtml($myXMLString)){
					$xpath = new \DOMXPath($docXML);
					
					$sXpath = "//link[@rel='canonical']";
					$lNodes = $xpath->query($sXpath);
					foreach ($lNodes as $oNode) {
						$tabInfo["canonical"] = $oNode->getAttribute("href");
					}
					
					$sXpath = "//meta[@name='robots']";
					$lNodes = $xpath->query($sXpath); 					
					foreach ($lNodes as $oNode) {
						$tabInfo["robots"] = $oNode->getAttribute("content");
					}
					
					$sXpath = $monitor->xpath_other;
					if ($sXpath != ""){
						$lNodes = $xpath->query($sXpath); 					
						foreach ($lNodes as $oNode) {
							$tabInfo["other"] = $oNode->value;
						}
					}
				}
			}
			$tabInfo["status"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			$bChgt = false;
			$sChgts = "URL: ".$monitor->url.":<br/>";
			foreach($tabInfo as $sField=>$sValue){
				if ($monitor->$sField != $sValue){
					$bChgt = true;
					$sOldValue = $monitor->$sField;
					if ($sOldValue == ""){
						$sOldValue = " pas de valeur ";
					}
					$sChgts .= "<br/><b>".$sField.":</b><br/>".$sOldValue." -> ".$sValue;
					$bAlerte = true;
				}
				$monitor->$sField = $sValue;
			}
			
			$to = $monitor->email;
			$monitor->result = $sChgts;
			$monitor->save();
			
			if ($bChgt){
				Mail::send('emails.monitor', ['monitor' => $monitor,'sChgts'=>$sChgts], function ($m) use ($to) {
					$m->from(config('mail.from.address'), config('mail.from.name'));
					$m->to($to, $to)->subject('Search-Foresight > Monitoring urls');
				});
			}
		}catch(\Exception $e){
			$monitor->result = $e->getMessage();
			$monitor->save();
		}
		
		
    }
}
