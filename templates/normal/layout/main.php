<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

<!--	<base href="--><?php //p(SITE_BASEURL);?><!--">-->

    <link rel="shortcut icon" href="ico/favicon.png">
	<link rel="icon" href="ico/favicon.ico" type="image/x-icon">

<!--    <title>--><?php //p(PRODUCT_NAME); ?><!--</title>-->

    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">

	<link href="css/jquery-ui-1.10.3.css" rel="stylesheet">

    <link href="css/backend-common.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->
  </head>

  <body>
 
<!--	--><?php //include_once(_template("module/navbar.php"))?>

	<?php $this->include_view(); ?>

<!--	--><?php //include_once(_template("module/footer.php"))?>

	<?php $this->include_css(); ?>

	<script src="js/jquery-1.7.2.min.js"></script>
    <script src="js/bootstrap.js"></script>
	<script src="js/jquery-ui-1.10.3.min.js"></script>
	<script src="js/jquery-form/jquery-form.min.js"></script>
	<script src="js/jquery-validate/jquery.validate.min.js"></script>
	<script src="js/jquery-validate/additional-methods.js"></script>
	<script src="js/masked-input/jquery.maskedinput.min.js"></script>
	<script src="js/notification/SmartNotification.js"></script>

	<!-- jquery autocomplete -->
	<script src='js/autocomplete/lib/jquery.ajaxQueue.js'></script>
	<script src='js/autocomplete/lib/thickbox-compressed.js'></script>
	<script src='js/autocomplete/jquery.autocomplete.js'></script>
	<link rel="stylesheet" type="text/css" href="js/autocomplete/jquery.autocomplete.css" />
	<link rel="stylesheet" type="text/css" href="js/autocomplete/lib/thickbox.css" />

	<!-- jquery fancybox -->
	<script type="text/javascript" src="js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

	<?php $this->include_js(); ?>

	<script src="js/utility.js?<?php p(VERSION); ?>"></script>

	<?php $this->include_viewjs(); ?>
  </body>
</html>
