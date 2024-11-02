<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_fonction_date extends site_fonction 
{
	
	#Fonction date format fr
	public static function decodeDate($date)
	{	
		$date2=explode('-',$date);
		$date_final=$date2[2].'-'.$date2[1].'-'.$date2[0];
		return $date_final;
	
	}
	
	#Fonction date format fr
	public static function decodeDatetime($date, $secondes = 0)
	{	
		$tDate = explode(' ', $date);
		$tDate1 = explode('-', $tDate[0]);
		$tDate2 = explode(':', $tDate[1]);
		return $tDate1[2].'-'.$tDate1[1].'-'.$tDate1[0].' '.$tDate2[0].':'.$tDate2[1].($secondes == 1 ? ':'.$tDate2[2] : '');
	
	}
	
	#methode de calcul d'ajout de jour a une date
	public static function ajoutJour($nb,$date)
	{
			#calcul de la date d'expiration				
			$Tdate = explode("-",$date);
			$date_reglement  = @mktime(0, 0, 0,$Tdate[1] , $Tdate[2] + $nb, $Tdate[0]);
			$date_reglement  = date("Y-m-d", $date_reglement);
			return($date_reglement);
	}
	
	#methode pour transformer les dates en lettre avec uniquement le mois et l'année
	public static function dateLettre($date)
	{
		$Tdate = explode("-",$date);
		$mois = $Tdate[1];
		if($mois == "01"){ $mois = "Janvier";}
		if($mois == "02"){ $mois = "Fevrier";}
		if($mois == "03"){ $mois = "Mars";}
		if($mois == "04"){ $mois = "Avril";}
		if($mois == "05"){ $mois = "Mai";}
		if($mois == "06"){ $mois = "Juin";}
		if($mois == "07"){ $mois = "Juillet";}
		if($mois == "08"){ $mois = "Aout";}
		if($mois == "09"){ $mois = "Septembre";}
		if($mois == "10"){ $mois = "Octobre";}
		if($mois == "11"){ $mois = "Novembre";}
		if($mois == "12"){ $mois = "D&eacute;cembre";}
		return $mois." ".$Tdate[0];
	}
	
	#methode pour ajouter un mois a une date
	public static function ajoutUnMois($date)
	{
		$Tdate = explode("-",$date);
		$mois = $Tdate[1];
		$annee = $Tdate[0];
		
		#calcul des mois suivant et precedent
		$mois = $mois + 1;
		if($mois == 13)
		{
			$mois = "01";
			$annee = $annee+1;
		}
		else 
		{
			if($mois > 0 && $mois < 10)
			{
				$mois = "0".$mois;
			}
			$annee = $annee;
		}
		$new_date = $annee."-".$mois."-".$Tdate[2];
		return $new_date;
	}
	
	#methode pour ajouter autant de mois que l'on veux a une date
	public function ajoutMois($date,$nb)
	{
		for($i=1;$i<=$nb;$i++)
		{
			$date = site_fonction_date::ajoutUnMois($date);
		}
		return $date;
	}
	
	#methode pour transfomer une date normale en une date avec uniquement le mois et l'année
	public static function transformDate($date)
	{
		$Tdate = explode("-",$date);
		return $Tdate[0]."-".$Tdate[1];
	}
}

?>