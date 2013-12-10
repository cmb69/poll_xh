<?php

$plugin_tx['poll']['menu_main']="Polls";

$plugin_tx['poll']['caption_vote']="<p>Your vote please:</p>";
$plugin_tx['poll']['caption_voted']="<p>You have already voted.</p>";
$plugin_tx['poll']['caption_just_voted']="<p>Thank you for voting!</p>";
$plugin_tx['poll']['caption_ended']="<p>The poll has ended.</p>";
$plugin_tx['poll']['caption_results']="<h5>The Results</h5>";
$plugin_tx['poll']['caption_total_singular']="<p><strong>In total %d person has voted.</strong></p>";
$plugin_tx['poll']['caption_total_plural_2-4']="<p><strong>In total %d people have voted.</strong></p>";
$plugin_tx['poll']['caption_total_plural_5']="<p><strong>In total %d people have voted.</strong></p>";

$plugin_tx['poll']['label_vote']="Vote now!";
$plugin_tx['poll']['label_result_singular']="%1\$s &ndash; %2\$.1f%% (%3\$d vote)";
$plugin_tx['poll']['label_result_plural_2-4']="%1\$s &ndash; %2\$.1f%% (%3\$d votes)";
$plugin_tx['poll']['label_result_plural_5']="%1\$s &ndash; %2\$.1f%% (%3\$d votes)";

$plugin_tx['poll']['error_invalid_name']="Invalid poll name '%s'! (must consist of 'a'-'z', '0'-'9' and '-' only)";
$plugin_tx['poll']['error_exceeded_max']="<p class=\"cmsimplecore_warning\">You may check %d options at most!</p>";

$plugin_tx['poll']['syscheck_title']="System check";
$plugin_tx['poll']['syscheck_phpversion']="PHP version &ge; %s";
$plugin_tx['poll']['syscheck_extension']="Extension '%s' loaded";
$plugin_tx['poll']['syscheck_encoding']="Encoding 'UTF-8' configured";
$plugin_tx['poll']['syscheck_magic_quotes']="Magic quotes runtime off";
$plugin_tx['poll']['syscheck_writable']="Folder '%s' writable";

$plugin_tx['poll']['cf_folder_data']="Path to a folder relative to the CMSimple root directory, where to store the plugin's data. E.g. <em>userfiles/poll/</em>. Leave empty to store into the plugin's data/ folder.";

?>
