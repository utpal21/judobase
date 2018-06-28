<?php
    /*---------------------------------------------------
        Project Name:       Judo Point System
        Developement:       
        Author:             Ken
        Date:               2015/06/04
    ---------------------------------------------------*/

    class trank extends model 
    {
        public function __construct()
        {
            parent::__construct("t_trank",
                "trank_id",
                array(
                    "tournament_id",
                    "gender",
                    "weight_id",
                    "ijf_rank",
                    "rank",
                    "country_id",
                    "short_name"),
                array("auto_inc" => true));
        }
    };
?>