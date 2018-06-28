<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

	class event extends model
	{
		public function __construct()
		{
			parent::__construct("t_event",
				"id",
				array(
					"contest_code",
					"id_competition",
					"id_event",
					"duration",
					"competition_date",
					"competition_name",
					"weight",
					"round_name",
					"time_sc",
					"color",
					"name"
				),
				array("auto_inc" => true));
		}


	};
?>


