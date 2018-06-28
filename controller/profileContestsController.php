<?php

	class profileContestsController extends controller {

		public $err_login;

		public function __construct(){
			parent::__construct();
		}

		public function checkPriv($action, $utype)
		{
			parent::checkPriv($action, UTYPE_NONE);
		}

		public function contests(){
			_set_template("normal");

		}

		public function index() {
			$this->competitions = $this->getDataToDB();
		}

		function getData(){
			$data = $this->scrap_profile_contests($_GET['id_competition']);
			$data_contests = $data['contests'];
			$count = count($data_contests);
			if($count >0){
				$db = db::getDB();
				$sql = "DELETE FROM t_profile_contests WHERE  	id_competition=" . _sql($_GET['id_competition']);
				$db->execute($sql);
			}

			for($i =0;$i<$count;$i++){
				$arrCom = $data_contests[$i];
				$competition = new profilecontests;
				$competition->id_competition = $arrCom["id_competition"];
				$competition->id_fight = $arrCom["id_fight"];
				$competition->fight_no = $arrCom["fight_no"];
				$competition->country_short_blue = $arrCom["country_short_blue"];
				$competition->person_blue = $arrCom["person_blue"];
				$competition->country_short_white = $arrCom["country_short_white"];
				$competition->person_white = $arrCom["person_white"];
				$competition->duration = $arrCom["duration"];
				$competition->round_name = $arrCom["round_name"];
				$competition->ippon = $arrCom["ippon"];
				$competition->waza = $arrCom["waza"];
				$competition->yuko = $arrCom["yuko"];
				$competition->penalty = $arrCom["penalty"];
				$competition->ippon_b = $arrCom["ippon_b"];
				$competition->waza_b = $arrCom["waza_b"];
				$competition->yuko_b = $arrCom["yuko_b"];
				$competition->penalty_b = $arrCom["penalty_b"];
				$competition->ippon_w = $arrCom["ippon_w"];
				$competition->waza_w = $arrCom["waza_w"];
				$competition->yuko_w = $arrCom["yuko_w"];
				$competition->penalty_w = $arrCom["penalty_w"];
				$competition->contest_code_long = $arrCom["contest_code_long"];
				$err = $competition->save();
				if ($err != ERR_OK) {
				} else {
				}

			}
			$this->forward("profileContests?id_competition=".$_GET['id_competition']);
		}

		function getDataToDB(){
			$arrData= array();
			$competition = new profilecontests;
			$err = $competition->query("SELECT * FROM t_profile_contests WHERE id_competition="._sql($_GET['id_competition'])." ORDER BY  fight_no  desc,id asc");
			while ($err == ERR_OK)
			{

				$duration =  date ('i:s',strtotime($competition->duration));

				array_push($arrData,
					array("id_competition" => $competition->id_competition,
						"fight_no" => $competition->fight_no ,
						"country_short_blue" => $competition->country_short_blue,
						"person_blue" => $competition->person_blue,
						"country_short_white" => $competition->country_short_white,
						"person_white" => $competition->person_white,
						"duration" => $duration,
						"round_name" => $competition->round_name,
						"ippon_b"=>$competition->ippon_b,
						"waza_b"=>$competition->waza_b,
						"yuko_b"=>$competition->yuko_b,
						"penalty_b"=>$competition->penalty_b,
						"ippon_w"=>$competition->ippon_w,
						"waza_w"=>$competition->waza_w,
						"yuko_w"=>$competition->yuko_w,
						"penalty_w"=>$competition->penalty_w,
						"contest_code"=>$competition->contest_code_long,
						"contest_code_long"=>"https://judobase.ijf.org/#/competition/contest/".$competition->contest_code_long
					));
				$err = $competition->fetch();
			}
			return $arrData;
		}

		function download(){
			$data = $this->getDataToDB();
			ob_clean();
			$file_name = sprintf("match_manual_contents_".$_GET['id_competition']."_".date("Y-m-d").".csv");
			_csvheader($file_name);
			_csvrow(array("#ID",
				"",
				"",
				"",
				"",
				"",
				"i",
				"w",
				"y",
				"p",
				"",
				"Opp-i",
				"Opp-w",
				"Opp-y",
				"Opp-p",
				"Duration",
				"Round",
				"URL"
			));
			foreach ($data as $p) {
				_csvrow(array(
					$p["fight_no"],
					$p["country_short_white"],
					$p["person_white"],
					$p["person_blue"],
					$p["country_short_blue"],
					(int)($p["ippon_w"].$p["waza_w"].$p["yuko_w"]),
					$p["ippon_w"],
					$p["waza_w"],
					$p["yuko_w"],
					$p["penalty_w"],
					(int)($p["ippon_b"].$p["waza_b"].$p["yuko_b"]),
					$p["ippon_b"],
					$p["waza_b"],
					$p["yuko_b"],
					$p["penalty_b"],
					$p["duration"],
					$p["round_name"],
					$p["contest_code_long"]
				));

			}
			exit;

		}

		function downloadResults(){
			$this->saveResults($_GET['contest_code']);
			$data = $this->getResults($_GET['contest_code']);
			ob_clean();
			$file_name = sprintf("match_manual_results02_".$_GET['contest_code']."_".date("Y-m-d").".csv");
			_csvheader($file_name);
			_csvrow(array("ID",
				"Event-ID",
				"Duration",
				"Date",
				"Event",
				"Category",
				"Round",
				"Duration",
				"",
				"",

			));
			foreach ($data as $p) {
				_csvrow(array(
					$p["id_competition"],
					$p["id_event"],
					$p["duration"],
					date("j M Y",strtotime($p["competition_date"])),
					$p["competition_name"],
					$p["weight"]." kg",
					$p["round_name"],
					_minutes2str($p["time_sc"]),
					$p["color"],
					$p["name"]
				));

			}
			exit;

		}

		function getResults($contest_code){
			$arrData= array();
			$event = new event;
			$err = $event->query("SELECT * FROM t_event WHERE contest_code=" . _sql($contest_code) . " ORDER BY  id asc");
			while ($err == ERR_OK) {
				$duration = date('i:s', strtotime($event->duration));
				array_push($arrData,
					array("id_competition" => $event->id_competition,
						"contest_code" => $event->contest_code,
						"id_event" => $event->id_event,
						"duration" => $duration,
						"competition_date" => $event->competition_date,
						"competition_name" => $event->competition_name,
						"weight" => $event->weight,
						"round_name" => $event->round_name,
						"time_sc" => $event->time_sc,
						"color" => $event->color,
						"name" => $event->name,
					));
				$err = $event->fetch();
			}
			return $arrData;
		}

		function saveResults($contest_code){
			$data = $this->getEvent($contest_code);
			$data_contests = $data['contests'];
			$count = count($data_contests);
			$db = db::getDB();
			$sql = "DELETE FROM t_event WHERE  	contest_code=" . _sql($contest_code);
			$db->execute($sql);
			for ($i = 0; $i < $count; $i++) {
				$arrCom = $data_contests[$i];
				$arrEvnet = $arrCom["events"];
				if( count($arrEvnet)>0){
					for ($j = 0; $j < count($arrEvnet); $j++) {
						$event = new event;
						$event->id_competition = $arrCom["id_competition"];
						$event->contest_code = $contest_code;
						$event->id_event = $arrEvnet[$j]['id_event'];
						$event->duration = $arrCom["duration"];
						$event->competition_date = $arrCom["competition_date"];
						$event->competition_name = $arrCom["competition_name"];
						$event->weight = $arrCom["weight"];
						$event->round_name = $arrCom["round_name"];
						$event->time_sc = $arrEvnet[$j]['time_sc'];
						$arrtag = $arrEvnet[$j]['tags'];
						$name = "";
						for ($t = 0; $t < count($arrtag); $t++) {
							if ($arrtag[$t]['group_name'] != 'Score') {
								if ($name == "") {
									$name = $name . $arrtag[$t]['group_name'];
								} else {
									$name = $name . "/" . $arrtag[$t]['group_name'];

								}
							}
							if ($name == "") {
								$name = $name . $arrtag[$t]['name'];
							} else {
								$name = $name . "/" . $arrtag[$t]['name'];
							}
						}
						$event->name = $name;

						$arrAction = $arrEvnet[$j]['actors'];
						$color = "";
						for ($a = 0; $a < count($arrAction); $a++) {
							if ($arrAction[$a]['country_short'] == $arrCom['country_short_blue']) {
								$color = "B";
							}
							if ($arrAction[$a]['country_short'] == $arrCom['country_short_white']) {
								if($color == ""){
									$color = "W";
								}else{
									$color = "B/W";
								}
							}
						}
						$event->color = $color;
						$err1 = $event->save();
						if ($err1 != ERR_OK) {
						} else {
						}

					}

				}else{
					$event = new event;
					$event->id_competition = $arrCom["id_competition"];
					$event->contest_code = $contest_code;
					$event->id_event = "";
					$event->duration = $arrCom["duration"];
					$event->competition_date = $arrCom["competition_date"];
					$event->competition_name = $arrCom["competition_name"];
					$event->weight = $arrCom["weight"];
					$event->round_name = $arrCom["round_name"];
					$event->time_sc = 4*60;
					$err1 = $event->save();
				}
			}

		}

		function getEvent($contest_code){

			$params = array();
			$path = "";
			$params["__ust"] = "";
			$params["action"] = "contest.find";
			$params["contest_code"] =$contest_code;
			$params["part"] = 'info,score_list,media,events';


			// get player's personal info
			$data = _get_content($path, $params);
			if($data == false)
			{
				console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return null;
			}

			$personInfo = json_decode($data, true);
			return $personInfo;
		}

		function scrap_profile_contests($id_competition)
		{

			$params = array();
			$path = "";
			$params["__ust"] = "";
			$params["action"] = "contest.find";
			$params["id_competition"] =$id_competition;
			$params["id_weight"] = 0;
			$params["order_by"] = 'cnum';


			// get player's personal info
			$data = _get_content($path, $params);
			if($data == false)
			{
				console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return null;
			}

			$personInfo = json_decode($data, true);
			return $personInfo;
		}

	}
?>