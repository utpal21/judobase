<?php if (!$this->script_mode()) { ?>
<div class="container-fluid">
    <h2><?php p($this->gender_label); ?><?php p($this->weight_label); ?>kg級選手ポイント一覧</h2>
    <p>(最終更新日:<?php p($this->date_label); ?>)</p>
    <div class="row">
        <div class="col-xs-12 auto-scroll-x">
            <table class="table table-bordered table-hover table-striped table-condensed table-point" style="min-width:750px;">
                <thead>
                    <tr>
                        <th rowspan="2"　width="40px">#</th>
                        <th rowspan="2">選手名</th>
                        <th class="back-blue" rowspan="2">国内<span class="hidden-xs hidden-sm">大会合計</span></th>
                        <th class="back-blue" rowspan="2">国際<span class="hidden-xs hidden-sm">大会合計</span></th>
                        <th colspan="4"><small>国際大会　直近1年（100%）</small></th>
                        <th colspan="4"><small>国際大会　1年以上2年未満（50%）</small></th>
                    </tr>
                    <tr>
                        <th class="back-green" width="70px"><span class="hidden-xs hidden-sm">ポイント</span>獲得率</th>
                        <th class="back-yellow" width="70px">Best</th>
                        <th class="back-yellow" width="70px">Second</th>
                        <th class="back-yellow" width="70px">Third</th>
                        <th class="back-green" width="70px"><span class="hidden-xs hidden-sm">ポイント</span>獲得率</th>
                        <th class="back-yellow" width="70px">Best</th> 
                        <th class="back-yellow" width="70px">Second</th>
                        <th class="back-yellow" width="70px">Third</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                        foreach ($this->oPlayers as $player) {
                            if($player->point_sum1 != null || $player->point_sum2 != null ) {
                    ?>
                    <tr>
                        <td width="40px"><?php p($i); ?></td>
                        <td width="130px">
                            <a href="point/detail/<?php $player->detail("player_id");?>/<?php p($this->date); ?>"><?php $player->detail("player_second_name"); ?> <?php $player->detail("player_first_name"); ?></a>
                        </td>
                        <td class="text-center"><?php $player->detail("point_sum1"); ?></td>
                        <td class="text-center"><?php $player->detail("point_sum2"); ?></td>
                        <td class="text-center"><?php $player->detail("year1_rate"); ?></td>
                        <td class="text-center"><?php $player->detail("year1_point1"); ?></td>
                        <td class="text-center"><?php $player->detail("year1_point2"); ?></td>
                        <td class="text-center"><?php $player->detail("year1_point3"); ?></td>
                        <td class="text-center"><?php $player->detail("year2_rate"); ?></td>
                        <td class="text-center"><?php $player->detail("year2_point1"); ?></td>
                        <td class="text-center"><?php $player->detail("year2_point2"); ?></td>
                        <td class="text-center"><?php $player->detail("year2_point3"); ?></td>
                    </tr>
                    <?php
                                $i ++;
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 text-right">
            <a class="btn btn-primary" href="point/">戻る</a>
        </div>
    </div>
    <br/>
</div>
<?php } else { ?>
<?php } ?>