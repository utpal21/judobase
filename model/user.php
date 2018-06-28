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
			parent::__construct("m_user",
				"user_id",
				array(
					"user_type",
					"user_name",
					"avartar",
					"email",
					"password",				
					"gender_assign",
					"activate_key",
					"activate_until",
					"access_time"),

				array("auto_inc" => true));
		}

		public static function getModel($pkvals, $ignore_del_flag=false)
		{
			$model = new static;
			$err = $model->get($pkvals, $ignore_del_flag);
			if ($err == ERR_OK) 
				return $model;

			if (is_string($pkvals)) {
				$err = $model->select("email = " . _sql($pkvals));
				if ($err == ERR_OK)
					return $model;
			}

			return null;
		}

		static public function set_access_time($user_id)
		{
			$db = db::getDB();
			$db->execute("UPDATE m_user SET access_time=NOW() WHERE user_id=" . _sql($user_id));
		}

		public function update_avartar($uploaded_photo)
		{
			if ($uploaded_photo != "") {
				$photo = $this->user_id . ".jpg";
				if (substr($uploaded_photo, 0, 3) == "tmp") {
					@unlink(AVARTAR_PATH . $photo);
					@rename(SITE_ROOT . "/" . $uploaded_photo, AVARTAR_PATH . $photo);
				}
			}

			$this->avartar = _newId();
			return $this->save();
		}

		public function check_delete()
		{
			return true;
		}

		public function login($auto_login = false)
		{
			global $_SERVER;
			$logined = ERR_FAILLOGIN;

			if ($this->email != "") {
				$user = new user;
				$err = $user->select("email=" . _sql($this->email));			
				if ($err == ERR_OK && $user->password == md5($this->password))
					$logined = ERR_OK;
			}
			else if ($auto_login) {
				// auto login
				$token = _auto_login_token();
				$s = preg_split("/\//", $token);
				if (count($s) == 2) {
					$user = new user;
					$err = $user->select("email=" . _sql($s[0]));
					if ($err == ERR_OK && $token == $user->auto_login_token())
						$logined = ERR_OK;
				}				
			}

			if ($logined == ERR_OK)
			{
				if ($auto_login) {
					_auto_login_token($user->auto_login_token());
				}
				else {
					_auto_login_token("NOAUTO");
				}

				user::init_session_data($user);

				$this->load($user);

				_access_log("ログイン");
			}

			return $logined;
		}

		public static function init_session_data($user)
		{
			global $_SERVER;
			$user->access_time = "##NOW()";		
			$user->save();

			_utype($user->user_type);
			_user_id($user->user_id);
			_user_name($user->user_name);
			_lang($user->language);
			_login_ip($_SERVER["REMOTE_ADDR"]);

			session::insert_session();
		}

		public function auto_login_token() 
		{
			return $this->email . "/" . md5($this->email . _ip() . $this->password);
		}

		public function logout()
		{
			_access_log("ログアウト");
			_session();
			_auto_login_token("NOAUTO");
		}

		public static function get_user_name($user_id)
		{
			if ($user_id == null)
				return "";

			$user = user::getModel($user_id);
			if ($user == null)
				return "";
			else
				return $user->user_name;
		}

		static public function is_exist_by_email($email, $user_id=null)
		{
			$user = new user;
			$where = "email=" . _sql($email);
			if (!_is_empty($user_id))
			{
				$where .= " AND user_id!=" . _sql($user_id);
			}
			$err = $user->select($where);
			return $err == ERR_OK;
		}

		public function avartar_url()
		{
			$id = $this->user_id;
			if ($this->user_id == null)
				$id = "all";

			return SITE_BASEURL . AVARTAR_URL . $id . ".jpg?" . $this->avartar;
		}
	};
?>