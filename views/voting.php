<form class="poll" action="<?=$this->action()?>" method="post">
    <p><?=$this->text('caption_vote')?></p>
    <ul>
<?php foreach ($this->keys as $key):?>
        <li>
            <label>
                <input type="<?=$this->type()?>" name="poll_<?=$this->name()?>[]"
                    value="<?=$this->escape($key)?>">
                <?=$this->escape($key)?>
            </label>
        </li>
<?php endforeach?>
    </ul>
    <button><?=$this->text('label_vote')?></button>
</form>
