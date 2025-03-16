<?php

use Plib\View;

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var bool $isAdministration
 * @var bool $isFinished
 * @var bool $hasMessage
 * @var int $totalVotes
 * @var list<object{count:int,key:string,percentage:string}> $votes
 */
?>

<?if (!$isAdministration):?>
<?  if ($isFinished):?>
<p><?=$this->text('caption_ended')?></p>
<?  elseif ($hasMessage):?>
<p><?=$this->text('caption_voted')?></p>
<?  endif?>
<h5><?=$this->text('caption_results')?></h5>
<?endif?>
<div class="poll_results">
<?foreach ($votes as $vote):?>
  <div class="poll_result">
    <div class="poll_text"><?=$this->plural('label_result', $vote->count, $vote->key, $vote->percentage)?></div>
    <div class="poll_bar" style="width: <?=$this->esc($vote->percentage)?>%">&nbsp;</div>
  </div>
<?endforeach?>
</div>
<p><strong><?=$this->plural('caption_total', $totalVotes)?></strong></p>
