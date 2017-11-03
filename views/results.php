<?php if (!$isAdministration):?>
<?php   if ($isFinished):?>
<p><?=$this->text('caption_ended')?></p>
<?php   elseif ($hasMessage):?>
<p><?=$this->text('caption_voted')?></p>
<?php   endif?>
<h5><?=$this->text('caption_results')?></h5>
<?php endif?>
<ul class="poll_results">
<?php foreach ($votes as $vote):?>
    <li>
        <div class="poll_results"><?=$this->plural('label_result', $vote->count, $vote->key, $vote->percentage)?></div>
        <div class="poll_bar" style="width: <?=$vote->percentage?>%">&nbsp;</div>
    </li>
<?php endforeach?>
</ul>
<p><strong><?=$this->plural('caption_total', $totalVotes)?></strong></p>
