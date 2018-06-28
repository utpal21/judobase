<?php
	/*---------------------------------------------------
		Project Name:		Judo Point System
		Developement:		
		Author:				Ken
		Date:				2015/06/04
	---------------------------------------------------*/

	class result extends model 
	{
		public function __construct()
		{
			parent::__construct("t_result",
				"result_id",
				array(
					"year",
					"tournament_id",
					"player_id",
					"weight_id",
					"play_mode",
					"rank_id",
					"team_rank_id",
					"result_time",
					"status",
                    "user_id"),
				array("auto_inc" => true));
		}

        static public function remove_tournament_results($tournament_id)        
        {
        	if(_is_empty($tournament_id))
        		return ERR_OK;

        	$result = new result;
        	$where = "tournament_id=" . _sql($tournament_id);
        	$err = $result->select($where);

        	if($err != ERR_NODATA && $err != ERR_OK)
        		return $err;

        	while($err == ERR_OK)
        	{
        		$result_id = $result->result_id;
        		$err = result_detail::remove_details($result_id);
        		$err = $result->remove();
        		$err = $result->fetch();
        	}

            return ERR_OK;
        }

        static public function remove_player_results($player_id)        
        {
        	if(_is_empty($player_id))
        		return ERR_OK;

        	$result = new result;
        	$where = "player_id=" . _sql($player_id);
        	$err = $result->select($where);

        	if($err != ERR_NODATA && $err != ERR_OK)
        		return $err;

        	while($err == ERR_OK)
        	{
        		$result_id = $result->result_id;
        		$err = result_detail::remove_details($result_id);
        		$err = $result->remove();
        		$err = $result->fetch();
        	}

            return ERR_OK;        	
        }

        static public function check_exist($player_id, $tournament_id, $play_mode, $weight_id, $rank_id, $result_id)
        {
            $result = new result;
            $from_where = "FROM t_result WHERE del_flag=0";

            // first check if player has the different rank in the same tournament            
            $from_where .= " AND tournament_id=" . _sql($tournament_id);
            $from_where .= " AND play_mode=" . _sql($play_mode);
            $from_where .= " AND weight_id=" . _sql($weight_id);
            $from_where .= " AND player_id=" . _sql($player_id);

            if($result_id)
                $from_where .= " AND result_id!=" . _sql($result_id);

            $query = "SELECT COUNT(*) " . $from_where;
            $total = $result->scalar($query);

            if($total > 0)
                return true;

            $from_where = "FROM t_result WHERE del_flag=0";            
            
            // second check if there is the result of the same rank in the same tournament
            if($play_mode == 1)
                return false;

            $from_where .= " AND tournament_id=" . _sql($tournament_id);
            $from_where .= " AND play_mode=" . _sql($play_mode);
            $from_where .= " AND weight_id=" . _sql($weight_id);
            $from_where .= " AND rank_id=" . _sql($rank_id);

            if($result_id)
                $from_where .= " AND result_id!=" . _sql($result_id);

            $query = "SELECT COUNT(*) " . $from_where;
            $total = $result->scalar($query);

            $ret = false;
            if($rank_id <= 2)
            {
                if($total > 0)
                    $ret = true;
            }
            else if($rank_id <= 5)
            {
                if($total > 1)
                    $ret = true;
            }

            return $ret;
        }

        static public function getOnePlayer($player_id, $calc_date, &$ret)
        {
            $results = array();
            $result = new result;

            $sql = "SELECT c.tournament_name, DATEDIFF(" . _sql($calc_date) . ", c.end_date) days, c.tname_id, 
                CASE WHEN a.play_mode = 0 THEN '個人戦' ELSE '団体戦' END play_mode_name, 
                d.weight_name, e.rank_name, f.start_point, e.vpercent, e1.vpercent team_percent, g.vpoint, f.tname_type, f.level_type, h.level, a.*
                FROM t_result a 
                LEFT JOIN t_tournament c ON c.open_year = a.year and c.tournament_id = a.tournament_id
                LEFT JOIN m_weight d ON d.weight_id = a.weight_id
                LEFT JOIN m_rank e ON e.rank_id = a.rank_id
                LEFT JOIN m_rank e1 ON e1.rank_id = a.team_rank_id
                LEFT JOIN m_tname f ON f.tname_id = c.tname_id
                LEFT JOIN m_vpoint g ON g.tname_id = f.tname_id AND g.rank_id = e.rank_id
                LEFT JOIN t_tlevel h ON h.tournament_id = a.tournament_id AND h.weight_id = a.weight_id 
                WHERE a.del_flag = 0 AND a.player_id = " . _sql($player_id) . " HAVING days > 0  
                ORDER BY c.end_date DESC";

            $err = $result->query($sql);
            if ($err != ERR_OK && $err != ERR_NODATA)
                return $err;

            $national = 0; //国内大会合計
            $international = 0; //国際大会合計
            $top_one = array(0, 0, 0); //直近1年（100%） 国際大会ポイント獲得率
            $top_two = array(0, 0, 0); //1年以上2年未満（50%） 国際大会ポイント獲得率
            $cnt_one = 0;
            $percent_one = 0;
            $cnt_two = 0;
            $percent_two = 0;

            while ($err == ERR_OK)
            {
                //
                if($result->status == 1){
                    //出場基礎点
                    $start_point = $result->start_point;
                    if($result->tname_type != 2){
                        //レベル固定大会
                        $vpoint = $result->vpoint;
                        $vpercent = $result->vpercent;
                    }
                    else{
                        $level = $result->level;
                        if ($result->level_type == 2) // 一律Dレベル
                            $level = 3;
                        //レベル変動大会
                        $err = result::getVictory($result->tournament_id, $result->weight_id, $result->rank_id, $level, $victory);
                        if ($err != ERR_OK)
                            return $err;
                        $vpoint = $victory["vpoint"];
                        $vpercent = $victory["vpercent"];
                    }
                    //有力選手勝利
                    $err = result::getAddPoint($result->result_id, $calc_date, $result->weight_id, $add_point);
                    if ($err != ERR_OK)
                        return $err;

                    if($result->play_mode == 1){ //団体戦勝利 point
                        $vpoint = $vpoint * $result->team_percent / 100;
                    }

                    if($result->days > 365){ //1年以上2年未満（50%）
                        $start_point = round($start_point / 2);
                        $vpoint = round($vpoint / 2);
                        $add_point = round($add_point / 2);
                    }

                    if($result->play_mode == 1){ //団体戦勝利 point
                        if(result::getSingle($result->tournament_id, $result->player_id) != 0)
                            //個人戦・団体戦の両試合に出場する選手の出場基礎点は重複させない
                            $value = $vpoint + $add_point;
                        else
                            $value = $start_point + $vpoint + $add_point;
                    }
                    else{
                        $value = $start_point + $vpoint + $add_point;
                    }
                    $result->add_point = $add_point;
                    $result->value = $value;

                    if($result->tname_type == 0){
                        if($result->days <= 365*3) //3年未満
                            $national = $national + $value;
                    }
                    else{
                        /*
                        if($result->days <= 365*3) //3年未満                        
                            $international = $international + $value;
                        */

                        if($result->days <= 365){ //直近1年
                            $cnt_one ++;
                            $percent_one = $percent_one + $vpercent;

                            if($value > $top_one[0]){
                                $top_one[2] = $top_one[1];
                                $top_one[1] = $top_one[0];
                                $top_one[0] = $value;
                            }
                            if($value < $top_one[0] && $value > $top_one[1]){
                                $top_one[2] = $top_one[1];
                                $top_one[1] = $value;
                            }
                            if($value < $top_one[1] && $value > $top_one[2]){
                                $top_one[2] = $value;
                            }
                        }else if($result->days > 365 && $result->days <= 730){ //1年以上2年未満
                            $cnt_two ++;
                            $percent_two = $percent_two + $vpercent;

                            if($value > $top_two[0]){
                                $top_two[2] = $top_two[1];
                                $top_two[1] = $top_two[0];
                                $top_two[0] = $value;
                            }
                            if($value < $top_two[0] && $value > $top_two[1]){
                                $top_two[2] = $top_two[1];
                                $top_two[1] = $value;
                            }
                            if($value < $top_two[1] && $value > $top_two[2]){
                                $top_two[2] = $value;
                            }
                        }
                    }
                }

                array_push($results, $result->props);
                $err = $result->fetch();
            }
            if($cnt_one != 0)
                $percent_one = round($percent_one / $cnt_one, 1);
            if($cnt_two != 0)
                $percent_two = round($percent_two / $cnt_two, 1);

            $international = $top_one[0] + $top_one[1] + $top_one[2] + $top_two[0] + $top_two[1] + $top_two[2];

            $pointsum = array("national" => $national, "international" => $international);
            $percent = array("percent_one" => $percent_one, "percent_two" => $percent_two);

            $ret = array("err_code" => ERR_OK, "results" => $results, "pointsum" => $pointsum, "percent" => $percent, "top_one" => $top_one, "top_two" => $top_two);
            return ERR_OK;
        }

        //有力選手勝利 point
        static public function getAddPoint($result_id, $calc_date, $weight_id, &$add_point)
        {
            $result = new result;

            $sql = "SELECT DATEDIFF(" . _sql($calc_date) . ", e.end_date) days, d.add_point, d.weight_id
                FROM t_result_detail a
                LEFT JOIN t_result b ON a.result_id = b.result_id
                LEFT JOIN m_superplayer c ON a.superplayer_id = c.player_id
                LEFT JOIN m_shistory d ON d.player_id = c.player_id 
                LEFT JOIN t_tournament e ON d.tournament_id = e.tournament_id 
                WHERE a.del_flag = 0 AND b.result_id = " . _sql($result_id) . " HAVING days > 0";

            $err = $result->query($sql);
            if ($err != ERR_OK && $err != ERR_NODATA)
                return $err;

            $add_point = 0;

            while ($err == ERR_OK)
            {
                //過去4年間のうち
                if($result->days <= 3 * 365 + 366){
                    if($weight_id == $result->weight_id)
                        $add_point = $add_point + $result->add_point;
                    else
                        //別の階級の試合に出た場合は、持ち点が半分になる
                        $add_point = $add_point + $result->add_point / 2;
                }
                $err = $result->fetch();
            }

            return ERR_OK;
        }

        //レベル変動大会 勝利 point
        static public function getVictory($tournament_id, $weight_id, $rank_id, $level, &$victory)
        {
            //$level = result::getTournamentLevel($tournament_id, $weight_id);

            $result = new result;
            $sql = "SELECT b.vpercent, a.vpoint 
                FROM m_vpoint a
                LEFT JOIN m_rank b on a.rank_id = b.rank_id
                WHERE a.del_flag = 0 AND a.tname_id = 0 
                 AND a.level = " . _sql($level) . 
                " AND a.rank_id = " . _sql($rank_id);

            $err = $result->query($sql);
            if ($err != ERR_OK && $err != ERR_NODATA)
                return $err;

            $victory = array("vpoint" => $result->vpoint, "vpercent" => $result->vpercent);

            return ERR_OK;
        }

        //個人戦 check
        static public function getSingle($tournament_id, $player_id)
        {
            $result = new result;
            $sql = "SELECT COUNT(play_mode) FROM t_result WHERE del_flag = 0 AND play_mode = 0 AND tournament_id = " . _sql($tournament_id) . " AND player_id = " . _sql($player_id);
            $cnt = $result->scalar($sql);

            return $cnt;
        }

        static public function getChampion($player_id, $calc_date, &$champions)
        {
            $champions = array();
            $champion = new result;

            $sql = "SELECT DATEDIFF(" . _sql($calc_date) . ", c.end_date) days, c.open_year, c.tournament_id, c.tournament_name, d.tname_name, e.player_name, f.weight_name, b.weight_id, a.* 
                FROM t_result_detail a
                LEFT JOIN t_result b ON a.result_id = b.result_id
                LEFT JOIN t_tournament c ON b.year = c.open_year and b.tournament_id = c.tournament_id
                LEFT JOIN m_tname d ON c.tname_id = d.tname_id 
                LEFT JOIN m_superplayer e ON a.superplayer_id = e.player_id
                LEFT JOIN m_weight f ON e.weight_id = f.weight_id
                WHERE a.del_flag = 0 AND b.player_id = " . _sql($player_id) .
                " HAVING days > 0 ORDER BY c.open_year DESC";

            $err = $champion->query($sql);
            if ($err != ERR_OK && $err != ERR_NODATA)
                return $err;

            while ($err == ERR_OK)
            {
                $champion->point_sum = result::getChampionPoint($champion->superplayer_id, $calc_date, $champion->weight_id);
                array_push($champions, $champion->props);
                $err = $champion->fetch();
            }

            return ERR_OK;
        }

        //有力選手勝利ポイント
        //オリンピック、世界選手権、マスターズのチャンピオン(過去4年間の間)に勝利した場合に勝利ポイントが別途加算される。
        static public function getChampionPoint($champion_id, $calc_date, $weight_id)
        {
            $result = new result;

            $sql = "SELECT DATEDIFF(" . _sql($calc_date) . ", e.end_date) days, d.add_point, d.weight_id
                FROM m_superplayer c
                LEFT JOIN m_shistory d ON d.player_id = c.player_id 
                LEFT JOIN t_tournament e ON d.tournament_id = e.tournament_id 
                WHERE c.del_flag = 0 AND c.player_id = " . _sql($champion_id) . 
                " HAVING days > 0";

            $err = $result->query($sql);
            if ($err != ERR_OK && $err != ERR_NODATA)
                return 0;

            $add_point = 0;

            while ($err == ERR_OK)
            {
                //過去4年間のうち
                if($result->days <= 3 * 365 + 366){
                    if($weight_id == $result->weight_id)
                        $add_point = $add_point + $result->add_point;
                    else
                        //別の階級の試合に出た場合は、持ち点が半分になる
                        $add_point = $add_point + $result->add_point / 2;
                }
                $err = $result->fetch();
            }

            return $add_point;
        }

	};
?>