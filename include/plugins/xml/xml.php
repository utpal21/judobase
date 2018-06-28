<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

	class xml {
		private $xml;

		public function __construct($datafile){
			$path = SITE_ROOT . "/" . $datafile;
			$fp = @fopen($path, "r");
			if ($fp != null) {
				$xmlstr = @fread($fp, filesize($path));
				@fclose($fp);

				$this->xml = new SimpleXMLElement($xmlstr);
			}
		}

		public function __get($prop) {
			return $this->xml->$prop;
		}

	};

?>