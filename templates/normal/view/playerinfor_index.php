<?php if (!$this->script_mode()) { ?>

    <div class="container">

      <form id="form" class="form-signin form-horizontal" action="playerinfor/scrap_player" role="form" method="post">
		<div class="row">
				<fieldset>
				<div class="control-group">
					<div class="controls control-action">
						<a href="playerinfor/scrap_player">Get data before download csv(Information Player)</a>
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
						<th >Competitor</th>
						<th>Birth date</th>

					</tr>
					<?php
					foreach($this->players as $player) {
						?>
						<tr>

							<td style="text-align: center;"><?php p(_str2html($player["competitor"])); ?></td>
							<td style="text-align: center;"><?php p(date("j M Y",strtotime($player["birth_date"]))); ?></td>
							<td style="align-content: center; text-align: center; width: 100px;"><a style="background-color: #286090; height: 30px; width: 100px; color: #fff; text-align: center; cursor: pointer; display: inline-block" href="playerinfor/download_result?id_person=<?php p($player["id_person"])?>">Down Results</a></td>
							<td style="align-content: center; text-align: center; width: 100px;" > <a  style="background-color: #286090; height: 30px; width: 100px;color: #fff;text-align: center; cursor: pointer; display: inline-block" href="playerinfor/download_list?id_person=<?php p($player["id_person"])?>">Down List</a> </td>
							<td style="align-content: center; text-align: center; width: 100px;" > <a  style="background-color: #286090; height: 30px; width: 100px;color: #fff;text-align: center; cursor: pointer; display: inline-block" href="playerinfor/download_WRL?id_person=<?php p($player["id_person"])?>">Down WRL</a> </td>

						</tr>
						<?php
					}
					?>
				</table>
			</div>
		</div>
	</div>


<?php }else {}?>