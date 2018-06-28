<?php
	/*---------------------------------------------------
		Project Name:		Kirari
		Developement:		
		Author:				WuxingQuan
		Date:				2014/11/01
	---------------------------------------------------*/

	class HomeController extends controller {
		public function __construct(){
//			parent::__construct();

			$this->_navi_menu = "home";
		}

		public function checkPriv($action, $utype)
		{
			//parent::checkPriv($action, UTYPE_ADMIN);
		}

		public function index() {
			$this->forward("matchinfor");
		}
	}
?>