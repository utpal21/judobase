<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

	class tournament extends model
	{
		public function __construct()
		{
			parent::__construct("t_tournament",
				"id",
				array(
					"tournament_id",
					"tname_id",
					"tournament_name",
					"tournament_name_en",
					"open_year",
					"country_id",
					"start_date",
					"end_date",
					"id_competition"),
				array("auto_inc" => true));
		}


	};
?>