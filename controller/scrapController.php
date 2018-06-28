<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

	define("PLAYER_AVARTAR_WIDTH", 150);
	define("PLAYER_AVARTAR_HEIGHT", 175);
	define("INTER_FLOAT_GAME_LEVEL_COUNT", 4);

	function is_valid_array($array)
	{
		if(!is_array($array))
			return false;

		if(count($array) == 0)
			return false;

		return true;
	}

	function is_array_key_exists($key, $array)
	{
		if(_is_empty($key))
			return false;

		if(!is_valid_array($array))
			return false;
		
		return array_key_exists($key, $array);
	}

	$debug = true;

	function console($msg)
	{
		global $debug;

		if($debug)
		{
			echo($msg."\n");
			flush();
			ob_flush();
		}
	}

	function cmp_ranking($a, $b)
	{
		return ($a["place"] < $b["place"]) ? -1 : 1;
	}

	if(!function_exists("array_column"))
	{

	    function array_column($array,$column_name)
	    {

	        return array_map(function($element) use($column_name){return $element[$column_name];}, $array);

	    }

	}
	
	class ScrapController extends controller {

		private $scrap_base_url = "https://data.judobase.org/api/get_json";

		private $tname_matching_table = array(
			"Olympic Games" => "オリンピック",
			"World Championships" => "世界選手権",
			"Masters" => "マスターズ",
			"Grand Slam" => "グランドスラム大会",
			"Grand Prix" => array(
				"DEU" => "ドイツ開催のグランプリ大会",
				"OTHERS" => "グランプ地大会"
				),
			"Asian Championships" => array(
				"Asian Games" => "アジア競技大会",
				"Asian Championships" => "アジア選手権"
				),
			"Asian Open" => "コンチネンタルオープン",
			"European Open" => "コンチネンタルオープン", 
			"Panamerican Open" => "コンチネンタルオープン",
			"Oceanian Open" => "コンチネンタルオープン",
			"Other Ranks" => "各種国際大会"
			);

		private $country_matching_table = array(
			"ALG" => "DZA", // Algeria
			"ASA" => "ASM", // American Samoa
			"ARU" => "ABW", // Aruba
			"BAH" => "BHS", // Bahamas
			"BRN" => "BHR", // Bahrain
			"BAN" => "BGD", // Bangladesh
			"BAR" => "BRB", // Barbados
			"BIZ" => "BLZ", // Belize
			"BER" => "BMU", // Bermuda
			"BHU" => "BTN", // Bhutan
			"BOT" => "BWA", // Botswana
			"IVB" => "VGB", // British Virgin Islands
			"BRU" => "BRN", // Brunei
			"BUL" => "BGR", // Bulgaria
			"BRU" => "BFA", // Burkina Faso
			"CAM" => "KHM", // Cambodia
			"CAY" => "CYM", // Cayman Islands
			"CHA" => "TCD", // Chad
			"CHI" => "CHL", // Chile
			"CGO" => "COG", // Congo
			"CRC" => "CRI", // Costa Rica
			"CRO" => "HRV", // Croatia
			"DEN" => "DNK", // Denmark
			"ESA" => "SLV", // El Salvador
			"GEQ" => "GNQ", // Equatorial Guinea
			"FIJ" => "FJI", // Fiji
			"GAM" => "GMB", // Gambia
			"GER" => "DEU", // Germany
			"GRN" => "GRD", // Grenada
			"GUA" => "GTM", // Guatemala
			"GUI" => "GIN", // Guinea
			"GBS" => "GNB", // Guinea-Bissau
			"HAI" => "HTI", // Haiti
			"HON" => "HND", // Honduras
			"INA" => "IDN", // Indonesia
			"IRI" => "IRN", // Iran
			"KUW" => "KWT", // Kuwait
			"LAT" => "LVA", // Latvia
			"LIB" => "LBN", // Lebanon
			"LES" => "LSO", // Lesotho
			"LBA" => "LBY", // Libya
			"MAD" => "MDG", // Madagascar
			"MAW" => "MWI", // Malawi
			"MAS" => "MYS", // Malaysia
			"MTN" => "MRT", // Mauritania
			"MRI" => "MUS", // Mauritius
			"MON" => "MCO", // Monaco
			"MGL" => "MNG", // Mongolia
			"MYA" => "MMR", // Myanmar
			"NEP" => "NPL", // Nepal
			"NED" => "NLD", // Netherlands
			"AHO" => "ANT", // Netherlands Antilles
			"NCA" => "NIC", // Nicaragua
			"NIG" => "NER", // Niger
			"NGR" => "NGA", // Nigeria
			"OMA" => "OMN", // Oman
			"PAR" => "PRY", // Paraguay
			"PHI" => "PHL", // Philippines
			"POR" => "PRT", // Portugal
			"PRU" => "PRI", // Puerto Rico
			"SKN" => "KNA", // Saint Kitts And Nevis
			"VIN" => "VCT", // Saint Vincent And The Grenadines
			"SAM" => "WSM", // Samoa
			"KSA" => "SAU", // Saudi Arabia
			"SEY" => "SYC", // Seychelles
			"SIN" => "SGP", // Singapore
			"SLO" => "SVN", // Slovenia
			"SOL" => "SLB", // Solomon Islands
			"RSA" => "ZAF", // South Africa
			"SRI" => "LKA", // Sri Lanka
			"SUD" => "SDN", // Sudan
			"SUI" => "CHE", // Switzerland
			"TAN" => "TZA", // Tanzania
			"TOG" => "TGO", // Togo
			"TGA" => "TON", // Tonga
			"TPE" => "TWN", // Taiwan
			"UAE" => "ARE", // United Arab Emirates
			"URU" => "URY", // Uruguay
			"VAN" => "VUT", // Vanuatu
			"VIE" => "VNM", // Viet Nam
			"ISV" => "VIR", // Virgin Islands
			"ZAM" => "ZMB", // Zambia
			"ZIM" => "ZWE"  // Zimbabwe
			);

		private $weight_world_rankings = array();

		public function __construct(){
			$this->_page_id = "scrap";
			parent::__construct();	
		}

		public function checkPriv($action, $utype)
		{
			parent::checkPriv($action, UTYPE_NONE);
		}

		public function index() {
			return "none/scrap_index";
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

		private function get_content($path, $params)
		{
			$url = $this->scrap_base_url;
			$url .= $path;

			$strParam = $this->make_params_string($params);
			$url .= $strParam;

			$data = file_get_contents($url);

			return $data;
		}

		private function get_tname_id($competition)
		{
			$tnameJP = null;			
			if(array_key_exists($competition["rank_name"], $this->tname_matching_table))
			{
				$tnameJP = $this->tname_matching_table[$competition["rank_name"]];
				if(is_array($tnameJP))
				{
					if($competition["rank_name"] == "Asian Championships")
					{
						foreach ($tnameJP as $key => $value)
						{
							if(strstr($competition["name"], $key))
							{
								$tnameJP = $value;
								break;
							}
						}

						if(is_array($tnameJP))
							$tnameJP = $tnameJP["Asian Championships"];
					}
					else if($competition["rank_name"] == "Grand Prix")
					{
						foreach ($tnameJP as $key => $value)
						{
							if($competition["country_short"] == $key)
							{
								$tnameJP = $value;
								break;
							}
						}

						if(is_array($tnameJP))
							$tnameJP = $tnameJP["OTHERS"];													
					}
				}
			}
			else
			{
				$tnameJP = $this->tname_matching_table["Other Ranks"];
			}

			$tname = new tname;
			$where = "tname_name=" . _sql($tnameJP);
			$err = $tname->select($where, null, true);

			if($err != ERR_OK)
				return null;

			return $tname->tname_id;
		}

		private function get_country_id($country_short)
		{
			if(array_key_exists($country_short, $this->country_matching_table))
				return $this->country_matching_table[$country_short];
			else
				return $country_short;
		}

		private function scrap_tournaments()
		{
			$path = "";
			$params = array();
			$params["action"] = "competition.get_list";
			$params["year"] = "";
			$params["month"] = "";
			$params["rank_group"] = "";
			$params["sort"] = -1;

			// get tournaments list
			$data = $this->get_content($path, $params);
			if($data == false)
			{
				console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return ERR_NODATA;
			}

			$jsonData = json_decode($data, true);
			
			$dateBegin = date("Y", strtotime("-5 Years"))."-01-01";
			$timeBegin = strtotime($dateBegin);

			foreach ($jsonData as $competition) 
			{
				$startTime = strtotime($competition["date_from"]);
				$startDate = date("Y-m-d H:i:s", $startTime);
				$endTime = strtotime($competition["date_to"]);
				$endDate = date("Y-m-d H:i:s", $endTime);

				if($startTime > $timeBegin)
				{
					$tournament = new tournament;

					$competition["country_short"] = $this->get_country_id($competition["country_short"]);

					$tname_id = $this->get_tname_id($competition);

					if($tname_id == null)
						continue;

					$where = "tournament_name_en=" . _sql($competition["name"]) . 
							" AND open_year=" . _sql($competition["comp_year"]) . 
							" AND country_id=" . _sql($competition["country_short"]);
					$err = $tournament->select($where, null, true);

					$bSave = false;
					if($err == ERR_NODATA)
					{
						$tournament->tname_id = $tname_id;
						$tournament->tournament_name = $competition["name"];
						$tournament->tournament_name_en = $competition["name"];						
						$tournament->open_year = $competition["comp_year"];
						$tournament->country_id = $competition["country_short"];
						$tournament->start_date = $startDate;
						$tournament->end_date = $endDate;
						$tournament->id_competition = $competition["id_competition"];
						$bSave = true;
					}
					else if($err == ERR_OK)
					{
						if(_is_empty($tournament->id_competition))
						{
							$tournament->id_competition = $competition["id_competition"];
							$bSave = true;
						}
					}
					else
					{
						console('$tournament->select() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
						continue;
					}

					if($bSave)
					{
						$this->start();
						$err = $tournament->save();
						if($err == ERR_OK)
						{
							$this->commit();
						}
						else
						{
							$this->rollback();						
							console('$tournament->save() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
						}
					}
				}
			}
			
			return ERR_OK;
		}

		private function get_weight_list(&$list)
		{
			$weight = new weight;
			$err = $weight->select("");
			if ($err != ERR_OK)
				return $err;

			while ($err == ERR_OK)
			{
				array_push($list, $weight->props);

				$err = $weight->fetch();
			}

			return ERR_OK;
		}

		private function scrap_ranking_info($id_person)
		{
			$path = "";
			$params = array();			
			$params["action"] = "competitor.wrl_current";
			$params["id_person"] = $id_person;			
			$rankingData = $this->get_content($path, $params);
			
			if($rankingData == false)
			{
				console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return null;
			}

			$jsonRankingData = json_decode($rankingData, true);

			$rankingInfo = null;

			if(count($jsonRankingData) != 0)
				$rankingInfo = $jsonRankingData[0];

			return $rankingInfo;
		}

		private function scrap_person_info($id_person)
		{
			$params = array();
			$path = "";
			$params["action"] = "competitor.info";
			$params["id_person"] = $id_person;

			// get player's personal info
			$data = $this->get_content($path, $params);
			if($data == false)
			{
				console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return null;
			}

			$personInfo = json_decode($data, true);
			return $personInfo;
		}

		private function scrap_players()
		{
			$path = "";
			$params = array();
			$params["action"] = "competitor.get_list";
			$params["country"] = 13; // JPN is country_id 13 in judobase.org

			$weights = array();
			$err = $this->get_weight_list($weights);
			if($err != ERR_OK)
			{
				console('$this->get_weight_list() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');				
				return $err;
			}

			$genders = array("male", "female");
			foreach ($weights as $weight)
			{
				// get the players list of the weight
				$weight_id = $weight["weight_id"];
				$params["weight"] = $weight_id;
				$data = $this->get_content($path, $params);
				if($data == false)
				{
					console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
					continue;
				}

				$jsonData = json_decode($data, true);
				$persons = $jsonData["feed"];
				
				foreach ($persons as $person) 
				{
					$id_person = $person["id_person"];

					$playerInfo = $this->scrap_person_info($id_person);
					if(!is_valid_array($playerInfo))
						continue;

					if($playerInfo["gender"] == $genders[$weight["gender"]-1])
					{
						$player = new player;

						$where = "player_first_name_en=" . _sql($playerInfo["given_name"]) . 
								" AND player_second_name_en=" . _sql($playerInfo["family_name"]) . 
								" AND birthday=" . _sql($playerInfo["birth_date"]); 

						$err = $player->select($where, null, true);

						$bSave = false;
						if($err == ERR_NODATA)
						{
							$player->player_first_name = $playerInfo["given_name_local"];
							$player->player_second_name = $playerInfo["family_name_local"];

							// in case of given_name_local contains family_name_local, too
							$first_name = $playerInfo["given_name_local"];							
							mb_regex_encoding( 'UTF-8');
							$matches = mb_split("　", $first_name);
							if($matches != false && count($matches)>1)
							{
								if(_is_empty($player->player_second_name))								
									$player->player_second_name = $matches[0];
								$player->player_first_name = $matches[1];
							}

							$player->player_first_name_en = $playerInfo["given_name"];
							$player->player_second_name_en = $playerInfo["family_name"];
							if(_is_empty($player->player_first_name) && _is_empty($player->player_second_name))
							{
								$player->player_first_name = $player->player_first_name_en;
								$player->player_second_name = $player->player_second_name_en;
							}
							$player->birthday = $playerInfo["birth_date"];
							$player->gender = $weight["gender"];
							$player->weight_id = $weight_id;
							$player->id_person = $id_person;
							$bSave = true;
						}
						else if($err == ERR_OK)
						{
							if(_is_empty($player->id_person))
							{
								$player->id_person = $id_person;
								$bSave = true;
							}
						}
						else
						{
							console('$player->select() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
							continue;
						}

						// get player's ranking info
						$rankingInfo = $this->scrap_ranking_info($id_person);
						if($rankingInfo)
						{
							if(_is_empty($player->champion_rank))
							{
								$player->champion_rank = $rankingInfo["place"];
								$bSave = true;
							}
							if(_is_empty($player->champion_point))
							{
								$player->champion_point = $rankingInfo["points"];
								$bSave = true;
							}
							if(_is_empty($player->olympics_rank))
							{
								$player->olympics_rank = $rankingInfo["ogq_place"];
								$bSave = true;
							}
							if(_is_empty($player->olympics_point))
							{
								$player->olympics_point = $rankingInfo["ogq_sum_points"];
								$bSave = true;
							}
						}

						if($bSave)
						{
							$this->start();
							$err = $player->save();
							if($err == ERR_OK)
							{
								$this->commit();
							}
							else
							{
								$this->rollback();
								console('$player->save() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
								continue;
							}
						}

						// get player's avatar
						$avatar_url = "https://78884ca60822a34fb0e6-082b8fd5551e97bc65e327988b444396.ssl.cf3.rackcdn.com/profiles/200/" . $id_person . ".jpg";

						if(!file_exists(PAVARTAR_PATH . $player->player_id . ".jpg"))
						{
							$image = file_get_contents($avatar_url);
							if($image != false)
							{
								$tmppath = _tmp_path("jpg");
								file_put_contents($tmppath, $image);
				
								_resize_userphoto($tmppath, "jpg", PLAYER_AVARTAR_WIDTH, PLAYER_AVARTAR_HEIGHT);
								_erase_old(TMP_PATH);

								$pos = strpos($tmppath, "tmp/");
								if ($pos !== FALSE)
									$tmppath = substr($tmppath, $pos);

								$this->start();
								$err = $player->update_avartar($tmppath);
								if($err == ERR_OK)
								{
									$this->commit();
								}
								else
								{
									$this->rollback();									
									console('$player->update_avartar() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
								}								
							}
						}
					}
				}
			}						
			
			return ERR_OK;
		}

		private function save_splayer($championInfo, $tournament_id, $gender, $weight_id)
		{
			$splayerName = $championInfo["family_name"] . " " . $championInfo["given_name"];
			$country_id = $this->get_country_id($championInfo["country_short"]);

			$superplayer = new superplayer;
			$where = "player_name=" . _sql($splayerName) . 
					" AND country_id=" . _sql($country_id) . 
					" AND weight_id=" . _sql($weight_id);

			$err = $superplayer->select($where, null, true);
			if($err != ERR_OK && $err != ERR_NODATA)
				return $err;

			$bSave = false;
			if($err == ERR_NODATA)
			{
				$superplayer->player_name = $splayerName;
				$superplayer->gender = $gender;
				$superplayer->weight_id = $weight_id;
				$superplayer->country_id = $country_id;				
				$superplayer->id_person = $championInfo["id_person"];
				$bSave = true;
			}
			else
			{
				if(_is_empty($superplayer->id_person))
				{
					$superplayer->id_person = $championInfo["id_person"];
					$bSave = true;
				}
			}
			
			if($bSave)
			{
				$err = $superplayer->save();
				if($err != ERR_OK)
					return $err;
			}

			$shistory = new shistory;
			$where = "player_id=" . _sql($superplayer->player_id) . 
					" AND tournament_id=" . _sql($tournament_id) . 
					" AND weight_id=" . _sql($weight_id);

			$err = $shistory->select($where, null, true);
			if($err != ERR_OK && $err != ERR_NODATA)
				return $err;

			if($err == ERR_NODATA)
			{
				$shistory->player_id = $superplayer->player_id;
				$shistory->tournament_id = $tournament_id;
				$shistory->weight_id = $weight_id;
				$shistory->add_point = tournament::get_spoint($tournament_id);

				$err = $shistory->save();

				if($err != ERR_OK)
					return $err;				
			}

			return ERR_OK;
		}

		private function scrap_super_players()
		{
			$path = "";
			$params = array();
			$params["action"] = "competition.results";

			// get olympic, world championshis and masters tournaments list
			$tournaments = array();			
			$err = tournament::get_stournaments($tournaments);
			if($err != ERR_OK)
			{
				console('tournament::get_stournaments() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return $err;
			}								

			// get weights list
			$weights = array();
			$err = $this->get_weight_list($weights);
			if($err != ERR_OK)
			{
				console('$this->get_weight_list() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return $err;
			}								

			$dateBegin = date("Y", strtotime("-5 Years"))."-01-01";
			$timeBegin = strtotime($dateBegin);
			$dateEnd = date("Y-m-d");
			$timeEnd = strtotime($dateEnd);

			foreach ($tournaments as $tournament) 
			{
				$id_competition = $tournament["id_competition"];
				$tournament_id = $tournament["tournament_id"];

				$time = strtotime($tournament["start_date"]);

				if($id_competition != null && $time >= $timeBegin && $time < $timeEnd)
				{
					$params["id_competition"] = $id_competition;
					
					// get the result list of the competition
					$data = $this->get_content($path, $params);
					if($data == false)
					{
						console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
						continue;
					}								

					$jsonData = json_decode($data, true);

					if(!is_array_key_exists("by_categories", $jsonData))
						continue;

					$categoryInfo = $jsonData["by_categories"];

					foreach ($weights as $weight)
					{
						$gender = $weight["gender"];
						$weight_id = $weight["weight_id"];

						if(!is_array_key_exists($gender, $categoryInfo))
							continue;

						$genderInfo = $categoryInfo[$gender];

						if(!is_array_key_exists($weight_id, $genderInfo))
							continue;

						$weightInfo = $genderInfo[$weight_id];

						if(!is_array_key_exists("persons", $weightInfo))
							continue;

						$arrayRanking = $weightInfo["persons"];

						if(count($arrayRanking) > 0)
						{
							$championInfo = $arrayRanking[0];

							if($championInfo["place"] == "1" && $championInfo["country_short"] != "JPN")
							{
								$this->start();
								$err = $this->save_splayer($championInfo, $tournament_id, $gender, $weight_id);
								if($err == ERR_OK)
								{
									$this->commit();
								}
								else
								{
									$this->rollback();
									console('$this->save_splayer() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
								}								
							}
						}
					}
				}
			}
			
			return ERR_OK;
		}

		private function get_id_person_player_list(&$players)
		{
			$player = new player;
			$where = "id_person IS NOT NULL";

			$err = $player->select($where);
			if($err != ERR_OK && $err != ERR_NODATA)
				return $err;

			while ($err == ERR_OK)
			{
				array_push($players, $player->props);

				$err = $player->fetch();
			}

			return ERR_OK;
		}

		private function get_tournament($id_competition)
		{
			if(_is_empty($id_competition))
				return null;

			$tournament = new tournament;
			$where = "id_competition=" . _sql($id_competition);
			$err = $tournament->select($where);

			if($err != ERR_OK)
				return null;


			return $tournament->props;
		}

		private function get_rank_id($rank_str)
		{
			if(_is_empty($rank_str))
				return null;

			$rank = new rank;
			$where = "rank_name=" . _sql($rank_str);

			$err = $rank->select($where);

			if($err != ERR_OK)
				return null;

			return $rank->rank_id;
		}

		private function get_splayer_id_at_time($id_person, $time)
		{			
			$superplayer = new superplayer;
			$sql = "SELECT s.player_id FROM m_superplayer s 
					LEFT JOIN m_shistory h ON s.player_id = h.player_id AND h.del_flag=0 
					LEFT JOIN t_tournament t ON t.tournament_id = h.tournament_id AND t.del_flag=0 
					WHERE s.del_flag=0 AND s.id_person=" . _sql($id_person) . " AND t.end_date<" . _sql($time);

			$err = $superplayer->query($sql);

			if($err != ERR_OK)
				return null;

			return $superplayer->player_id;
		}

		private function scrap_player_result_details($result, $id_person, $id_competition, $del_flag)
		{
			$path = "";
			$params = array();
			$params["action"] = "contest.find";
			$params["id_competition"] = $id_competition;
			$params["id_person"] = $id_person;

			$result_id = $result->result_id;

			// get player's contest list
			$data = $this->get_content($path, $params);

			if($data == false)
			{
				console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return ERR_NODATA;
			}								

			$jsonData = json_decode($data, true);
			$contests = $jsonData["contests"];

			$oneTimeWinRank = $this->get_rank_id("1勝");
			$oneThirtyTwoRank = $this->get_rank_id("1/32位");
			$oneSixTeenRank = $this->get_rank_id("1/16位");

			$bWin = false;			
			$updatedRank = $oneTimeWinRank; 

			if(is_array($contests) && count($contests) > 0)
			{				
				foreach ($contests as $contest) 
				{
					if($contest["id_winner"] == $id_person)
					{
						// update result rank if it was saved 1勝 as temporary
						$bWin = true;
						if($result->rank_id == $oneTimeWinRank) // 1勝
						{
							$round_name = $contest["round_name"];
							if($round_name == "best 32" && $updatedRank > $oneThirtyTwoRank ) // 1/32位
								$updatedRank = $oneThirtyTwoRank;
							if($round_name == "best 16" && $updatedRank > $oneSixTeenRank ) // 1/16位
								$updatedRank = $oneSixTeenRank;
						}

						// check if fit the super player
						$id_loser = ($contest["id_winner"] == $contest["id_person_blue"])
									 ? $contest["id_person_white"] 
									 : $contest["id_person_blue"];
						$loser_country = ($contest["id_winner"] == $contest["id_person_blue"])
											 ? $contest["country_short_white"] 
											 : $contest["country_short_blue"];

						if($loser_country != "JPN") // win other country player
						{
							$superplayer_id = $this->get_splayer_id_at_time($id_loser, $result->result_time);
							if($superplayer_id == null)
								continue;

							// add result detail
							$result_detail = new result_detail;
							$where = "result_id=" . $result_id . " AND superplayer_id=" . $superplayer_id;
							$err = $result_detail->select($where, null, true);

							$bSave = false;
							if($err == ERR_NODATA)
							{
								$result_detail->result_id = $result_id;
								$result_detail->superplayer_id = $superplayer_id;
								$result_detail->del_flag = $del_flag;
								$bSave = true;
							}
							else if($err == ERR_OK)
							{
								if($del_flag && ($result_detail->del_flag != $del_flag))
								{
									$result_detail->del_flag = $del_flag;
									$bSave = true;
								}
							}
							else
							{
								console('$result_detail->select() failed: result_id=(' . $result_id . ') superplayer_id=(' . $superplayer_id . ') ' . 
									__FUNCTION__ . '[' . __LINE__ . ']');
								continue;
							}

							if($bSave)
							{
								$this->start();
								$err = $result_detail->save();
								if($err == ERR_OK)
								{
									$this->commit();
								}
								else
								{
									$this->rollback();
									console('$result_detail->save() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
								}
							}
						}
					}
				}
			}

			// update or remove rank of result
			if($bWin == false)
			{
				$this->start();
				$err = $result->remove(true);
				if($err == ERR_OK)
				{
					$this->commit();
				}
				else
				{
					$this->rollback();
					console('$result->remove() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
					return $err;
				}

			}
			else if($updatedRank != $oneTimeWinRank)
			{
				$result->rank_id = $updatedRank;
				$err = $result->save();
				if($err == ERR_OK)
				{
					$this->commit();
				}
				else
				{
					$this->rollback();
					console('$result->save() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
					return $err;
				}				
			}

			return ERR_OK;
		}

		private function save_player_result($player_id, $id_person, $tournamentInfo, $resultInfo)
		{
			$tournament_id = $tournamentInfo["tournament_id"];
			$id_competition = $tournamentInfo["id_competition"];
			$weight_id = $resultInfo["id_weight"];

			$place = $resultInfo["place"];
			if(is_numeric($place))
				$place .= "位";
			else if($place == "tp") // participation
				$place = "1勝"; // suppose it is one time win

			$rank_id = $this->get_rank_id($place);

			if($rank_id == null)
			{
				console('$this->get_rank_id() failed: id_person=(' . $id_person . ') id_competition=(' . $id_competition . ') ' . 
					__FUNCTION__ . '[' . __LINE__ . ']');
				return ERR_NODATA;
			}

			$result = new result;
			$where = "player_id=" . _sql($player_id) . 
					" AND tournament_id=" . _sql($tournament_id) . 
					" AND weight_id=" . _sql($weight_id) . 
					" AND rank_id=" . _sql($rank_id);

			$err = $result->select($where, null, true);

			$bSave = false;
			if($err == ERR_NODATA)
			{
				$result->year = $tournamentInfo["open_year"];
				$result->tournament_id = $tournament_id;				
				$result->player_id = $player_id;
				$result->weight_id = $weight_id;
				$result->play_mode = 0;
				$result->rank_id = $rank_id;
				$result->result_time = $tournamentInfo["start_date"];
				$result->status = 0;
				$result->user_id = 1; // admin adds this result

				$bSave = true;
			}
			else if($err == ERR_OK)
			{
				if($result->user_id == 0) // 0 or null
				{
					$result->user_id = 1; // admin adds this result
					$bSave = true;
				}
			}
			else
			{
				console('$result->select() failed: ' . __FUNCTION__ . '[' . __LINE__ . '] Erro:(' . $err . ')' );					
				return $err;
			}

			if($bSave)
			{
				$this->start();
				$err = $result->save();
				if($err == ERR_OK)
				{
					$this->commit();
				}
				else
				{
					$this->rollback();
					console('$result->save() failed: ' . __FUNCTION__ . '[' . __LINE__ . '] Erro:(' . $err . ')' );					
					return $err;
				}
			}

			// scrap and add the result_detail that win superplayer
			$err = $this->scrap_player_result_details($result, $id_person, $id_competition, $result->del_flag);
			return $err;
		}

		private function scrap_players_results()
		{
			$path = "";
			$params = array();
			$params["action"] = "competitor.results";

			// get players list with id_person
			$players = array();
			$err = $this->get_id_person_player_list($players);
			if($err != ERR_OK)
			{
				console('$this->get_id_person_player_list() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return $err;
			}								

			$dateBegin = date("Y", strtotime("-5 Years"))."-01-01";
			$timeBegin = strtotime($dateBegin);

			foreach ($players as $player) 
			{
				$player_id = $player["player_id"];
				$id_person = $player["id_person"];

				// get player's result list
				$params["id_person"] = $id_person;
				$data = $this->get_content($path, $params);

				if($data == false)
				{
					console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
					continue;
				}								

				$jsonData = json_decode($data, true);

				if(count($jsonData) > 0)
				{				
					foreach ($jsonData as $key => $value) 
					{						
						if(is_valid_array($value))
						{
							$resultInfo = $value[0];
							$id_competition = $resultInfo["id_competition"];
							$tournamentInfo = $this->get_tournament($id_competition);

							if($tournamentInfo == null)
								continue;

							$time = strtotime($tournamentInfo["start_date"]);
							if($time < $timeBegin)
								continue;

							$err = $this->save_player_result($player_id, $id_person, $tournamentInfo, 
											$resultInfo);

							if($err != ERR_OK)
							{
								console('$this->save_player_result() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
							}
						}
					}
				}
			}
			
			return ERR_OK;
		}

		private function get_lf_tournaments_list(&$tournaments)
		{
			$dateBegin = date("Y", strtotime("-5 Years"))."-01-01";
			$dateEnd = date("Y-m-d");

			$tournament = new tournament;
			$sql = "SELECT t.tournament_id, t.id_competition, m.level_type FROM t_tournament t 
					LEFT JOIN m_tname m ON  t.tname_id = m.tname_id 
					WHERE t.del_flag=0 AND t.id_competition IS NOT NULL AND m.tname_type=2";
			$sql .= " AND t.start_date<" . _sql($dateEnd) . " AND t.start_date>=" . _sql($dateBegin);
			$order = "t.tournament_id ASC";

			$err = $tournament->query($sql, array("order" => $order));

			if($err != ERR_OK)
				return $err;

			while ($err == ERR_OK)
			{
				array_push($tournaments, array('tournament_id'=>$tournament->tournament_id, 
												'id_competition'=>$tournament->id_competition,
												'level_type'=>$tournament->level_type));
				$err = $tournament->fetch();
			}

			return ERR_OK;
		}

		private function scrap_weight_world_rankings()
		{
			$path = "";
			$params = array();
			$params["action"] = "wrl.by_category";
			$params["limit"] = 3000;
			$params["part"] = "info,points";

			$genders = ["m", "f"]; // male, female

			foreach ($genders as $gender)
			{
				$params["gender"] = $gender;

				$data = $this->get_content($path, $params);
				if($data == false)
					return ERR_NODATA;

				$jsonData = json_decode($data, true);
				if(!is_array_key_exists("categories", $jsonData))
					return ERR_NODATA;

				$categories = $jsonData["categories"];
				if(!is_valid_array($categories))
					return ERR_NODATA;

				foreach ($categories as $key => $value) 
				{
					$weight_id = $key;
					$weightRankings = array();
					$personRankings = $value["competitors"];
					foreach ($personRankings as $personRanking) 
					{
						$id_person = $personRanking["id_person"];
						$place = $personRanking["place"];
						$country_id = $this->get_country_id($personRanking["country_short"]);
						$short_name = substr($personRanking["family_name"], 0, 4);
						$weightRanking = array(
							"id_person" => $id_person,
							"place" => $place,
							"country_id" => $country_id,
							"short_name" => $short_name
							);
						array_push($weightRankings, $weightRanking);
					}

					$this->weight_world_rankings[$weight_id] = $weightRankings;
				}
			}

			return ERR_OK;
		}

		private function get_weight_world_ranking($weight_id, $id_person)
		{
			if(!is_valid_array($this->weight_world_rankings))
				return null;

			$weightRankings = $this->weight_world_rankings[$weight_id];
			$idx = array_search($id_person, array_column($weightRankings, 'id_person'));
			if($idx === false)
				return null;

			return $weightRankings[$idx];
		}

		private function get_weight_high_rankings($weight_id, $competitors, &$rankings, $max_count)
		{
			// get all rankings of competitors
			for($i=0; $i<count($competitors); $i++)
			{
				// get competitor's ranking info
				$id_person = $competitors[$i];
				$rankingInfo = $this->get_weight_world_ranking($weight_id, $id_person);

				if(!is_valid_array($rankingInfo))
					continue;

				if($rankingInfo["country_id"] == "JPN")
					continue;

				array_push($rankings, $rankingInfo);
			}

			if(!is_valid_array($rankings))
				return ERR_NODATA;

			// sort rankings by world ranking
			usort($rankings, "cmp_ranking");

			if(count($rankings) > $max_count)
				array_splice($rankings, $max_count);

			return ERR_OK;
		}

		private function calc_level($rankings, $threshold, $max_count)
		{
			if(_is_empty($threshold) || !is_numeric($threshold))
				return -1;

			if(!is_valid_array($rankings))
				return -1;

			// calc average of ranking
			$count = count($rankings);
			$avg = 0;
			for($i=0; $i<$count; $i++)
				$avg += $rankings[$i]["place"];

			for($i=$count; $i<$max_count; $i++)
				$avg += $threshold;

			$avg /= $max_count;

			$level = 3; // "D"

			for($i = 0; $i<INTER_FLOAT_GAME_LEVEL_COUNT; $i++)
			{
				$percent = 10 * ($i + 1);
				if($i == 0)
					$begin = 0;
				else
					$begin = round(($percent - 10) * $threshold / 20 + 5 + 1) / 10;

				if($i == INTER_FLOAT_GAME_LEVEL_COUNT-1)
					$end = 0xffffffff;
				else
					$end = round($percent * $threshold / 20 + 5) / 10;

				if($avg >= $begin && $avg <= $end)
				{
					$level = $i;
					break;
				}
			}

			return $level;
		}

		private function save_tournament_weight_high_rankings($tournament_id, $gender, $weight_id, $rankings)
		{
			if(!is_valid_array($rankings))
				return ERR_NODATA;

			$count = count($rankings);

			for($i=0; $i<$count; $i++)
			{
				$ranking = $rankings[$i];

				$trank = new trank;
				$where = "tournament_id=" . _sql($tournament_id) . 
						" AND gender=" . _sql($gender) . 
						" AND weight_id=" . _sql($weight_id) . 
						" AND ijf_rank=" . _sql($i);

				$err = $trank->select($where, null, true);

				$bSave = false;
				if($err == ERR_NODATA)
				{
					$trank->tournament_id = $tournament_id;
					$trank->gender = $gender;
					$trank->weight_id = $weight_id;
					$trank->ijf_rank = $i;
					$trank->rank = $ranking["place"];
					$trank->country_id = $ranking["country_id"];
					$trank->short_name = $ranking["short_name"];
					$bSave = true;
				}
				else if($err == ERR_OK)
				{
					if($trank->rank == 0) // 0 or null
					{
						$trank->rank = $ranking["place"];
						$bSave = true;
					}

					if(_is_empty($trank->country_id))
					{
						$trank->country_id = $ranking["country_id"];
						$bSave = true;						
					}

					if(_is_empty($trank->short_name))
					{
						$trank->short_name = $ranking["short_name"];
						$bSave = true;						
					}					
				}
				else
				{
					return $err;
				}

				if($bSave)
				{
					$err = $trank->save();
					if($err != ERR_OK)
						return $err;					
				}
			}
			
			return ERR_OK;
		}

		private function save_tournament_weight_level($tournament_id, $weight_id, $level)
		{
			$tlevel = new tlevel;
			$where = "tournament_id=" . _sql($tournament_id) . 
					" AND weight_id=" . _sql($weight_id);

			$err = $tlevel->select($where, null, true);

			$bSave = false;
			if($err == ERR_NODATA)
			{
				$tlevel->tournament_id = $tournament_id;
				$tlevel->weight_id = $weight_id;
				$tlevel->level = $level;
				$bSave = true;
			}
			else if($err == ERR_OK)
			{
				if($tlevel->level === null) // not 0, but null
				{
					$tlevel->level = $level;
					$bSave = true;
				}
			}
			else
			{
				return $err;
			}

			if($bSave)
			{
				$err = $tlevel->save();
				if($err != ERR_OK)
					return $err;				
			}

			return ERR_OK;
		}

 		// determine international level float tournament's each weight level.
		private function scrap_lf_tournaments_info()
		{
			$path = "";
			$params = array();
			$params["action"] = "competition.competitors";

			// get level float tournament list
			$tournaments = array();
			$err = $this->get_lf_tournaments_list($tournaments);
			if($err != ERR_OK)
			{
				console('$this->get_lf_tournaments_list() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return $err;
			}

			// get weights list
			$weights = array();
			$err = $this->get_weight_list($weights);
			if($err != ERR_OK)
			{
				console('$this->get_weight_list() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
				return $err;
			}

			// scrap the world rankings for all weights
			$err = $this->scrap_weight_world_rankings();			
			if($err != ERR_OK)
			{
				console('$this->scrap_weight_world_rankings() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');				
				return $err;
			}

			foreach ($tournaments as $tournament) 
			{
				$id_competition = $tournament["id_competition"];
				$tournament_id = $tournament["tournament_id"];

				$params["id_competition"] = $id_competition;

				foreach ($weights as $weight)
				{
					$gender = $weight["gender"];

					// get the competitor list of the weight						
					$weight_id = $weight["weight_id"];
					$params["id_weight"] = $weight["weight_id"];

					$data = $this->get_content($path, $params);
					if($data == false)
					{
						console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
						continue;
					}								

					$jsonData = json_decode($data, true);
					if(!is_array_key_exists("categories", $jsonData))
						continue;

					$categories = $jsonData["categories"];
					if(!is_array_key_exists($gender, $categories))
						continue;

					$genderInfo = $categories[$gender];
					if(!is_array_key_exists($weight_id, $genderInfo))
						continue;

					$weightInfo = $genderInfo[$weight_id];
					if(!is_array_key_exists("persons", $weightInfo))
						continue;

					$persons = $weightInfo["persons"];
					$competitors = array_keys($persons);
					$rankings = array();

					$max_count = 8;
					if($tournament["level_type"] == 1)
						$max_count = 4;

					$err = $this->get_weight_high_rankings($weight_id, $competitors, $rankings, $max_count);
					if($err != ERR_OK)
					{
						console('$this->get_weight_high_rankings() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
						continue;
					}

					$this->start();
					$err = $this->save_tournament_weight_high_rankings($tournament_id, $gender, $weight_id, $rankings);
					if($err != ERR_OK)
					{
						$this->rollback();
						console('$this->save_tournament_weight_high_rankings() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
						continue;
					}

					$threshold = $weight["level_threshold"];
					$level = $this->calc_level($rankings, $threshold, $max_count);
					if($level == -1)
					{
						$this->rollback();
						console('$this->calc_level() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
						continue;						
					}

					$err = $this->save_tournament_weight_level($tournament_id, $weight_id, $level);
					if($err == ERR_OK)
					{
						$this->commit();
					}
					else
					{
						$this->rollback();
						console('$this->save_tournament_weight_level() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
					}					
				}
			}
			
			return ERR_OK;

		}

		public function scrap()
		{
			global $debug;

			if($debug)
			{
				error_reporting(E_ALL);
				ob_implicit_flush();
			}

			console('start scrapping[time: ' . date("Y-m-d H:i:s") . ']');

			console('start scrapping tournaments.');
			$err = $this->scrap_tournaments();
			console('end scrapping tournaments[result:' . $err . ']');

			console('start scrapping players.');
			$err = $this->scrap_players();
			console('end scrapping players[result:' . $err . ']');

			console('start scrapping super players.');			
			$err = $this->scrap_super_players();
			console('end scrapping super players[result:' . $err . ']');

			console('start scrapping players results.');			
			$err = $this->scrap_players_results();
			console('end scrapping players results[result:' . $err . ']');

			console('start level floating turnaments each weight level info.');			
			$err = $this->scrap_lf_tournaments_info();
			console('end level floating turnaments each weight level info[result:' . $err . ']');

			console('end scrapping[time: ' . date("Y-m-d H:i:s") . ']');
		}
	}
?>