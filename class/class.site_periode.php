<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_periode extends site_fonction 
{
	private $periode_id;
	private $typehebergement_id;
	private $periode_nom;
	private $periode_intitule;
	private $periode_couleur;
	
	#methode de recuperation d'un objet hebergement
	public static function recupPeriode($periode_id)
	{
		$query = "SELECT * FROM ".site_fonction::table."_periode WHERE periode_id=$periode_id";
		$rs = mysql_query($query) or die($query);
		$row = mysql_fetch_assoc($rs);
		$h = new site_Periode($row["periode_id"],$row["typehebergement_id"],$row["periode_nom"],$row["periode_intitule"],$row["periode_couleur"]);
		return $h;
	}
	

	#constructeur//////////////////////////////////////////
	public function __construct($periode_id = "",$typehebergement_id = 0,$periode_nom = "",$periode_intitule = "",$periode_couleur ="")
	{
		$this->periode_id = $periode_id;	
		$this->typehebergement_id = $typehebergement_id;
		$this->periode_nom = $periode_nom;
		$this->periode_intitule = $periode_intitule;
		$this->periode_couleur = $periode_couleur;
	}
	
	//Mise a jour d'un periode
	public function majPeriode()
	{
		$ok = $this->testPeriode();
		if($ok == 0)
		{
			
			#mise a jour de la periode
			$query = "UPDATE  ".site_fonction::table."_periode set
						typehebergement_id = '$this->typehebergement_id',
						periode_nom = \"".site_fonction::protec($this->periode_nom)."\",
						periode_intitule = \"".site_fonction::protec($this->periode_intitule)."\",
						periode_couleur = '$this->periode_couleur'
						WHERE periode_id = $this->periode_id
					";
			$rs = mysql_query($query) or die($query);		
		}
		return $ok;
	}
	
	//Ajout d'un periode
	public function ajoutPeriode()
	{
		$ok = $this->testPeriode();
		if($ok == 0)
		{
			$query = "INSERT INTO ".site_fonction::table."_periode value
			(
					'',
					'$this->typehebergement_id',
					\"".site_fonction::protec($this->periode_nom)."\",
					\"".site_fonction::protec($this->periode_intitule)."\",
					'$this->periode_couleur'
					
			)";
			$rs = mysql_query($query) or die($query);
			
			#mise a jour de l'id du type d'hebergement
			$this->periode_id = mysql_insert_id();
			
			
		}
		return $ok;		
	}
	
	//supression d'un hebergeemnt
	public function supPeriode()
	{	
				
		#suppression de la peridoe
		$del = mysql_query("delete from ".site_fonction::table."_periode where periode_id=$this->periode_id") or die($query);
		
		#on supprime les tarif_periode de cette periode
		$query = "delete from ".self::table."_tarif_periode where periode_id=".$this->periode_id;
		$rs = mysql_query($query) or die(mysql_error());
		
	}	
	
	#methode de récuperation d'un champ////////////////////////////////
	public function get($attribut)
	{
		return($this->$attribut);
	}
	#methode de modification d'un attribut///////////////////////////////
	public function set($attribut,$valeur)
	{
		$this->$attribut = $valeur;
	}

	#methode pour tester les dates rentrer
	public function testPeriode()
	{
		#initialisation de la variable de test
		$ok = 0;
		
		/*#test sur la date de debut
		$rs = site_fonction::recup("periode","where (periode_debut = '$this->periode_debut' or periode_fin = '$this->periode_debut') and typehebergement_id = $this->typehebergement_id  and periode_id != '$this->periode_id'");
		if(@mysql_num_rows($rs) > 0){ $ok = 0;}
		
		#test sur la date de fin
		$rs = site_fonction::recup("periode","where (periode_fin = '$this->periode_fin'  or periode_fin = '$this->periode_fin') and typehebergement_id = $this->typehebergement_id  and periode_id != '$this->periode_id'");
		if(@mysql_num_rows($rs) > 0){ $ok = 0;}
		
		#test si la date de debut est avant la date de fin
		if($this->periode_debut >= $this->periode_fin){ $ok = 0;}*/
		
		return $ok;
	}
	
}

?>