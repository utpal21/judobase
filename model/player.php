<?php
	/*---------------------------------------------------
		Project Name:		Judo Point System
		Developement:		
		Author:				Ken
		Date:				2015/06/04
	---------------------------------------------------*/

	class player extends model 
	{
		public function __construct()
		{
			parent::__construct("m_player",
				"id",
				array(
					"id_person",
					"competitor",
					"player_first_name",
					"player_second_name",
					"country",
					"country_short",
					"birthday"),
				array("auto_inc" => true));
		}


	};
?>