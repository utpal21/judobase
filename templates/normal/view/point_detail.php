<?php if (!$this->script_mode()) { ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-8 col-xs-12">
            <h2>
                <?php p($this->oPlayer->player_second_name); ?> <?php p($this->oPlayer->player_first_name); ?>
            </h2>
            <p> <?php p($this->oPlayer->weight); ?>kg級 (最終更新日:<?php p($this->date_label); ?>)</p>
            <table class="table table-bordered table-condensed table-hover table-striped list-table-head table-point">
                <tbody>
                    <tr>
                        <th>世界ランキング</th>
                        <td><?php p($this->oPlayer->champion_point); ?></td>
                        <td><?php p($this->oPlayer->champion_rank); ?>位</td>
                    </tr>
                    <tr>
                        <th>オリンピック</th>
                        <td><?php p($this->oPlayer->olympics_point); ?></td>
                        <td><?php p($this->oPlayer->olympics_rank); ?>位</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-4 col-xs-12 text-center">
            <br/>
            <img src="<?php p($this->oPlayer->avartar_url); ?>">
        </div>
    </div>

    <div class="row">
        <div class="col-sm-5 col-xs-12">
            <h4>ポイント合計</h4>
            <table class="table table-bordered table-hover table-striped table-condensed table-point">
                <tbody>
                    <tr>
                        <th>国内大会合計</th>
                        <td><?php p($this->pointsum["national"]); ?></td>
                    </tr>
                    <tr>
                        <th>国際大会合計</th>
                        <td><?php p($this->pointsum["international"]); ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">直近1年（100%）</th>
                    </tr>
                    <tr>
                        <td>国際大会ポイント獲得率</td>
                        <td><?php p($this->percent["percent_one"]); ?></td>
                    </tr>
                    <tr>
                        <td>Best</td>
                        <td><?php p($this->top_one[0]); ?></td>
                    </tr>
                    <tr>
                        <td>Second</td>
                        <td><?php p($this->top_one[1]); ?></td>
                    </tr>
                    <tr>
                        <td>Third</td>
                        <td><?php p($this->top_one[2]); ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">1年以上2年未満（50%）</th>
                    </tr>
                    <tr>
                        <td>国際大会ポイント獲得率</td>
                        <td><?php p($this->percent["percent_two"]); ?></td>
                    </tr>
                    <tr>
                        <td>Best</td>
                        <td><?php p($this->top_two[0]); ?></td>
                    </tr>
                    <tr>
                        <td>Second</td>
                        <td><?php p($this->top_two[1]); ?></td>
                    </tr>
                    <tr>
                        <td>Third</td>
                        <td><?php p($this->top_two[2]); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-7 col-xs-12">
            <h4>競技結果</h4>
            <table class="table table-bordered table-hover table-striped table-point">
                <thead>
                    <tr>
                        <th width="80px">年度</th>
                        <th>大会名</th>
                        <th width="80px" class="hidden-xs hidden-sm">個人/団体</th>
                        <th width="70px" class="hidden-xs hidden-sm">階級</th>
                        <th width="70px" class="hidden-xs hidden-sm">順位</th>
                        <th width="100px" class="hidden-xs hidden-sm">ポイント</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($this->results as $result) {
                            if ($result["status"] == 1) {
                    ?>
                    <tr>
                        <td>
                            <?php p($result["year"]); ?>年
                        </td>
                        <td>
                            <strong><?php p($result["tournament_name"]); ?></strong>
                            <p class="hidden-md hidden-lg">
                                <?php p($result["play_mode_name"]); ?> <?php p($result["weight_name"]); ?>kg
                                <span class="badge badge-warning"><?php p($result["rank_name"]); ?></span>
                                <br class="hidden-sm"/>ポイント:<span class="badge badge-info"><?php p($result["value"]); ?></span>
                            </p>
                        </td>
                        <td class="hidden-xs hidden-sm"><?php p($result["play_mode_name"]); ?></td>
                        <td class="hidden-xs hidden-sm"><?php p($result["weight_name"]); ?>kg</td>
                        <td class="hidden-xs hidden-sm"><?php p($result["rank_name"]); ?></td>
                        <td class="hidden-xs hidden-sm"><?php p($result["value"]); ?></td>
                    </tr>
                    <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
            <!--
            <h4>海外有力選手勝利結果</h4>
            <table class="table table-bordered table-hover table-striped list-table-head">
                <thead>
                    <tr>
                        <th width="70px">年度</th>
                        <th width="250px" class="hidden-xs">大会名</th>
                        <th>選手名</th>
                        <th width="70px" class="hidden-xs">階級</th>
                        <th width="100px" class="hidden-xs">ポイント</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($this->champions as $champion) {
                    ?>
                    <tr>
                        <td width="70px">
                            <?php p($champion["open_year"]); ?>年
                        </td>
                        <td class="hidden-xs">
                            <?php p($champion["tname_name"]); ?>
                        </td>
                        <td>
                            <strong><?php p($champion["player_name"]); ?></strong>
                        </td>
                        <td class="hidden-xs"><?php p($champion["weight_name"]); ?>kg</td>
                        <td class="hidden-xs"><?php p($champion["point_sum"]); ?></td>
                    </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
            -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 text-right">
            <a class="btn btn-primary" href="point/index/<?php p($this->oPlayer->gender); ?>/<?php p($this->oPlayer->weight_id); ?>/<?php p($this->date); ?>">戻る</a>
        </div>
    </div>
    <br/>
</div>
<?php } else { ?>
<?php } ?>