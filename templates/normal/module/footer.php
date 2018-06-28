
<!-- Footer
================================================== -->
<footer class="footer">
  <div class="container">
	<div class="row">
		<div class="span6 text-left">
			<?php $thisyear = date("Y"); ?>
			<small>© <?php if ($thisyear > 2015) p("2014 - "); p(date("Y")); ?></small> <b>全柔連国内ポイントシステム</b> <span class="label label-important"><?php p(VERSION); ?></span>
		</div> <!-- /span6 -->

		<div id="builtby" class="span6 text-right">
		</div> <!-- /.span6 -->
	</div>
  </div>
</footer>
