<?php
class site_wubook_etab{
	private $lcode;
	private $room_list=array();
	private $room_list_h=array();
	private $res_room;
	//private $room_list_str;
	
	#constructeur
	public function __construct($tab)
	{
		$this->lcode = $tab["lcode"];
		$room_list_str="";
		
		foreach( $tab["list_room"] as $room_id=>$detail){
			$add_room=new site_wubook_chambre( $room_id, $this->lcode, $detail[0], $detail[1],$tab["type_req"] );
			$this->room_list[$room_id]=$add_room;
			
			//$room_list_str.=$room_id.",";
		}
		//$this->room_list_str=substr($room_list_str,0,-1);
		// $this->set_rooms_detail();
		
		if($tab["type_req"]==2){
			$this->set_rooms_detail();
		}
	}
	
	
	public function get_detail($detail_name){
		return $this->$detail_name;
	}
	
	//Récupère les caractéristiques des chambres de l'établissement
	private function request_rooms_detail(){
		$data_string = "lcode=".$this->lcode;
		//Requete Post
		$ch = curl_init(_WB_FOUNT_CHAMBRE_URL);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('multipart/form-data', 'Content-Length: ' . strlen($data_string)));
		$this->res_room = json_decode(curl_exec($ch));
		curl_close($ch);
		
	}
	
	//Récupère les caractéristiques des chambres de l'établissement
	private function set_rooms_detail(){
		$this->request_rooms_detail();
		
		
		foreach($this->res_room->rooms as $detail_room){
			$tab_desc[$detail_room->id]=$detail_room->descr_fr;
			if(isset($this->room_list[$detail_room->id])){
				$this->room_list[$detail_room->id]->set_desc($detail_room);
			}
		}
		
		foreach($this->res_room->rooms as $detail_room){
			if(isset($this->room_list[$detail_room->id])){
				if($detail_room->room_parent!="") 
					$this->room_list[$detail_room->id]->set_detail('descr_fr',$tab_desc[$detail_room->room_parent]);
			}
		}
	}
	
	public function get_best_price(){
		$prix_tmp=0;
		foreach($this->room_list as $room_id => $room){
			if($prix_tmp==0){
				$prix_tmp=$room->get_detail('price');
			}else{
				if ($prix_tmp > $room->get_detail('price')) $prix_tmp =$room->get_detail('price');
			}
		}
		return $prix_tmp;
		
	}
	
	public function get_max_price(){
		$prix_tmp=0;
		foreach($this->room_list as $room_id => $room){
			if($prix_tmp==0){
				$prix_tmp=$room->get_detail('price');
			}else{
				if ($prix_tmp < $room->get_detail('price')) $prix_tmp =$room->get_detail('price');
			}
		}
		return $prix_tmp;
		
	}
	//Supprime une chambre de l'établissement (filtre)
	public function unset_room($room_id){
		unset($this->room_list[$room_id]);
	}
	
	private function build_sorter($champ,$ordre) {
		
		return function ($a, $b) use ($champ,$ordre) {
			if($ordre=="ASC")
				return ($a->get_detail($champ) > $b->get_detail($champ));
			else
				return ($a->get_detail($champ) < $b->get_detail($champ));
			
		};
	}
	
	public function sort_room($champ, $ordre){
		uasort($this->room_list, $this->build_sorter($champ, $ordre));
	}
	
}

?>
