<?php
	/*---------------------------------------------------
		Project Name:		Judo Point System
		Developement:		
		Author:				Ken
		Date:				2015/06/04
	---------------------------------------------------*/

	class weight extends model 
	{
		public function __construct()
		{
			parent::__construct("m_weight",
				"weight_id",
				array(
					"gender",
					"weight_name",
					"weight_size",
					"level_threshold"),
				array("auto_inc" => true));
		}
	};
?>