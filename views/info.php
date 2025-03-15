<?php

use Plib\View;

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<array{class:string,label:string,stateLabel:string}> $checks
 * @var string $version
 */
?>

<h1>Poll <?=$this->esc($version)?></h1>
<div class="poll_syscheck">
  <h2><?=$this->text('syscheck_title')?></h2>
<?foreach ($checks as $check):?>
  <p class="<?=$this->esc($check['class'])?>"><?=$this->text('syscheck_message', $check['label'], $check['stateLabel'])?></p>
<?endforeach?>
</div>
