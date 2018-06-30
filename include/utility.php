<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2014/09/01
	---------------------------------------------------*/

	define('PRODUCT_NAME',		'全柔連国内ポイントシステム');

	define('SITE_BASE',			preg_replace('/\/index.php/i', '', $_SERVER["SCRIPT_NAME"]) . "/");
	$http_schema = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on") ? "https" : "http");

	if(isset($_SERVER["HTTP_HOST"]))
		define("SITE_ORIGIN",		$http_schema . "://" . $_SERVER["HTTP_HOST"]);
	else
		define("SITE_ORIGIN",		$http_schema . "://");

	define("SITE_BASEURL",		SITE_ORIGIN . SITE_BASE);

	if(preg_match('/\/index.php/i', $_SERVER["SCRIPT_FILENAME"]))
		define('SITE_ROOT',			preg_replace('/\/index.php/i', '', $_SERVER["SCRIPT_FILENAME"]) . "/");
	else
		define('SITE_ROOT',			".");


	@include_once("config.inc");

	define("SITE_MODE",			0); // 0:Standard 1:CreateMockup 2:Mockup
	define("IS_NOMOCKUP",		SITE_MODE == 0);
	define("IS_CREATEMOCKUP",	SITE_MODE == 1);
	define("IS_MOCKUP",			SITE_MODE == 2);

	define('LOG_MODE',			1); // 0:NONE, 1:DEBUG
	define('LOG_PATH',			SITE_ROOT . '/log/');

	define('TMP_URL',			'tmp/');
	define('TMP_PATH',			SITE_ROOT . '/' . TMP_URL);
	define('AVARTAR_URL',		'avartar/');
	define('AVARTAR_PATH',		SITE_ROOT . '/data/' . AVARTAR_URL);
	define('PAVARTAR_URL',		'pavartar/');
	define('PAVARTAR_PATH',		SITE_ROOT . '/data/' . PAVARTAR_URL);
	define('ATTACH_URL',		'attach/');
	define('ATTACH_PATH',		SITE_ROOT . '/data/' . ATTACH_URL);
	define('PLAN_URL',			'data/plan/');
	define('PLAN_PATH',			SITE_ROOT . PLAN_URL);
	define('REPORT_URL',		'data/report/');
	define('REPORT_PATH',		SITE_ROOT . REPORT_URL);
	define('PRESCRIPTION_URL',	'prescription/');
	define('PRESCRIPTION_PATH',	SITE_ROOT . '/data/' . PRESCRIPTION_URL);
	define('MEDINFOPDF_URL', 	'medinfopdf/');
	define('MEDINFOPDF_PATH', 	SITE_ROOT . '/data/' . MEDINFOPDF_URL);
	define('LANG_PATH',			SITE_ROOT . '/lang/');
	define('AVARTAR_SIZE',		240); // 240x240

	define('CAT_ABOUTSITE',		1);
		
	define('PAD_SIZE', 4);

	define("BATCH_INTERVAL", 30);

	// browser flag
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		define('ISIE',				(strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) ? true : false);
		define('ISIE6',				(strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 6.0")) ? true : false);
		define('ISIE7',				(strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 7.0")) ? true : false);
		define('ISIE8',				(strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 8.0")) ? true : false);		
	}

	define("MAIL_HEADER", "全日本柔道連盟です。
いつもお世話になっております。\n");
	define("MAIL_FOOTER", "--「全日本柔道連盟」--");

	if (!defined('DEFAULT_LANGUAGE')) 
		define('DEFAULT_LANGUAGE', 'ja_jp');

	if (!LOG_MODE) {
		error_reporting(0);
		ini_set('display_errors', '0');
	}

	include_once("consts.php");

	include_once("resource/lang/" . _lang() . ".php");

	if (_request("TOKEN") != null) {
		_load_session_from_token(_request("TOKEN"));
	}
	else {
		session_start();
	}
	ob_start("mb_output_handler");
	header("Content-Type: text/html; charset=UTF-8");
	// setting mbstring
	mb_internal_encoding("UTF-8");

	// global error message
	$g_err_msg = "";

	if (_time_zone() != null) 
		date_default_timezone_set(_time_zone());

	include_once("include/controller.php");
	include_once("include/module.php");
	include_once("include/timezones.php");

	//---------------------
	// 1. Auto loadding class
	//---------------------
	function __autoload($class_name)
	{
		if ($class_name == "db")
			include_once("db/db.php");
		if ($class_name == "model")	
			include_once("db/model.php");
		if (preg_match('/Module$/', $class_name)) {
			include_once(HOME_BASE . "module/" . $class_name . ".php");
		}
		else if (preg_match('/Helper$/', $class_name)) {
			include_once("include/helpers/" . $class_name . ".php");
		}
		else if (file_exists("model/" . $class_name . ".php")){
			include_once("model/" . $class_name . ".php");
		}
		else if ($class_name == "PHPMailer") {
			include_once("include/plugins/mail/" . strtolower($class_name) . ".php");
		}
		else if ($class_name == "xml") {
			include_once("include/plugins/xml/" . $class_name . ".php");
		}
		else if ($class_name == "ldap") {
			include_once("include/plugins/ldap/" . $class_name . ".php");
		}
		else if ($class_name == "TCPDF") {
			require_once("include/plugins/tcpdf/" . strtolower($class_name) . ".php");
		}
		else if ($class_name == "faximoClient") {
			include_once("include/plugins/faximo/soap.php");
		}
		else if ($class_name == "PhpImap\Mailbox") {		
			include_once("include/plugins/PhpImap/Mailbox.php");
			include_once("include/plugins/PhpImap/IncomingMail.php");
		}
		else if ($class_name == "APIController") {
			include_once("controller/api/apiController.php");
		}
		else {
			$classPath = explode('_', $class_name);
			if ($classPath[0] == 'Google') {
				if (count($classPath) > 3) {
					// Maximum class file path depth in this project is 3.
					$classPath = array_slice($classPath, 0, 3);
				}
				$filePath = 'include/plugins/googleapi/src/' . implode('/', $classPath) . '.php';
				if (file_exists($filePath)) {
					require_once($filePath);
				}
			}
		}
	}

	//---------------------
	// 2. HTTP related
	//---------------------
	
	// get data of Query
	function _request($name)
	{
		$ret = _post($name);
		if ($ret != null)
			return $ret;

		return _get($name);
	}

	// get POST data
	function _post($txt, $key=null)
	{
		global $_POST;
		
		if ($key == null)
			$ret = isset($_POST[$txt]) ? $_POST[$txt] : null;
		else
			$ret = isset($_POST[$txt][$key]) ? $_POST[$txt][$key] : null;

		if(!isset($ret))
			return $ret;

		$ret = str_replace("\\\\", "\\", $ret);
		$ret = str_replace("\\\"", "\"", $ret);
		$ret = str_replace("\\'", "'", $ret);

		return $ret;
	}
	
	// get GET data
	function _get($name)
	{
		global $_GET;
		
		return isset($_GET[$name]) ? $_GET[$name] : null;
	}
	
	// clear/get/set Session data
	function _session($name=null, $value="@no_val@")
	{
		global $_SESSION;
		if ($name == null && $value == "@no_val@")
		{
			global $_COOKIE;
			if (isset($_COOKIE[session_name()]))
				setcookie(session_name(), '', time()-42000, '/');
			session_destroy();
		}
		else if ($value == "@no_val@") {
			if (!is_array($_SESSION) || !array_key_exists($name, $_SESSION))
				return null;

			return $_SESSION[$name];
		}
		else 
			$_SESSION[$name] = $value;
	}

	// get/set Cookie data
	function _cookie($name=null, $value="@no_val@")
	{
		global $_COOKIE;
		if ($value == "@no_val@") {
			if (!array_key_exists($name, $_COOKIE))
				return null;

			return $_COOKIE[$name];
		}
		else 
			setcookie($name, $value, time() + 3600 * 24 * 30, '/');
	}
	
	function _load_ip_session()
	{
		$session_id = str_replace(".", "a", _ip());

		session_write_close();
		session_id($session_id);
		session_start();
	}

	function _load_session_from_token($token)
	{
		if ($token != null) {
			$tokens = @preg_split("/:/", $token);
			if (count($tokens) == 2) {
				$org_session_id = session_id();
				$user_id = $tokens[0];
				$session_id = $tokens[1];
				@session_write_close();
				session_id($session_id);
				session_start();
				$session = session::getModel(array($session_id, $user_id));
				if ($session == null || $session->user_id != $user_id) {
					session_write_close();
					session_id($org_session_id);
					session_start();
					return false;
				}

				if ($session->user_id == $user_id) {
					if (_user_id() != $user_id) {
						$user = user::getModel($user_id);
						if ($user == null)
							return false;
						user::init_session_data($user);
					}
					else {
						return true;
					}
				}
				return false;
			}
		}

		return false;
	}
	
	// clear/get/set Server data
	function _server($name=null, $value="@no_val@")
	{
		global $_SESSION;

		$old_session_id = session_id();
		session_write_close();

		session_id("SERVER");
		session_start();
		
		$ret = null;
		if ($name == null && $value == "@no_val@")
		{
			$_SESSION = array();
		}
		else if ($value == "@no_val@") {
			if (!array_key_exists($name, $_SESSION))
				$ret = null;
			else
				$ret = $_SESSION[$name];
		}
		else 
			$_SESSION[$name] = $value;
		session_write_close();

		session_id($old_session_id);
		session_start();

		return $ret;
	}
	
	// goto URL
	function _goto($url)
	{
		ob_clean();
		header('Location: ' . $url);
		exit;
	}

	function _nocache()
	{
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");   // always modified
		header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
		header ("Pragma: no-cache");  // HTTP/1.0
	}
	
	// convert relative url to absolute url
	function _url($url)
	{
		return SITE_BASEURL . $url;
	}

	function _page_url($page)
	{
		$utype = _utype();
		switch($page) {
			case PAGE_HOME:
				return "home";
				break;
		}
		return "home";
	}

	function _server_os()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			return 'WIN';
		else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX')
			return 'LINUX';

		return '';
	}

	//---------------------
	// 3. String & Number & Date related
	//---------------------

	function _is_empty($str)
	{
		return $str == null || $str == "";
	}

	function _is_null_empty_string($val)
	{
		if ($val == 0)
			return '';
		else
			return $val;
	}

	// generate sql safe string
	function _sql($txt)
	{
		global $connection;
		if ($txt === null || $txt === "")
			return "NULL";

		if (substr($txt, 0, 2) == "##")
			return substr($txt, 2);

		//$txt = str_replace("'", "''", $txt);
		//$txt = mysql_real_escape_string($txt);
		$txt = mysqli_real_escape_string($connection, $txt);
		return "'" . $txt . "'";
	}

	function _sql_date($d=null)
	{
		if ($d == null)
			$d = time();
		return _sql(date('Y-m-d', $d));
	}

	function _sql_datetime($d=null)
	{
		if ($d == null)
			$d = time();
		return _sql(date('Y-m-d H:i:s', $d));
	}

	function _date($d=null, $format="Y-m-d")
	{
		if ($d == null)
			$d = time();
		return date($format, $d);
	}

	function _datetime($d=null, $format="Y-m-d H:i:s")
	{
		if ($d == null)
			$d = time();
		return date($format, $d);
	}

	function _google_date($d=null)
	{
		if ($d == null)
			$d = time();
		return date("Y-m-d", $d);
	}

	function _google_datetime($d=null)
	{
		if ($d == null)
			$d = time();
		return date("Y-m-d", $d) . "T" . date("H:i:s", $d);
	}

	function _first_weekday($date)
	{
		$time = strtotime($date);

		return date('Y-m-d', strtotime('Last Sunday', $time));
	}

	function _last_weekday($date)
	{
		$time = strtotime($date);

		return date('Y-m-d', strtotime('Next Saturday', $time));
	}

	function _jp_weekday($w)
	{
		switch($w) {
			case 0:
				return "日曜日";
			case 1:
				return "月曜日";
			case 2:
				return "火曜日";
			case 3:
				return "水曜日";
			case 4:
				return "木曜日";
			case 5:
				return "金曜日";
			case 6:
				return "土曜日";
		}

		return "";
	}

	function _time($d=null, $format="H:i")
	{
		if ($d == null)
			$d = time();
		return date($format, $d);
	}

	function _trim_all($s)
	{
		return str_replace(" ", '', $s);
	}

	function _is_valid_number($val)
	{
		if (intval($val) == NULL)
			return false;

		return true;
	}

	function _is_valid_date($val)
	{
		$ret = date_parse($val);

		if ($ret["error_count"] > 0)
			return false;

		return true;
	}

	function _time_zone($time_zone = null)
	{
		if ($time_zone == null) { // read
			$time_zone = _session('TIME_ZONE');
			if ($time_zone != null)
				return $time_zone;
			else {
				if (defined('TIME_ZONE'))
					return TIME_ZONE;
				else
					null;
			}
		}
		else { // write
			_session('TIME_ZONE', $time_zone);
		}
	}
	
	function _str2html($str)
	{
		$str = htmlspecialchars($str);
		$str = preg_replace('/ /i', '&nbsp;', $str);
		return nl2br($str);
	}

	function _str2paragraph($str)
	{
		$str = htmlspecialchars($str);

		$ps = preg_split("/\n/", $str);
		
		$str = "";

		foreach($ps as $p)
		{
			$str .= "<p>" . $p . "</p>";
		}

		return $str;
	}

	function _shift_space($str, $shift=1)
	{
		$ps = preg_split("/\n/", $str);
		
		$str = array();
		
		$space = "";
		for ($i = 0; $i < $shift; $i ++) {
			$space .= "   ";
		}
		foreach($ps as $p)
		{
			$str[] = $space . $p;
		}


		return implode("\n", $str);
	}

	function _str2firstparagraph($str)
	{
		$str = htmlspecialchars($str);

		$ps = preg_split("/\n/", $str);
		
		if (count($ps) > 0) 
			$str = "<p>" . $ps[0] . "</p>";
		else
			$str = "<p></p>";

		return $str;
	}

	function _str2json($str) 
	{
		$str = str_replace("\\", "\\\\", $str);
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "\\n", $str);
		$str = str_replace("\"", "\\\"", $str);
		return $str;
	}

	function _number($v) 
	{
		if ($v == null)
			return "0";
		return number_format($v);
	}

	function _currency($v) 
	{
		if ($v == null)
			return "0.00";
		return number_format($v, 2, '.', ',');
	}

	function _now()
	{
		$db = db::getDB();
		$now = $db->scalar("SELECT NOW()");

		return strtotime($now);
	}

	function _suffix($str, $suffix)
	{
		if (_is_empty($str))
			return "";
		else 
			return $str . $suffix;
	}

	function _fusezi_name($name)
	{
		$len = mb_strlen($name, "UTF-8");
		$new_name = "";
		$first = true;
		for ($i = 0; $i < $len; $i ++)
		{
			$c = mb_substr($name, $i, 1, "UTF-8");
			if ($first == true)
				$c = "○";
			$first = false;

			if ($c == " " || $c == "　")
				$first = true;

			$new_name .= $c;
		}

		return $new_name;
	}

	//---------------------
	// 4. Uploading related
	//---------------------
	function _upload($field, $dest_path)
	{
		global $_FILES;
		if ($_FILES[$field]["error"] != 0)
			return null;

		if (!move_uploaded_file($_FILES[$field]["tmp_name"], $dest_path))
			return null;

		return $_FILES[$field]["name"];
	}

	function _get_uploaded_ext($field)
	{
		global $_FILES;
		if ($_FILES[$field]["error"] != 0)
			return null;
		if ($_FILES[$field]["type"] == "image/png" ||
			$_FILES[$field]["type"] == "image/x-png")
			return "png";
		if ($_FILES[$field]["type"] == "image/jpeg" ||
			$_FILES[$field]["type"] == "image/pjpeg")
			return "jpg";
		if ($_FILES[$field]["type"] == "image/gif")
			return "gif";
		if ($_FILES[$field]["type"] == "application/pdf")
			return "pdf";
		
		return null;
	}

	//---------------------
	// 5. Image Processing
	//---------------------
	function _resize_image($path, $source_ext, $w, $h=null){
		$path_parts = pathinfo($path);
		$ext = strtolower($path_parts['extension']);
		if ($ext == "")
			$ext = $source_ext;

		if ($source_ext == "png")
			$src_img = imagecreatefrompng($path); 
		else if ($source_ext == "jpg")
			$src_img = imagecreatefromjpeg($path); 

		$ow = imagesx($src_img);
		$oh = imagesy($src_img);

		if ($h == null)
			$h = intval($oh * $w / $ow);

		$dst_img = imagecreatetruecolor($w, $h); 
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $w, $h, $ow, $oh); 

		if ($ext == "png")
			imagepng($dst_img, $path); 
		else if ($ext == "jpg")
			imagejpeg($dst_img, $path); 

		imagedestroy($src_img);
		imagedestroy($dst_img);
	}

	function _resize_photo($path, $source_ext, $maxw, $maxh){
		$path_parts = pathinfo($path);
		$ext = strtolower($path_parts['extension']);
		if ($ext == "")
			$ext = $source_ext;

		if ($source_ext == "png")
			$src_img = imagecreatefrompng($path); 
		else if ($source_ext == "jpg")
			$src_img = imagecreatefromjpeg($path); 

		$ow = imagesx($src_img);
		$oh = imagesy($src_img);

		if ($ow < $maxw && $oh < $maxh)
			return;
		
		$w = $maxw;
		$h = intval($oh * $maxw / $ow);
		if ($h > $maxh) {
			$h = $maxh;
			$w = intval($ow * $maxh / $oh);
		}

		$dst_img = imagecreatetruecolor($w, $h); 
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $w, $h, $ow, $oh); 
		if ($ext == "png")
			imagepng($dst_img, $path); 
		else if ($ext == "jpg")
			imagejpeg($dst_img, $path); 

		imagedestroy($src_img);
		imagedestroy($dst_img);
	}

	function _resize_thumb($path, $source_ext, $maxw=PHOTO_MAX_WIDTH, $maxh=PHOTO_MAX_HEIGHT){
		if ($source_ext == "png")
			$src_img = imagecreatefrompng($path); 
		else if ($source_ext == "jpg")
			$src_img = imagecreatefromjpeg($path); 

		$ow = imagesx($src_img);
		$oh = imagesy($src_img);
		
		$w = $maxw;
		$h = intval($oh * $maxw / $ow);
		if ($h > $maxh) {
			$h = $maxh;
			$w = intval($ow * $maxh / $oh);
		}

		$dst_img = imagecreatetruecolor($maxw, $maxh); 
		imagealphablending($dst_img, true);
		$back = imagecolorallocatealpha($dst_img, 255, 255, 255, 0);
		imagefilledrectangle($dst_img, 0, 0, $maxw - 1, $maxh - 1, $back);
		imagecopyresampled($dst_img, $src_img, ($maxw - $w) / 2, ($maxh - $h) / 2, 0, 0, $w, $h, $ow, $oh); 
		imagesavealpha($dst_img, true);
		imagepng($dst_img, $path); 

		imagedestroy($src_img);
		imagedestroy($dst_img);
	}

	function _resize_userphoto($path, $source_ext, $width, $height){
		$path_parts = pathinfo($path);
		$ext = strtolower($path_parts['extension']);
		if ($ext == "")
			$ext = $source_ext;

		if ($source_ext == "png")
			$src_img = imagecreatefrompng($path); 
		else if ($source_ext == "jpg")
			$src_img = imagecreatefromjpeg($path); 

		$ow = imagesx($src_img);
		$oh = imagesy($src_img);
		
		$w = $width;
		$h = intval($oh * $width / $ow);
		if ($h < $height) {
			$h = $height;
			$w = intval($ow * $height / $oh);
		}
		$x = - ($w - $width) / 2;
		$y = - ($h - $height) / 2;

		$dst_img = imagecreatetruecolor($width, $height); 
		imagecopyresampled($dst_img, $src_img, $x, $y, 0, 0, $w, $h, $ow, $oh); 
		
		if ($ext == "png")
			imagepng($dst_img, $path); 
		else if ($ext == "jpg")
			imagejpeg($dst_img, $path);  

		imagedestroy($src_img);
		imagedestroy($dst_img);
	}

	//---------------------
	// 6. CSV related
	//---------------------
//	s
	function _downheader($filename)
	{
		if (ISIE || ISEDGE) {
			header('Content-Disposition: attachment; filename=' . urlencode($filename)); //プロファイル
		}
		else if (ISSafari) {
			header('Content-Disposition: attachment; filename="' . $filename . '"'); //プロファイル
		}
		else {
			header('Content-Disposition: attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . urlencode($filename)); //プロファイル
		}
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: private, must-revalidate");  // HTTP/1.1
		header("Expires: 0");
	}

	function _csvheader($filename)
	{
		header("ContentType: application/text-csv; charset=UTF-8");
		_downheader($filename);
	}

	function _csvrow($arr)
	{
		for ($i = 0; $i < count($arr); $i ++) {
			if ($arr[$i]) {
				$arr[$i] = str_replace('"', '""', $arr[$i]);
				$arr[$i] = '"' . $arr[$i] . '"';
			}
		}
		$txt = implode(",", $arr);
		$txt .= "\r\n";
		$txt = mb_convert_encoding($txt, 'SJIS-win', 'UTF-8');
		print $txt;
	}

	//---------------------
	// 7. Log related
	//---------------------
	function _log($log_type, $msg)
	{
		jlog::write($log_type, $msg);
	}

	function _access_log($msg, $url = "")
	{
		if ($url != "")
			$url = " url:" . $url;
		_log(LOGTYPE_ACCESS, $msg . $url);
	}

	function _opr_log($msg)
	{
		_log(LOGTYPE_OPERATION, $msg);
	}

	function _warn_log($msg)
	{
		_log(LOGTYPE_WARNING, $msg);
	}

	function _err_log($msg)
	{
		_log(LOGTYPE_ERROR, $msg);
	}

	function _debug_log($msg)
	{
		_log(LOGTYPE_DEBUG, $msg);
	}

	//---------------------
	// 8. File related
	//---------------------
	function _fwrite($path, $str)
	{
		$fp = @fopen($path,"wb");
		if ($fp != null) {
			@fputs($fp, $str);
			@fclose($fp);
		}
	}

	function _fread($path)
	{
		$fp = @fopen($path,"rb");
		if ($fp != null) {
			$str = '';
			while (!feof($fp)) {
			  $str .= fread($fp, 8192);
			}
			@fclose($fp);
		}
		return $str;
	}

	/**
	 * @param $file
	 * @return array
	 * @authr <Utpal Biswas utpal.uoda@gmail.com>
	 */
	function _csvFileRead($path)
	{
		$file_handle = fopen($path, "r");
		$data = array();
		if($file_handle != null) {
			while (!feof($file_handle) ) {
				$data[] = fgetcsv($file_handle, 1024);
			}
			fclose($file_handle);
		}
		return $data;
	}

	function _basename($file) 
	{ 
	    return end(explode('/',$file)); 
	} 
	//---------------------
	// 9. User Session Related
	//---------------------
	function _utype($utype = null) {
		if ($utype == null)
			return _session("utype");
		else
			_session("utype", $utype);
	}

	function _user_id($user_id = null) {
		if ($user_id == null)
			return _session("user_id");
		else
			_session("user_id", $user_id);
	}

	function _user_name($user_name = null) {
		if ($user_name == null)
			return _session("user_name");
		else
			_session("user_name", $user_name);
	}

	function _shop_id($shop_id = null) {
		if ($shop_id == null)
			return _session("shop_id");
		else
			_session("shop_id", $shop_id);	
	}

	$_cur_user = null;
	function _user() {
		global $_cur_user;
		if ($_cur_user == null) {
			$utype = _utype();
			switch ($utype) {
				case UTYPE_MI_CLINIC:
					$user = doctor::getModel(_user_id());
					$user->user_id = $user->doctor_id;
					$user->user_name = $user->doctor_name;
					$user->user_type = $utype;
					break;

				case UTYPE_MI_CAREOFFICE:
					$user = careman::getModel(_user_id());
					$user->user_id = $user->careman_id;
					$user->user_name = $user->careman_name;
					$user->user_type = $utype;
					break;

				case UTYPE_MI_FACILITY:
					$user = facility::getModel(_user_id());
					$user->user_id = $user->facility_id;
					$user->user_name = $user->facility_name;
					$user->user_type = $utype;
					break;
				
				default:
					$user = user::getModel(_user_id());
					break;
			}

			if ($user != null) {
				$_cur_user = $user;
			}
		}
		return $_cur_user;
	}

	function _login_ip($login_ip = null) {
		if ($login_ip == null)
			return _session("login_ip");
		else
			_session("login_ip", $login_ip);
	}

	function _first_logined($first = null) {
		if ($first == null)
			return _session("first_logined") == 1;
		else
			_session("first_logined", $first);
	}

	function _auto_login_token($token = null) {
		if ($token == null)
			return _cookie("hc_token");
		else
			_cookie("hc_token", $token);
	}
	
	function _editor_type($editor_type = null) {
		if ($editor_type == null)
			return _session("editor_type");
		else
			_session("editor_type", $editor_type);
	}

	//---------------------
	// 10. File Path Related
	//---------------------
	function _tmp_path($ext = null)
	{
		$tmppath = "";
		$seed = time();
		while(1) {
			$tmpfile = "a" . substr(md5($seed), 0, 10);
			if ($ext != null)
				$tmpfile .= "." . $ext;
			$tmppath = TMP_PATH . $tmpfile;
			
			if (!file_exists($tmppath))
				break;
			
			$seed += 12345;
		}
		
		return $tmppath;
	}

	function _avartar_url($id)
	{
		if ($id == null)
			$id = "all";
		return AVARTAR_URL . $id . ".jpg";
	}

	function _avartar_cache_id()
	{
		$cache_id = _session("AVARTAR_CACHE_ID");
		if ($cache_id == null) {
			return session_id();
		}
		else {
			return $cache_id;
		}
	}

	function _renew_avartar_cache_id()
	{
		_session("AVARTAR_CACHE_ID", _newId());
	}

	//---------------------
	// 11. Template Related
	//---------------------
	function _set_template($t)
	{
		if (HOME_BASE == "backend/")
			_session("backend_template", "backend/templates/" . $t . "/");
		else
			_session("template", "templates/" . $t . "/");
	}

	function _template($path)
	{
		if (HOME_BASE == "backend/")
			$template = _session("backend_template");
		else
			$template = _session("template");
		if ($template == null)
			$template = HOME_BASE . "templates/normal/";
		return $template . $path;
	}

	//---------------------
	// 12. View Output Related
	//---------------------
	function p($d)
	{
		print $d;
	}

	function _nodata_message($data)
	{
		if (count($data) == 0) {
			?>
			<div class="alert alert-block">
				<?php p(_err_msg(ERR_NODATA));?>
			</div>
			<?php
		}
	}

	function _code_label($code, $val) 
	{
		global $g_codes;
		if (isset($g_codes)) {
			$codes = $g_codes[$code];
			return $codes[$val];
		}
		else {
			return null;
		}
	}

	function _err_msg($err, $param1=null, $param2=null)
	{
		global $g_err_msg, $g_err_msgs;
		$err_msg = "";
		if ($g_err_msg != "")
			$err_msg = $g_err_msg;
		else
			$err_msg = $g_err_msgs[$err];

		$err_msg = sprintf($err_msg, $param1, $param2);
		return $err_msg;
	}

	//---------------------
	// 13. Mail & SMS Related
	//---------------------

	// send email
	function _send_mail($from, $to_address, $to_name, $title, $body, $file_path=null, $file_name=null)
	{
		if (MAIL_ENABLE == ENABLED) {
			$mailer = new PHPMailer();
			if ($from == null || _is_empty($from->mail_from) || $from->mail_smtp_auth != 1) {
				$mailer->From = MAIL_FROM;
				$mailer->FromName = MAIL_FROMNAME;
				$mailer->SMTPAuth = MAIL_SMTP_AUTH;
				if (MAIL_SMTP_USE_SSL && $mailer->SMTPAuth) {
					$mailer->SMTPSecure = "ssl";
				}
				$mailer->Host 	= MAIL_SMTP_SERVER;
				$mailer->Username = MAIL_SMTP_USER;
				$mailer->Password = MAIL_SMTP_PASSWORD;
				$mailer->Port     = MAIL_SMTP_PORT;
			}
			else {
				$mailer->From = $from->mail_from;
				$mailer->FromName = $from->mail_fromname;
				$mailer->SMTPAuth = ($from->mail_smtp_auth == 1);
				if (($from->mail_smtp_use_ssl == 1) && $mailer->SMTPAuth) {
					$mailer->SMTPSecure = "ssl";
				}
				$mailer->Host 	= $from->mail_smtp_server;
				$mailer->Username = $from->mail_smtp_user;
				$mailer->Password = $from->mail_smtp_password;
				$mailer->Port     = $from->mail_smtp_port;
			}

			$mailer->IsSMTP();	
			$mailer->Subject = $title;
			$mailer->Body = $body;

			if (_is_empty($to_address) && _is_empty($to_name)) {
				// 確認用メール
				if ($from != null && !_is_empty($from->mail_from))
					$mailer->AddAddress($from->mail_from, $from->mail_fromname);
				else
					$mailer->AddAddress(MAIL_FROM, MAIL_FROMNAME);
			}
			else {
				$mailer->AddAddress($to_address, $to_name);

				if ($from != null && !_is_empty($from->mail_from))
					$mailer->AddBCC($from->mail_from, $from->mail_fromname);
				else
					$mailer->AddBCC(MAIL_FROM, MAIL_FROMNAME);
			}

			if ($file_path != null) {
				$mailer->AddAttachment($file_path, $file_name);
			}
			
			$ret = $mailer->Send();

			_opr_log("sended mail from: " . $mailer->From . " to: " . $to_address . " result:" . $ret);

			return $ret;
		}
		return false;
	}

	// send efax
	function _send_efax($from, $to_fax, $to_name, $fax_header, $file_path, $file_name)
	{
		$to_address = "81" . preg_replace('/-/', "", $to_fax) . "@efaxsend.com";

		return _send_mail($from, $to_address, $to_name, "", $fax_header, $file_path, $file_name);
	}

	function _faximo()
	{
		global $faximo;
		if (!isset($faximo))
			$faximo = new faximoClient;
		return $faximo;
	}

	function _send_faximo($to_fax, $file_path, $file_name)
	{
		$to_fax = preg_replace('/-/', "", $to_fax);
		$faximo = _faximo();

		$faximo->send_fax($to_fax, $file_path, $file_name);
	}

	//---------------------
	// 13. Mail & SMS Related
	//---------------------
	// set/get current language
	function _lang($lang = null) {
		if ($lang == null)
		{
			$lang = _session("LANGUAGE");
			return $lang == null ? DEFAULT_LANGUAGE : $lang;
		}
		else 
			_session("LANGUAGE", $lang);
	}

	function _l($str) {
		global $g_string;
		$lstr = isset($g_string[$str]) ? $g_string[$str] : null;
		return $lstr == null ? $str : $lstr;
	}

	function l($str) {
		print _l($str);
	}

	//---------------------
	// 14. Batch service related
	//---------------------
	function _install_batch() {
		if (_server_os() === 'WIN') {
		}
		else if (_server_os() === 'LINUX') {
			$path = SITE_ROOT . "resource/service/batch.sh";

			_fwrite($path, "cd " . SITE_ROOT . "\nphp " . SITE_ROOT . "scrap.php > " . SITE_ROOT . "log/batch.log");

			system('chmod -R 777');
		}
	}

	function _uninstall_batch() {
		if (_server_os() === 'WIN') {
			$exe_path = SITE_ROOT . "resource/service/batch.exe";

			exec($exe_path . " -k");
			exec($exe_path . " -u");
		}
		else if (_server_os() === 'LINUX') {
			$uninstall_batch = SITE_ROOT . "resource/service/uninstall_batch.sh";

			exec($uninstall_batch);
		}
	}

	function _run_batch() {
		if (_server_os() === 'WIN') {
			$exe_path = SITE_ROOT . "resource/service/batch.exe";

			echo exec($exe_path . " -r");
		}
	}

	//---------------------
	// 15. Other
	//---------------------
	function _newId($seed=null, $len=15) {
		return substr(strtoupper(md5($seed == null ? microtime() . rand() : microtime() . $seed . rand())), 0, $len);
	}

	function _erase_old($dir) {
		$files = scandir($dir);
		if (count($files) == 0)
			return;

		$now = time();
		foreach ($files as $file)
		{
			$tm = filectime($dir . $file);
			if ($now - $tm > 3600) // before 1 hour
				@unlink($dir . $file);
		}
	}

	function _ip()
	{
		global $_SERVER;
		return $_SERVER["REMOTE_ADDR"];
	}

	function _in_blacklist()
	{
		$ip = _ip();
		if ($ip == "::1")
			return false;

		$ip = preg_split("/\./", $ip);
		if (count($ip) != 4)
			return false;


		if (defined("BLACKLIST") && BLACKLIST != "")
		{
			$ll = @preg_split("/;/", BLACKLIST);
			foreach ($ll as $l)
			{
				$bl = preg_split("/,/", $l);
				$addr = preg_split("/\./", $bl[1]);
				$mask = preg_split("/\./", $bl[2]);

				$check = true;
				for($i = 0; $i < 4; $i ++)
				{
					$ii = ($ip[$i] + 0) & ($mask[$i] + 0);
					if ($ii != $addr[$i])
						$check = false;
				}
				if ($check)
					return true;
			}
		}

		return false;
	}

	function _ifnull($val, $default)
	{
		return $val == null ? $default : $val;
	}

	function _path2id($path)
	{
		$ps = preg_split("/\//", $path);
		if ($ps == null)
			return $path;

		return $ps[count($ps) - 1] + 0;
	}

	function _path2parent_ids($path)
	{
		$ps = preg_split("/\//", $path);
		if ($ps == null || count($ps) == 1)
			return array();

		$parent_ids = array();
		for ($i = 0; $i < count($ps) - 1; $i ++)
		{
			array_push($parent_ids, $ps[$i] + 0);
		}
		return $parent_ids;
	}

	function _rating($sum, $count)
	{
		if ($count > 0)
			return floor($sum / $count);
		else
			return 0;
	}
	
	// for sorting tree
	function _next_sort($sort)
	{
		$sorts = preg_split("/\//", $sort);

		if ($sorts != null) 
		{
			$last = count($sorts) - 1;
			$sorts[$last] = str_pad($sorts[$last] + 1, PAD_SIZE, "0", STR_PAD_LEFT);
			return join("/", $sorts);
		}
		else
			return null;
	}

	function _first_sort($sort)
	{
		$sorts = preg_split("/\//", $sort);

		if ($sorts != null) 
		{
			$last = count($sorts) - 1;
			$sorts[$last] = str_pad(0, PAD_SIZE, "0", STR_PAD_LEFT);
			return join("/", $sorts);
		}
		else
			return null;
	}

	// class utility
	function get_public_methods($className) {
		/* Init the return array */
		$returnArray = array();

		/* Iterate through each method in the class */
		foreach (get_class_methods($className) as $method) {

			/* Get a reflection object for the class method */
			$reflect = new ReflectionMethod($className, $method);

			/* For private, use isPrivate().  For protected, use isProtected() */
			/* See the Reflection API documentation for more definitions */
			if($reflect->isPublic()) {
				/* The method is one we're looking for, push it onto the return array */
				array_push($returnArray,$method);
			}
		}
		/* return the array to the caller */
		return $returnArray;
	}

	function get_this_class_methods($class){
		$array1 = get_public_methods($class);
		if($parent_class = get_parent_class($class)){
			$array2 = get_public_methods($parent_class);
			$array3 = array_diff($array1, $array2);
		}else{
			$array3 = $array1;
		}
		return($array3);
	}


	// pcntl related
	function _fork()
	{
		if (function_exists("pcntl_fork")) {
			$pid = pcntl_fork();
		}
		else {
			$pid = -1;
		}

		return $pid;
	}

	// pdf related
	function _pdf2jpg($pdf, $folder)
	{
		$convert = '"' . IMAGICK_HOME . '/convert"';
		$cmd = $convert . ' "' . $pdf . '" -density 150 -geometry 1000x800 -quality 100 -sharpen 0x1.0 -background white "' . $folder . '%d.jpg"';
		exec($cmd);
	}

	function _prescription_pdf2jpg($pdf, $prescription_id)
	{
		$url = date('Y/m/') . $prescription_id . "/";

		$folder = PRESCRIPTION_PATH . $url;

		if (!file_exists($folder)) {
			mkdir($folder, 0777, true);
		}
		_pdf2jpg($pdf, $folder);

		$files = scandir($folder);
		$pages = count($files) - 2;

		if ($pages > 0) {
			return array("url" => PRESCRIPTION_URL . $url, "pages" => $pages);
		}
		else 
			return null;
	}

	function _prescription_full_url($url, $renew=false)
	{
		//if ($renew)
		//	_renew_avartar_cache_id();

		return SITE_BASEURL . $url;
	}

	function _medinfo_pdf_full_url($url)
	{
		return SITE_BASEURL . $url;		
	}

	
	# 明治元年(M) 1868-1-25 ~ 1912-7-29
	# 大正元年(T) 1912-7-30 ~ 1926-12-24
	# 昭和元年(S) 1926-12-25 ~ 1989-1-7
	# 平成元年(H) 1989-1-8 ~
	function date_wareki($date)
	{
		if (!isset($date) || strlen($date) < 10)
			return "";
		$def_old_date = substr($date, 0, 10);
		$y = substr($date, 0, 4);
		$m = substr($date, 5, 2);
		$d = substr($date, 8, 2);
		$m_date = "1868-01-25";
		$t_date = "1912-07-30";
		$s_date = "1926-11-25";
		$h_date = "1989-01-08";
		$def_m_date = "明治" . ($y - 1868 + 1) . "年" . onedate($m) . "月" . onedate($d) . "日";
		$def_t_date = "大正" . ($y - 1912 + 1) . "年" . onedate($m) . "月" . onedate($d) . "日";
		$def_s_date = "昭和" . ($y - 1926 + 1) . "年" . onedate($m) . "月" . onedate($d) . "日";
		$def_h_date = "平成" . ($y - 1989 + 1) . "年" . onedate($m) . "月" . onedate($d) . "日";
		if ($date < $m_date)
			return $def_old_date;
		if ($date < $t_date)
			return $def_m_date;
		else if ($date < $s_date)
			return $def_t_date;
		else if ($date < $h_date)
			return $def_s_date;
		return $def_h_date;
	}

	function onedate($date)
	{
        	if (!isset($date) || strlen($date) < 2)
			return "";
		$one = substr($date, 0, 1);
		if ($one == 0)
           		return substr($date, 1, 1);
		return $date;
	}

	// json related
	function _json_encode($a=false)
	{
	    if (is_null($a)) return 'null';
	    if ($a === false) return 'false';
	    if ($a === true) return 'true';
	    if (is_scalar($a))
	    {
			if (is_float($a))
			{
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if (is_numeric($a))
			{
				$b = $a + 0;
				if (($a . "") === ($b . ""))
					return $a;
			}

			if (is_string($a))
			{
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else
				return $a;
	    }

	    $isList = true;
	    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
	    {
	    	if (key($a) !== $i)
	    	{
	        	$isList = false;
	        	break;
	      	}
	    }

	    $result = array();
	    if ($isList)
	    {
	    	foreach ($a as $v) $result[] = _json_encode($v);
			return '[' . join(',', $result) . ']';
	    }
	    else
	    {
	      	foreach ($a as $k => $v) $result[] = _json_encode($k).':'._json_encode($v);
	      	return '{' . join(',', $result) . '}';
	    }
	}

	function _level_name($level)
	{
		switch ($level) {
			case 0:
				return 'A';
			case 1:
				return 'B';
			case 2:
				return 'C';
			case 3:
				return 'D';
			default:
				return '';
		}
	}
	 function _make_params_string($params)
	{
		$retStr = "?";

		foreach ($params as $key => $value)
		{
			$retStr .= "params[";
			$retStr .= $key;
			$retStr .= "]=";
			$retStr .= $value;
			$retStr .= "&";
		}

		$retStr = rtrim($retStr, "&");

		return $retStr;
	}

	function _get_content($path, $params)
	{
		$url = "https://data.ijf.org/api/get_json";
		$url .= $path;

		$strParam = _make_params_string($params);
		$url .= $strParam;

		$data = file_get_contents($url);

		return $data;
	}

	function _minutes2str($minutes)
	{
		if ($minutes === null || $minutes === '')
			return "";

		if ($minutes == -1)
			return "ラスト";

		$hour = floor($minutes / 60);
		$minute = $minutes % 60;
		if($hour<10){
			$hour = "0".$hour;
		}

		return $hour . ":" . str_pad($minute, 2, "0", STR_PAD_LEFT);
	}
?>