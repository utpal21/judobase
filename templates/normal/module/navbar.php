<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="nav-collapse collapse">
			<div class="brand"><?php p(PRODUCT_NAME); ?></div>
			<h1>全柔連国内ポイントシステム</h1>
			<ul class="nav">
				<?php if (_utype() == UTYPE_ADMIN) { ?>
				<li class="dropdown <?php $this->setActive("sysman"); ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear"></i> システム管理 <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="sysman/setting">システム設定</a></li>
						<li><a href="sysman/version_history">パッチ履歴</a></li>
					</ul>
				</li>
				<?php } ?>
			</ul>
			<ul class="nav pull-right"">
				<li>
					<a href="myinfo"><img class="mini-avartar" src="<?php p(_avartar_url(_user_id())); ?>"/> <?php p(_user_name()); ?></a>
				</li>
				<li>
					<a href="login/logout"><i class="fa fa-sign-out"></i> ログアウト</a>
				</li>
			</ul>
			</div>
		</div>
	</div>
</div>