<?php if (!$this->script_mode()) { ?>
	<div class="container">

		<form id="form" class="form-signin form-horizontal" action="collectinfor/competition" role="form" method="post">
			<div class="row">
				<fieldset>
					<div class="control-group">
						<div class="controls control-action">
							<a href="profileContests/getData?id_competition=<?php echo $_GET['id_competition']; ?>">Get data before download csv(Competition)</a>
							</br>
							<a href="profileContests/download?id_competition=<?php echo $_GET['id_competition']; ?>">Download csv</a>
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
						<th >#ID</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Duration</th>
						<th>Round</th>
						<th>URL</th>
						<th></th>

					</tr>
					<?php
					foreach($this->competitions as $p) {
						?>
						<tr>
							<td><?php p($p["fight_no"]); ?></td>
							<td><?php p($p["country_short_white"]); ?></td>
							<td><?php p($p["person_white"]); ?></td>
							<td><?php p($p["person_blue"]); ?></td>
							<td><?php p($p["country_short_blue"]); ?></td>
							<td><?php p($p["ippon_w"]); ?></td>
							<td><?php p($p["waza_w"]); ?></td>
							<td><?php p($p["yuko_w"]); ?></td>
							<td><?php p($p["ippon_b"]); ?></td>
							<td><?php p($p["waza_b"]); ?></td>
							<td><?php p($p["yuko_b"]); ?></td>

							<td><?php p($p["duration"]); ?></td>
							<td><?php p($p["round_name"]); ?></td>
							<td><?php p($p["contest_code_long"]); ?></td>
							<td style="background-color: #286090;">
								<a style="color: #ffffff" href="profileContests/downloadResults?contest_code=<?php echo $p["contest_code"]; ?>">Download Results02</a>

							</td>

						</tr>
						<?php
					}
					?>
				</table>
			</div>
		</div>
	</div>


<?php }else {}?>