<?php

use Plib\View;

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $action
 * @var list<string> $keys
 * @var string $name
 * @var string $type
 */
?>

<form class="poll" action="<?=$this->esc($action)?>" method="post">
  <p><?=$this->text('caption_vote')?></p>
<?foreach ($keys as $key):?>
  <label class="poll_option">
    <input type="<?=$this->esc($type)?>" name="poll_<?=$this->esc($name)?>[]" value="<?=$this->esc($key)?>">
    <?=$this->esc($key)?>
  </label>
<?endforeach?>
  <p>
    <button><?=$this->text('label_vote')?></button>
  </p>
</form>
