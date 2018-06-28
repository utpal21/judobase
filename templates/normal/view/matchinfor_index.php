<?php if (!$this->script_mode()) { ?>

    <div class="container">

      <form id="form" class="form-signin form-horizontal" action="matchinfor/competition" role="form" method="post">
		<div class="row">
				<fieldset>
				<div class="control-group">
					<div class="controls control-action">
						<a href="matchinfor/competition">Get data before download csv(Competition)</a>
						</br>
						<a href="matchinfor/download">Download csv</a>
					</div>
				</div>
			</fieldset> <!-- /fieldset -->
		</div>
      </form>

    </div> <!-- /container -->
	<div class="main">
		<div class="row">
			<div class="span12">
				<table class="table table-striped">
					<tr>
						<th >Date</th>
						<th>Name</th>
						<th>Country</th>
						<th></th>
					</tr>
					<?php
					foreach($this->competitions as $p) {
						?>
						<tr>
							<td><?php p(date("j M Y",strtotime($p["date_from"]))); ?></td>
							<td><?php p(_str2html($p["name"])); ?></td>
							<td><?php p(_str2html($p["country"])); ?></td>
							<?php if($p["has_results"] > 0) { ?>
							<td style="background-color: #449d44;color: #ffffff"> <a style="color: #ffffff" href="profileContests?id_competition=<?php p($p["id_competition"])?>">View results</a> </td>
							<?php }else{ ?>
								<td style="background-color: #286090;color: #ffffff"><a style="color: #ffffff"  href="profileContests?id_competition=<?php p($p["id_competition"])?>">View inscriptions</a></td>
							<?php } ?>
						</tr>
						<?php
					}
					?>
				</table>
			</div>
		</div>
	</div>


<?php }else {}?>