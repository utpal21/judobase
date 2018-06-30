<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

	class competition extends model
	{
		public function __construct()
		{
			parent::__construct("t_competition",
				"id",
				array(
					"id_competition",
					"date_from",
					"date_to",
					"name",
					"has_results",
					"city",
					"comp_year",
					"prime_event",
					"continent_short",
					"has_logo",
					"country",
					"id_country",
					"unq_flag"),
				array("auto_inc" => true));
		}


	};
?>