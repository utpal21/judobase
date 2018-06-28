<?php if (!$this->script_mode()) { ?>
<div class="container-fluid">
    <h2>階級一覧</h2>

    <h4>男子</h4>
    <div class="row">
        <?php
            foreach ($this->oManUrls as $u) {
        ?>
            <div class="col-xs-6 col-sm-3 hidden-md hidden-lg text-center">
                <a class="btn btn-primary btn-large btn-point btn-man" href="<?php p($u["url"]); ?>"><h2><?php p($u["weight_name"]); ?></h2><h5>kg級</h5></a>
            </div>
        <?php
            }
        ?>

        <div class="col-md-12 hidden-xs hidden-sm text-center">
            <?php
                foreach ($this->oManUrls as $u) {
            ?>
                <a class="btn btn-primary btn-large btn-point" href="<?php p($u["url"]); ?>"><h2><?php p($u["weight_name"]); ?></h2><h5>kg級</h5></a>
            <?php
                }
            ?>
        </div>
    </div>

    <br/>
    <h4>女子</h4>
    <div class="row">
        <?php
            foreach ($this->oWomanUrls as $u) {
        ?>
            <div class="col-xs-6 col-sm-3 hidden-md hidden-lg text-center">        
                <a class="btn btn-danger btn-large btn-point" href="<?php p($u["url"]); ?>"><h2><?php p($u["weight_name"]); ?></h2><h5>kg級</h5></a>
            </div>
        <?php
            }
        ?>

        <div class="col-md-12 hidden-xs hidden-sm text-center">
            <?php
                foreach ($this->oWomanUrls as $u) {
            ?>
                <a class="btn btn-danger btn-large btn-point" href="<?php p($u["url"]); ?>"><h2><?php p($u["weight_name"]); ?></h2><h5>kg級</h5></a>
            <?php
                }
            ?>
        </div>        
    </div>
    <br/>
    <br/>
</div>
<?php } else { ?>
<?php } ?>