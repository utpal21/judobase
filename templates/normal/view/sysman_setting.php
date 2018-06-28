<?php if (!$this->script_mode()) { ?>
<header class="jumbotron subhead" id="overview">
	<div class="container">
		<h2>システム設定</h2>
	</div>
</header>

<div class="container">
	<div class="row">
		<div class="span12">
			<section id="main">
				<form id="form" action="sysman/setting_save_ajax" class="form-signin form-horizontal" method="post">
					<div class="row">
						<div class="span6">
							<h3>1. <?php l("データベース設定");?></h3>
							<fieldset class="control-group">
								<label class="control-label" for="db_hostname">MySQL<?php l("サーバーアドレス");?></label>
								<div class="controls"><?php $this->oConfig->input("db_hostname", array("placeholder" => "例: localhost, 192.168.224.55")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group">
								<label class="control-label" for="db_user"><?php l("ユーザーID");?></label>
								<div class="controls"><?php $this->oConfig->input("db_user", array("placeholder" => "データベース作成権限必要")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group">
								<label class="control-label" for="db_password"><?php l("パスワード");?></label>
								<div class="controls"><?php $this->oConfig->password("db_password"); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group">
								<label class="control-label" for="db_name"><?php l("データベース名");?></label>
								<div class="controls"><?php $this->oConfig->input("db_name", array("placeholder" => "例: kirari")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group">
								<label class="control-label" for="db_name"><?php l("ポート");?></label>
								<div class="controls"><?php $this->oConfig->input("db_port", array("class" => "input-mini", "placeholder" => "例: 3306")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group">
								<div class="controls"><button type="button" class="btn btn-testdb btn-mini"><i class="fa fa-warning"></i> <?php l("接続テスト");?></button></div>
							</fieldset> <!-- /fieldset -->
						</div>
						<div class="span6">
							<h3>2. <?php l("メール設定");?></h3>
							<fieldset class="control-group">
								<label class="control-label" for="mail_from"><?php l("送信用メールアドレス");?></label>
								<div class="controls"><?php $this->oConfig->input("mail_from", array("placeholder" => "例: webmaster@kirari.com")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group">
								<label class="control-label" for="mail_fromname"><?php l("ユーザー名");?></label>
								<div class="controls"><?php $this->oConfig->input("mail_fromname", array("class" => "input-large")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group">
								<label class="control-label" for="mail_smtp_auth">SMTP<?php l("認証");?></label>
								<div class="controls"><?php $this->oConfig->checkbox_single("mail_smtp_auth", "SMTP認証使用"); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group" id="group_mail_smtp_use_ssl">
								<label class="control-label" for="mail_smtp_use_ssl">SMTP<?php l("SSL認証使用");?></label>
								<div class="controls"><?php $this->oConfig->checkbox_single("mail_smtp_use_ssl", "SSL認証を利用する"); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group" id="group_mail_smtp_server">
								<label class="control-label" for="mail_smtp_server">SMTP<?php l("サーバーアドレス");?></label>
								<div class="controls"><?php $this->oConfig->input("mail_smtp_server", array("placeholder" => "例: smtp.google.com")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group" id="group_mail_smtp_user">
								<label class="control-label" for="mail_smtp_user">SMTP<?php l("ユーザーID");?></label>
								<div class="controls"><?php $this->oConfig->input("mail_smtp_user", array("placeholder" => "例: yamada")); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group" id="group_mail_smtp_password">
								<label class="control-label" for="mail_smtp_password">SMTP<?php l("パスワード");?></label>
								<div class="controls"><?php $this->oConfig->password("mail_smtp_password"); ?></div>
							</fieldset> <!-- /fieldset -->
							<fieldset class="control-group" id="group_mail_smtp_port">
								<label class="control-label" for="mail_smtp_port">SMTP<?php l("ポート");?></label>
								<div class="controls"><?php $this->oConfig->input("mail_smtp_port", array("class" => "input-mini", "placeholder" => "例: 25")); ?></div>
							</fieldset> <!-- /fieldset -->
						</div>
					</div>
					<div class="text-right">
						<button type="button" class="btn btn-primary" id="btnstart"><i class="fa fa-check"></i> <?php l("保存");?></button>
					</div>
				</form>
			</section>
		</div>
	</div>
</div> <!-- /container -->
<?php } else { ?>
<script type="text/javascript">
disable_alarm = true;

$(function () {

	var $form = $('#form').validate($.extend({
		rules : {
			require_php_ver: {
				required: true
			},
			installed_mysql: {
				required: true
			},
			installed_mbstring: {
				required: true
			},
			installed_simplexml: {
				required: true
			},
			installed_gd: {
				required: true
			},
			db_hostname: {
				required: true
			},
			db_user: {
				required: true
			},
			db_name: {
				required: true
			},
			db_port: {
				required: true,
				digits: true
			},
			mail_from: {
				required: true,
				email: true
			},
			mail_fromname: {
				required: true
			},
			mail_smtp_server: {
				required: true
			},
			mail_smtp_user: {
				required: true
			},
			mail_smtp_password: {
				required: true
			},
			mail_smtp_port: {
				required: true,
				digits: true
			}
		},

		// Messages for form validation
		messages : {
			require_php_ver: {
				required: "<?php l('このシステムPHP ' . MIN_PHP_VER . '以上でだけ動作します。');?>"
			},
			installed_mysql: {
				required: "<?php l('データベースを利用するにはMySQLエクステンションが必要です。');?>"
			},
			installed_mbstring: {
				required: "<?php l('多国語対応のためにmbstringクステンションが必要です。');?>"
			},
			installed_simplexml: {
				required: "<?php l('XMLファイルを読み出すにはSimpleXMLエクステンションが必要です。');?>"
			},
			installed_gd: {
				required: "<?php l('イメージ処理のためにはgdエクステンションが必要です。');?>"
			},
			db_hostname: {
				required: "<?php l('MySQLサーバーアドレスを入力してください。');?>"
			},
			db_user: {
				required: "<?php l('ユーザーIDを入力してください。');?>"
			},
			db_name: {
				required: "<?php l('データベース名を入力してください。');?>"
			},
			db_port: {
				required: "<?php l('ポートを入力してください。');?>",
				digits: "<?php l('数値を入力してください。');?>"
			},
			mail_from: {
				required: "<?php l('送信用メールアドレスを入力してください。');?>",
				email: "<?php l('メールアドレスが有効ではありません。');?>"
			},
			mail_fromname: {
				required: "<?php l('送信用ユーザー名を入力してください。');?>"
			},
			mail_smtp_server: {
				required: "<?php l('SMTPサーバーアドレスを入力してください。');?>"
			},
			mail_smtp_user: {
				required: "<?php l('SMTPユーザーIDを入力してください。');?>"
			},
			mail_smtp_password: {
				required: "<?php l('SMTPパスワードを入力してください。');?>"
			},
			mail_smtp_port: {
				required: "<?php l('SMTPポートを入力してください。');?>",
				digits: "<?php l('数値を入力してください。');?>"
			}
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(ret, statusText, xhr, form) {
			try {
				if (ret.err_code == 0)
				{
					alertBox("<?php l('設定完了');?>", "<?php l('システム設定が完了しました。');?>", function() {
					});
					return;
				}
				else {
					hideMask();
					errorBox("<?php l('設定エラー');?>", "<?php l('すみません。システム設定中にエラーが発生しました。');?>");
					$('#step').val(0);
				}
			}
			finally {
			}
		}
	});

	$('#btnstart').click(function() {		
		if ($('#form').valid())
		{
			var ret = confirm("<?php l('システムを設定しましょうか？');?>");
			if (ret)
			{
				$('#form').submit();
			}
		}
	});

	$('.btn-testdb').click(function() {
		$.ajax({
			url :"sysman/testdb_ajax",
			type : "post",
			dataType : 'json',
			data : { 
				db_hostname : $('#db_hostname').val(), 
				db_user : $('#db_user').val(), 
				db_password : $('#db_password').val(), 
				db_name : $('#db_name').val()
			},
			success : function(data){
				if (data.err_code == 0)
				{
					alertBox("<?php l('接続成功');?>", "<?php l('データベースに接続することができます。');?>");
				}
				else {
					errorBox("<?php l('接続失敗');?>", "<?php l('データベースに接続することができません。データーベース設定を再度確認してください。');?>");
				}
			},
			error : function() {
			},
			complete : function() {
			}
		});
	});
	
});
</script>
<?php } ?>