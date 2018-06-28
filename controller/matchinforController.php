<?php

	class matchinforController extends controller {

		public function __construct(){
			parent::__construct();	
		}

		public function checkPriv($action, $utype)
		{
			parent::checkPriv($action, UTYPE_NONE);
		}

		private function make_params_string($params)
		{
			$retStr = "?";
			foreach ($params as $key => $value)
			{
				$retStr .= "params[";
				$retStr .= $key;
				$retStr .= "]=";
				$retStr .= $value;
				$retStr .= "&";
			}
			$retStr = rtrim($retStr, "&");
			return $retStr;
		}

		public function index() {
			$competitions = array();
			$competition = new competition;
			$err = $competition->query("SELECT * FROM t_competition");
			/*var_dump($err);
			die();*/
			if ($err != ERR_NODATA)
				$this->checkError($err);
			while ($err == ERR_OK)
			{
				array_push($competitions,
					array("id_competition" => $competition->id_competition  ,"date_from" => $competition->date_from ,
						"name" => $competition->name,
						"country" => $competition->country,
						"has_results" => $competition->has_results
					));
				$err = $competition->fetch();
			}
			$this->competitions = $competitions;
		}

		function download(){
			$file_name = sprintf("match_manual_competition_".date("Y-m-d").".csv");
			_csvheader($file_name);
			_csvrow(array("Date",
				"Name",
				"Country",
				""));
			$competitions = array();
			$competition = new competition;
			$err = $competition->query("SELECT * FROM t_competition");
			if ($err != ERR_NODATA)
				$this->checkError($err);
			while ($err == ERR_OK)
			{
				array_push($competitions,
					array("id_competition" => $competition->id_competition  ,"date_from" => $competition->date_from ,
						"name" => $competition->name,
						"country" => $competition->country,
						"has_results" => $competition->has_results
					));
				$err = $competition->fetch();
			}

			foreach ($competitions as $p) {
				$txt = "View inscriptions";
				if($p["has_results"] > 0){
					$txt = "View results";
				}
				_csvrow(array(
					date("j M Y",strtotime($p["date_from"])),
					$p["name"],
					$p["country"],
					$txt
				));

			}
			exit;
		}

		private function scrap_competition()
		{
//			access_token

			$params = array();
			$path = "";
			$params["__ust"] = "";
			$params["action"] = "competition.get_list";
			$params["month"] = "";
			$params["rank_group"] = "";
			$params["sort"] = -1;
			$params["year"] = "";



			// get player's personal info
			$data =_get_content($path, $params);
			if($data == false)
			{
				console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return null;
			}

			$personInfo = json_decode($data, true);
			return $personInfo;
		}

		function competition(){
			_set_template("normal");
			$data = $this->scrap_competition();
			$count = count($data);
			if($count >0){
				$db = db::getDB();
				$sql = "DELETE FROM t_competition"  ;
				$db->execute($sql);
			}
			for($i =0;$i<$count;$i++){
				$arrCom = $data[$i];
				$competition = new competition;
				if($arrCom["date_from"] != "" || $arrCom["date_to"] !="") {
					$competition->id_competition = $arrCom["id_competition"];
					$competition->date_from = $arrCom["date_from"];
					$competition->date_to = $arrCom["date_to"];
					$competition->name = $arrCom["name"];
					$competition->has_results = $arrCom["has_results"];
					$competition->city = $arrCom["city"];
					$competition->comp_year = $arrCom["comp_year"];
					$competition->prime_event = $arrCom["prime_event"];
					$competition->continent_short = $arrCom["continent_short"];
					$competition->has_logo = $arrCom["has_logo"];
					$competition->country = $arrCom["country"];
					$competition->id_country = $arrCom["id_country"];
					$err = $competition->save();
					if ($err != ERR_OK) {
					} else {
					}
				}
			}
			$this->forward("matchinfor");
		}

	}
?>