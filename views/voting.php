<form class="poll" action="<?=$action?>" method="post">
    <p><?=$this->text('caption_vote')?></p>
<?php foreach ($keys as $key):?>
    <label class="poll_option">
        <input type="<?=$type?>" name="poll_<?=$name?>[]" value="<?=$key?>">
        <?=$key?>
    </label>
<?php endforeach?>
    <p>
        <button><?=$this->text('label_vote')?></button>
    </p>
</form>
