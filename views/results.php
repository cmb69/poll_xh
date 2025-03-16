<?php

use Plib\View;

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var bool $isAdministration
 * @var bool $isFinished
 * @var string $message
 * @var int $totalVotes
 * @var list<object{count:int,key:string,percentage:string}> $votes
 */
?>

<?if (!$isAdministration):?>
<?  if ($isFinished):?>
<p class="xh_info"><?=$this->text('caption_ended')?></p>
<?  elseif ($message):?>
<p class="xh_info"><?=$this->text($message)?></p>
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
