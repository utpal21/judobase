<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

	class profilecontests extends model
	{
		public function __construct()
		{
			parent::__construct("t_profile_contests",
				"id",
				array(
					"id_competition",
					"id_fight",
					"fight_no",
					"country_short_blue",
					"person_blue",
					"country_short_white",
					"person_white",
					"duration",
					"round_name",
					"ippon",
					"waza",
					"yuko",
					"penalty",
					"ippon_b",
					"waza_b",
					"yuko_b",
					"penalty_b",
					"ippon_w",
					"waza_w",
					"yuko_w",
					"penalty_w",
					"contest_code_long"),
				array("auto_inc" => true));
		}


	};
?>


