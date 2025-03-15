<h1>Poll <?=$this->esc($version)?></h1>
<div class="poll_syscheck">
    <h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
    <p class="<?=$this->esc($check['class'])?>"><?=$this->text('syscheck_message', $check['label'], $check['stateLabel'])?></p>
<?php endforeach?>
</div>
