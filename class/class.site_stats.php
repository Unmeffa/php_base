<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_stats extends site_fonction 
{
	private $stats_id;
	private $lien_id;
	private $stats_date;
	private $stats_ip;
	private $stats_type;
		
	#constructeur
	public function __construct($lien_id,$stats_ip,$stats_type = "lien")
	{
		$this->stats_id = '';
		$this->lien_id = $lien_id;
		$this->stats_date = date("Y-m-d");
		$this->stats_ip = $stats_ip;
		$this->stats_type = $stats_type;
	}
	
	
	
	#methode de récuperation d'un champ
	public function get($attribut)
	{
		return($this->$attribut);
	}
	
	#methode de modification d'un attribut
	public function set($attribut,$valeur)
	{
		$this->$attribut = $valeur;
	}
	
	#ajout d'une stats
	public function ajoutStats()
	{
		#Requete d'ajout de la stats
		$query = "INSERT INTO ".self::table."_stats value
				  (
					'$this->stats_id',
					'".$this->lien_id."',
					'$this->stats_date',
					'$this->stats_ip',
					'$this->stats_type'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->stats_id = mysql_insert_id();
		
	}
		
	#Suppression d'un stats
	public static function supStats($lien_id,$type = "lien")
	{
		#on supprime la stats
		$query = "delete from ".self::table."_stats where lien_id = ".$lien_id." and stats_type = '$type'";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#methode pour l'insertion du click sur le lien dans les statss
	public static function click($lien_id,$ip,$type = "lien")
	{
		if($type == ""){ $type = "lien";}
		
		#je teste si un click par cet ip a deja eu lieu sur ce lien
		$rs = site_fonction::recup("stats","where lien_id = $lien_id and stats_type = '$type' and stats_ip ='$ip' and stats_date = '".date("Y-m-d")."'",0,1,"stats_id");
		if(mysql_num_rows($rs) == 0)
		{
			#je rajoute mon click au statss
			$s = new site_stats($lien_id,$ip,$type);
			$s->ajoutStats();
		}
	}
	
	#methode pour recuperer les stats d'un lien sur une periode
	public static function recupVisite($lien_id,$debut,$fin = "",$stats_type = "lien")
	{
		if($stats_type == ""){ $stats_type = "lien";}
		
		#ajout de la clause de fin de perdiode
		if($fin != ""){ $test_date = " and stats_date >= '$debut' and stats_date <= '$fin' ";} else{ $test_date = " and stats_date = '$debut' ";}
		
		#recuperation des stats
		$rs = site_fonction::recup("stats","where lien_id = $lien_id and stats_type = '$stats_type' $test_date","","","stats_id");
		
		#on renvoi le nombre de resultats
		$nb = mysql_num_rows($rs);
		return $nb;
	}
	
	public static function recupVisiteUnique($lien_id,$debut,$fin = "",$stats_type = "lien")
	{
		if($stats_type == ""){ $stats_type = "lien";}
		
		#ajout de la clause de fin de perdiode
		if($fin != ""){ $test_date = " and stats_date >= '$debut' and stats_date <= '$fin' ";} else{ $test_date = " and stats_date = '$debut' ";}
		
		#recuperation des stats
		$rs = site_fonction::recup("stats","where lien_id = $lien_id and stats_type = '$stats_type' $test_date group by stats_ip","","","stats_id");
		
		#on renvoi le nombre de resultats
		$nb = mysql_num_rows($rs);
		return $nb;
	}
	

}

?>