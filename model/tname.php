<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

	class user extends model 
	{
		public function __construct()
		{
			parent::__construct("m_tname",
				"tname_id",
				array(
					"tname_name",
					"tname_name_en",
					"tname_type",
					"level_type",
					"start_point"),
				array("auto_inc" => true));
		}
	};
?>