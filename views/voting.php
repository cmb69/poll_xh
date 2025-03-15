<?php

use Plib\View;

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
<?php foreach ($keys as $key):?>
  <label class="poll_option">
    <input type="<?=$this->esc($type)?>" name="poll_<?=$this->esc($name)?>[]" value="<?=$this->esc($key)?>">
    <?=$this->esc($key)?>
  </label>
<?php endforeach?>
  <p>
    <button><?=$this->text('label_vote')?></button>
  </p>
</form>
