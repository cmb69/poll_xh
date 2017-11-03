<form class="poll" action="<?=$action?>" method="post">
    <p><?=$this->text('caption_vote')?></p>
    <ul>
<?php foreach ($keys as $key):?>
        <li>
            <label>
                <input type="<?=$type?>" name="poll_<?=$name?>[]"
                    value="<?=$key?>">
                <?=$key?>
            </label>
        </li>
<?php endforeach?>
    </ul>
    <button><?=$this->text('label_vote')?></button>
</form>
