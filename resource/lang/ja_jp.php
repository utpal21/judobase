<?php
	/*---------------------------------------------------
		Project Name:		Kirari Report System
		Developement:		
		Author:				Ken
		Date:				2015/02/10
	---------------------------------------------------*/

$g_err_msgs = array(
	ERR_OK => "成功",																		
	ERR_SQL => "データベースエラーが発生しました。",
	ERR_INVALID_PKEY => "プライマリーキーが有効ではない。",
	ERR_NODATA => "データがありません。",		
													 
	ERR_FAILLOGIN => "ログインIDやパスワードが正確ではありません。",
	ERR_ALREADYLOGIN => "このユーザーは既にログインしています。",
													 
	ERR_INVALID_REQUIRED => "",
													 
	ERR_NOPRIV => "アクセス権限がありません。",
	ERR_NOT_LOGINED => "ログインしてください。",

	ERR_FAIL_UPLOAD => "アップロード失敗",

	ERR_INVALID_IMAGE => "イメージファイルではありません。",
	ERR_INVALID_PDF => "PDFファイルではありません。",

	ERR_USER_LOCKED => "このアカウントはロックされました。管理者に連係してください。",
	ERR_USER_UNACTIVATED => "このユーザーはまだアクティブされていません。確認メールから確認リンクをクリックしてください。",
	ERR_DUPLICATE_LOGINID => "このログインIDはすでに利用しています。",
	
	ERR_ALREADYINSTALLED => "システムはすでにインストールされています。",

	ERR_NOTFOUND_PAGE => "当該ページは存在しません。",

	ERR_INVALID_OLDPWD => "現在のパスワードが正しくありません。",

	ERR_ALREADY_USING_USER_NAME => "このユーザー名前は既に利用しています。",
	ERR_ALREADY_USING_EMAIL => "このメールアドレスは既に利用しています。",

	ERR_DELUSER => "このユーザーは関連データーが存在するので、削除できません。",

	ERR_CANTCONNECT => "サーバーにアクセスできません。",

	ERR_NEWINSERTED => "プロファイル情報を入力してください。",

	ERR_INVALID_ACTIVATE_KEY => "メール認証キーが有効ではありません。",
	ERR_ACTIVATE_EXPIRED => "このメール認証キーはすでに有効期限が過ぎました。",
	ERR_INVALID_EMAIL => "メールアドレスが正しくありません。",

	ERR_NOTFOUND_USER => "ユーザーが存在しません。",

	ERR_FAILOPENFILE => "ファイルへの書き出し権限がありません。",

	ERR_NOTSETTED_TO_EMAIL => "送信先メールが設定されていません。",
	ERR_NOTSETTED_TO_FAX => "送信先FAXが設定されていません。",
	ERR_FAIL_SEND_EMAIL => "メール送信が失敗しました。",
	ERR_NOTFOUND_TOURNAMENT => "大会が存在しません。",
	ERR_ALEADY_EXIST_SAME_RESULT => "同じ競技戦績が存在します。"	
);

$g_codes = array(
	CODE_UTYPE => array(
		UTYPE_ADMIN => "管理者",
		UTYPE_REVIEW => "レビュー担当者",
		UTYPE_COARCH => "コーチ"
	),
	CODE_GENDER => array(
		GENDER_MAN => "男性",
		GENDER_WOMAN => "女性"
	),
	CODE_PAGE => array(
		PAGE_HOME => "ホーム"
	),
	CODE_LANG => array(
		LANG_JA_JP => "日本語"
	),
	CODE_LOCK => array(
		UNLOCKED => "解除",
		LOCKED => "ロック"
	),
	CODE_ENABLE => array(
		ENABLED => "可能",
		DISABLED => "不可"
	)
);

$g_string = array(
	"ホーム" => "ホーム"
);
?>