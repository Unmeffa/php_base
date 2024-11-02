<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_prio extends site_fonction 
{
	#fonction pour gerer les prios
	public static function changePrio($table,$prio,$new_prio,$and = "")
	{		
		#recuperation du nombre d'enregistrement
		$rs = site_fonction::recup($table,"where 1 ".$and);
		$nb = mysql_num_rows($rs);
		if($new_prio > 0 && $new_prio <= $nb)
		{
		
			#recuperation de l'enregistrement dont on modifie la prio
			$rs = site_fonction::recup($table," where ".$table."_prio = ".$prio." ".$and);
			$row = @mysql_fetch_row($rs);
			$id = $row[0];
			
			#on met la prio a 0
			$query = "	UPDATE  ".site_fonction::table."_".$table." set ".
						$table."_prio = 0
						WHERE ".$table."_id = ".$id." ";
			$rs = mysql_query($query) or die($query);
			if($prio>$new_prio)
			{
				#mise a jour des enregistrements a decaler
				$rs =site_fonction::recup($table," where ".$table."_prio >= ".$new_prio." and ".$table."_prio < ".$prio." ".$and." order by ".$table."_prio");
				while($row = @mysql_fetch_assoc($rs))
				{
					$p = $row[$table."_prio"] + 1;
					$query = "	UPDATE  ".site_fonction::table."_".$table." set ".
								$table."_prio = ".$p."
								WHERE ".$table."_id = ".$row[$table."_id"]." ";
					$rs = mysql_query($query) or die($query);
				}
			}
			else
			{
				#mise a jour des enregistrements a decaler
				$rs =site_fonction::recup($table," where ".$table."_prio <= ".$new_prio." and ".$table."_prio > ".$prio." ".$and." order by ".$table."_prio");
				while($row = @mysql_fetch_assoc($rs))
				{
					$p = $row[$table."_prio"] - 1;
					$query = "	UPDATE  ".self::table."_".$table." set ".
								$table."_prio = ".$p."
								WHERE ".$table."_id = ".$row[$table."_id"]." ";
					$rs = mysql_query($query) or die($query);
				}
			}
			
			#mise a jour de l'enregistrement dont on a modifier la prio
			$query = "	UPDATE  ".self::table."_".$table." set ".
						$table."_prio = ".$new_prio."
						WHERE ".$table."_id = ".$id." ";
			$rs = mysql_query($query) or die($query);
		}
			
	}
	
	public static function majPrio($table,$prio,$and = "")
	{
		
		#mise a jour des enregistrements a decaler
		$rs =site_fonction::recup($table," where ".$table."_prio > ".$prio." ".$and." order by ".$table."_prio");
		while($row = @mysql_fetch_assoc($rs))
		{
			$p = $row[$table."_prio"] - 1;
			$query = "	UPDATE  ".site_fonction::table."_".$table." set ".
						$table."_prio = ".$p."
						WHERE ".$table."_id = ".$row[$table."_id"]." ";
			$resu = mysql_query($query) or die($query);
		}
	}
}

?>