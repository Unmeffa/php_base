<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

//Traitement à effectuer

		//Nombre de connectés
		/*$user_ip = $_SERVER['REMOTE_ADDR'];
		//On regle le temps à 5min
		$t = time() - (60 * 5);
		
		$rs_user = site_fonction::recup("users","where ip='$user_ip'");
		if(mysql_num_rows($rs_user) == 0)
		{
			$tab_user["ip"] = $user_ip;
			$tab_user["timestamp"] = time();
			$user = new site_users($tab_user);
			$user->ajout_user();
		}
		else
		{
			$user = site_users::recup_user($user_ip);
			$user->maj_user();
		}
		
		
		//On efface les enregistrements antérieurs à 5 min
		$user->sup_users();
		
		//On récupère toutes les entrées apres la suppression
		$new_rs = site_fonction::recup("users","where 1");
		$nb_connectes = mysql_num_rows($new_rs);
		if($nb_connectes == 1)
		{
			$string_connect = $nb_connectes." utilisateur connecté";
		}
		else
		{
			$string_connect = $nb_connectes." utilisateurs connectés";
		}*/
		
//Classe pour savoir le nombre de connectés
class site_users extends site_fonction 
{
	private $ip;
	private $timestamp;

	public static function recup_user($ip)
	{
		#Recuperation de l'IP
		$query = "select * from ".self::table."_users where ip='".$ip."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet USER
		$o = new site_users($row);
			
		#on retourne l'objet USER
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{

		$this->ip = $tab["ip"];
		$this->timestamp = $tab["timestamp"];

	}
	
	
	#methode de rÃ©cuperation d'un champ
	public function get($attribut)
	{
		return($this->$attribut);
	}
	
	#methode de modification d'un attribut
	public function set($attribut,$valeur)
	{
		$this->$attribut = $valeur;
	}
	
	#ajout d'un utilisateur
	public function ajout_user()
	{
		#Requete d'ajout de la type_client
		$query = "INSERT INTO ".self::table."_users value
				  (
					'$this->ip',
					'$this->timestamp'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
	}
	
	#Mise a jour du moment de connexion à l'instant T
	public function maj_user()
	{
		$query = "UPDATE  ".self::table."_users set
					timestamp = '".time()."'
					WHERE ip = '$this->ip'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression des utilisateurs à partir du temps voulu time()
	public function sup_users($time)
	{
		#on supprime la type_client
		$query = "delete from ".self::table."_users where timestamp < ".$time;
		$rs = mysql_query($query) or die(mysql_error());
		
	}
}

?>