<?php
/*---------------------------------------------------
    Developement:		DungNH
    Author:				DungNH
    Date:				2018/02/8
---------------------------------------------------*/
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

class PlayerinforController extends controller {
    public $err_login;
    private $scrap_base_url = "https://data.judobase.org/api/get_json";
    public function __construct(){
        parent::__construct();
    }

    public function checkPriv($action, $utype)
    {
        parent::checkPriv($action, UTYPE_NONE);
    }

    public function index() {
        $players = array();
        $player = new player();
        $err = $player->query("SELECT * FROM m_player");
        if ($err != ERR_NODATA)
            $this->checkError($err);

        while ($err == ERR_OK)
        {
            array_push($players,
                array("id_person" => $player->id_person,
                    "birth_date" => $player->birthday ,
                    "competitor" => $player->competitor,

                ));
            $err = $player->fetch();
        }
        $this->players = $players;

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

    private function scrap_statistics_info($id_person)
    {
        $path = "";
        $params = array();
        $params["action"] = "competitor.fights_statistics";
        $params["__ust"] = "";
        $params["id_person"] = $id_person;
        $statisticsData = $this->get_content($path, $params);

        if($statisticsData == false)
        {
            console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
            return null;
        }

        $jsonData = json_decode($statisticsData, true);
        return $jsonData;

    }
    private function scrap_wins_losses($id_person)
    {
        $path = "";
        $params = array();
        $params["action"] = "competitor.wins_losses";
        $params["__ust"] = "";
        $params["id_person"] = $id_person;
        $statisticsData = $this->get_content($path, $params);

        if($statisticsData == false)
        {
            console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
            return null;
        }

        $jsonData = json_decode($statisticsData, true);
        $rankingInfo = null;
        if(count($jsonData) != 0)
            $rankingInfo = $jsonData[0];
        return $jsonData;

    }
    private function scrap_ranking_info($id_person)
    {
        $path = "";
        $params = array();
        $params["__ust"] = "";
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
    private function scrap_wrl_competition($id_person)
    {
        $path = "";
        $params = array();
        $params["action"] = "competitor.wrl_competitions";
        $params["__ust"] = "";
        $params["id_person"] = $id_person;
        $params["only_actual_wrl"] = "1";
        $wrlData = $this->get_content($path, $params);
        if($wrlData == false)
        {
            console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
            return null;
        }
        $jsonWRLData = json_decode($wrlData, true);
        return $jsonWRLData;
    }

    private function scrap_person_info($person_id) {
        $params = array();
        $path="";
        $params["action"] = "competitor.info";
        $params["__ust"] = "";
        $params["id_person"] = $person_id;
        //get player info
        $data = $this->get_content($path, $params);
        if($data == false) {
            echo "get content failed \n";
            return null;
        }
        $personInfo = json_decode($data, true);
        return $personInfo;

    }
   private function scrap_results_info($person_id){
        $params = array();
        $path="";
        $params["action"] = "competitor.results";
        $params["id_person"] = $person_id;
        //get player info
        $data = $this->get_content($path, $params);
        if($data == false) {
            echo "get content failed \n";
            return null;
        }
        $personInfo = json_decode($data, true);
        return $personInfo;

    }
    private function scrap_person_list()
    {
        $path = "";
        $params = array();
        $params["action"] = "competitor.get_list";
        $data = $this->get_content($path, $params);
        if($data == false)
        {
            console('$this->get_content() failed: ' . __FUNCTION__ . '[' . __LINE__ . ']');
        }
        $jsonData = json_decode($data, true);
        $persons = $jsonData["feed"];
        return $persons;



    }
    public function scrap_player(){
        _set_template("normal");
        $data_persons=$this->scrap_person_list();
        $length = count($data_persons);
        for($i=0; $i < $length; $i++)
        {
            $data_player = $data_persons[$i];
            $player = new player();
            $where = "id_person=" . _sql($data_player["id_person"]) ;
            $err = $player->select($where, null, true);

            $bSave = false;
            if($err == ERR_NODATA)
            {
                $bSave = true;
            }
            else if($err == ERR_OK)
            {
                $bSave = true;
            }
            else
            {
            }
            if($bSave)
            {
                $player->id_person = $data_player["id_person"];
                $player->competitor = $data_player["family_name"]." ".$data_player["given_name"];
                $player->country = $data_player["country"];
                $player->birthday = $data_player["birth_date"];
                $player->player_first_name = $data_player["given_name"];
                $player->player_second_name = $data_player["family_name"];
                $err = $player->save();
                if($err != ERR_OK){

                }else{
                }
            }

        }
        $this->forward("playerinfor");

    }
    public function download_result()
    {
        $id_person = $_GET['id_person'];
        $playerResults = $this->scrap_results_info($id_person);
        $data_export = array();
        $count = 0;
        foreach($playerResults as $result)
        {
            $count++;
            $temp = array();
            for ( $i=0; $i < count($result); $i++){
                $temp['no'] = $count;
                $temp['id_competition'] =  $id_person;
                $temp['competition_date'] = date("j M Y",strtotime($result[$i]["competition_date"]));
                $temp['competition_name'] = $result[$i]["competition_name"];
                $temp['weight'] = $result[$i]["weight"]." kg";
                if($result[$i]["place"]=='tp'){
                    $temp['place'] = "-";
                }else {
                    $temp['place'] = $result[$i]["place"].".place";
                }
            }
            array_push($data_export, $temp);
        }
        array_unshift($data_export, array('No', 'ID', 'Date', 'Competition','Weight','Place'));
        $this->export_csv($data_export,"data_result_player_".$id_person."_". date("Y-m-d") . ".csv");
        exit();

    }
    public function download_list()
    {

        $id_person = $_GET['id_person'];
        $playerInfo = $this->scrap_person_info($id_person);
        $playerStatistics = $this->scrap_statistics_info($id_person);
        $playerRankInfor = $this->scrap_ranking_info($id_person);
        $dataWinLosses = $this->scrap_wins_losses($id_person);
        $length = count($playerInfo['categories']);
        $category="";
        if($length > 0 )
        {
            for( $i = 0;$i < $length; $i++)
            {
                $category .= $playerInfo['categories'][$i]." kg ";
            }
        }
        $data_export = array();
        $data_export['id'] = $id_person;
        $data_export['competitor'] = $playerInfo['family_name']." ".$playerInfo['given_name'];
        $data_export['birth_date'] = date("j M Y",strtotime($playerInfo["birth_date"]));
        $data_export['family_name'] = $playerInfo['family_name'];
        $data_export['given_name'] = $playerInfo['given_name'];
        $data_export['gender'] = $playerInfo['gender'];
        $data_export['age'] = $playerInfo['age']."(".date("j M Y",strtotime($playerInfo["birth_date"])).")";
        $data_export['categories'] = $category;
        $data_export['fav.tech'] = $playerInfo['ftechique'];
        $data_export['country'] = "(".$playerInfo['country_short'].")". $playerInfo['country'];
        $data_export['wrl_position'] = $playerRankInfor['weight']." kg ".$playerRankInfor['place']." place (".$playerRankInfor['points']." pts)";
        $data_export['world_ranking'] = $playerRankInfor['weight']." kg. "." #".$playerRankInfor['place']." (".$playerRankInfor['points']." pts)";;
        $data_export['number_of_competitions'] = count($dataWinLosses["detailed"]);
        $percent_win = $dataWinLosses["wins"]/$dataWinLosses['matches']*100;
        $data_export['fights_won'] = $dataWinLosses["wins"].' / '.$dataWinLosses['matches']." (".round($percent_win,2)." %)";
        $percent_lost = $dataWinLosses["losses"]/$dataWinLosses['matches']*100;
        $data_export['fights_lost'] = $dataWinLosses["losses"].' / '.$dataWinLosses['matches']." (".round($percent_lost,2)." %)";
        $data_export['avg_duration'] = $playerStatistics["avg_duration"];
        $data_export['shortest_contest_win'] = $playerStatistics["shortest_fight_win"]." - ".$playerStatistics["shortest_fight_win_comp"];
        $data_export['shortest_contest_lost'] = $playerStatistics["shortest_fight_lost"]. " - ".$playerStatistics["shortest_fight_lost_comp"];
        $data_export['longest_contest_win'] = $playerStatistics["longest_fight_win"]." - ".$playerStatistics["longest_fight_win_comp"];
        $data_export['longest_contest_lost'] = $playerStatistics["longest_fight_lost"]. " - ".$playerStatistics["longest_fight_lost_comp"];
        $percent_win_ippon = $playerStatistics["num_win_by_ippon"] / $playerStatistics['num_contests']*100;
        $percent_lost_ippon = $playerStatistics["num_lost_by_ippon"] / $playerStatistics['num_contests']*100;
        $data_export['won_by_ippon'] = $playerStatistics["num_win_by_ippon"]." / ".$playerStatistics["num_contests"]." - ".round($percent_win_ippon,2)." %" ;
        $data_export['lost_by_ippon'] = $playerStatistics["num_lost_by_ippon"]." / ".$playerStatistics["num_contests"]." - ".round($percent_lost_ippon,2)." %" ;

        $data = array();
        array_push($data,$data_export);
        array_unshift($data, array('ID', 'Competitor', 'Birth date','Family name','Given name','Gender','Age','Categories','Fav. Technique','Country',
            'WRL position', 'World ranking position','Number of competitions','Fights won','Fights lost', 'Average contest duration','Shortest contest win',
            'Shortest contest lost','Longest contest win','Longest contest lost','Won by ippon','Lost by ippon'));
        $this->export_csv($data,"data_list_player_".$id_person."_". date("Y-m-d") . ".csv");
        exit();
    }
    public function download_WRL()
    {
        $id_person = $_GET['id_person'];
        $playerWRL = $this->scrap_wrl_competition($id_person);
        $data_export = array();
        foreach($playerWRL as $wrl)
        {
            $temp2 = array();
            for ( $i=0; $i < count($wrl); $i++){
                $temp2['id_competitor'] =  $id_person;
                $temp2['date'] = date("j M Y",strtotime( $wrl["competition_date"]));
                $temp2['competition'] = $wrl["competition_name"];
                $temp2['category'] = $wrl["weight"]." kg";
                if($wrl["expiry_status"]==""){
                    $temp2['point'] = $wrl["points_orig"];
                } else {
                    $temp2['point'] = $wrl["points_orig"]."(".$wrl["expiry_status"]."%)";
                }
                $temp2['result'] = $wrl["place"];
            }
            array_push($data_export, $temp2);
        }
        array_unshift($data_export, array('ID', 'Date', 'Competition','Category','Points','Result'));
        $this->export_csv($data_export,"data_wrl_player_".$id_person."_". date("Y-m-d") . ".csv");
        exit();
    }

    function export_csv($data_export, $filename = "export.csv",$delimiter=",")
    {
        if (count($data_export) == 0) {
            return null;
        }
        ob_end_clean();
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        $file = fopen('php://output','w');
        foreach($data_export as $data_line) {
            fputcsv($file,$data_line,$delimiter);
        }
        fpassthru($file);
        exit();
    }


}
?>