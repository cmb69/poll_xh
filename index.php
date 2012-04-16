<?php

/**
 * Front-End of Poll_XH
 *
 * Copyright (c) 2012, Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('POLL_VERSION', '1beta1');


define('POLL_TOTAL', '%%%TOTAL%%%');
define('POLL_MODE', '%%%MODE%%%');
define('POLL_END', '%%%END%%%');


/**
 * Returns the path to the data folder.
 *
 * @return string
 */
function poll_data_folder() {
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['poll'];

    if ($pcf['folder_data'] == '') {
	$fn = $pth['folder']['plugins'].'poll/data/';
    } else {
	$fn = $pth['folder']['base'].$pcf['folder_data'];
    }
    if (substr($fn, -1) != '/') {$fn .= '/';}
    if (file_exists($fn)) {
	if (!is_dir($fn)) {
	    e('cntopen', 'folder', $fn);
	}
    } else {
	if (!mkdir($fn, 0777, TRUE)) {
	    e('cntwriteto', 'folder', $fn);
	}
    }
    return $fn;
}


/**
 * Returns the poll data.
 *
 * @param string $name  The name of the poll.
 * @param array $recs  The new records.
 * @return array
 */
function poll_data($name, $ndata = NULL) {
    static $cname = NULL, $data = NULL;

    $fn = poll_data_folder().$name.'.csv';
    if (is_null($ndata)) {
	if (is_null($data) || $name != $cname) {
	    $cname = $name;
	    $data = array('multi' => FALSE, 'end' => 2147483647, 'total' => 0);
	    if (($lines = file($fn)) !== FALSE) {
		foreach ($lines as $line) {
		    $rec = explode("\t", rtrim($line));
		    switch ($rec[0]) {
			case POLL_MODE:
			    $data['multi'] = $rec[1] == 'multi';
			    break;
			case POLL_END:
			    $data['end'] = $rec[1];
			    break;
			case POLL_TOTAL:
			    $data['total'] = $rec[1];
			    break;
			default:
			    $data['votes'][$rec[0]] = isset($rec[1]) ? $rec[1] : 0;
		    }
		}
	    }
	}
    } else {
	$cname = $name;
	$data = $ndata;
	$lines = array();
	foreach ($data['votes'] as $key => $count) {
	    $lines[] = $key."\t".$count;
	}
	$lines[] = POLL_MODE."\t".($data['multi'] ? 'multi' : 'single');
	$lines[] = POLL_END."\t".$data['end'];
	$lines[] = POLL_TOTAL."\t".$data['total'];
	if (($fh = fopen($fn, 'w')) === FALSE
		|| fwrite($fh, implode("\n", $lines)."\n") === FALSE) {
	    e('cntsave', 'file', $fn);
	}
	if ($fh !== FALSE) {fclose($fh);}
    }
    return $data;
}


/**
 * Returns wether the poll has ended.
 *
 * @param string $name  The name of the poll.
 * @return bool
 */
function poll_has_ended($name) {
    $data = poll_data($name);
    return $data['end'] <= time();
}


/**
 * Returns wether the current user has already voted.
 *
 * @return bool
 */
function poll_has_voted($name) {
    if (isset($_COOKIE['poll_'.$name]) && $_COOKIE['poll_'.$name] == CMSIMPLE_ROOT) {
	return TRUE;
    }
    $fn = poll_data_folder().$name.'.ips';
    if (!file_exists($fn)) {touch($fn);}
    if (($lines = file($fn)) === FALSE) {
	e('cntopen', 'file', $fn);
	return FALSE;
    }
    $ips = array_map('rtrim', $lines);
    return in_array($_SERVER['REMOTE_ADDR'], $ips);
}


/**
 * Returns wether there's a vote for the given poll.
 *
 * @param string $name  The name of the poll.
 * @return bool
 */
function poll_is_voting($name) {
    return isset($_POST['poll_'.$name]);
}


/**
 * Registers the new vote.
 *
 * @param string $name  The name of the poll.
 * @return void
 */
function poll_vote($name) {
    $fn = poll_data_folder().$name.'.ips';
    if (($fh = fopen($fn, 'a')) !== FALSE
	    && fwrite($fh, $_SERVER['REMOTE_ADDR']."\n") !== FALSE) {
	setcookie('poll_'.$name, CMSIMPLE_ROOT, $data['end']);
	$data = poll_data($name);
	foreach ($_POST['poll_'.$name] as $vote) {
	    $data['votes'][stsl($vote)]++;
	}
	$data['total']++;
	poll_data($name, $data);
    } else {
	e('cntwriteto', 'file', $fn);
    }
    if ($fh !== FALSE) {fclose($fh);}
}


/**
 * Returns the voting view.
 *
 * @param string $name  The name of the poll.
 * @return string  The (X)HTML.
 */
function poll_voting_view($name) {
    global $sn, $su, $plugin_tx;

    $ptx = $plugin_tx['poll'];
    $data = poll_data($name);
    $type = $data['multi'] ? 'checkbox' : 'radio';
    $o = '<form class="poll" action="'.$sn.'?'.$su.'" method="POST">'."\n"
	    .$ptx['caption_vote']."\n".'<ul>'."\n";
    $i = 0;
    foreach ($data['votes'] as $key => $dummy) {
	$o .= '<li>'
		.tag('input type="'.$type.'" id="poll_'.$name.$i.'" name="poll_'.$name.'[]" value="'.htmlspecialchars($key).'"')
		.'<label for="poll_'.$name.$i.'">'.$key.'</label></li>'."\n";
	$i++;
    }
    $o .= '</ul>'."\n"
	    .tag('input type="submit" value="'.$ptx['label_vote'].'"')."\n"
	    .'</form>'."\n";
    return $o;
}


/**
 * Returns the results view.
 *
 * @param string $name  The name of the poll.
 * @return string  The (X)HTML.
 */
function poll_results_view($name) {
    global $admin, $plugin_tx;

    $ptx = $plugin_tx['poll'];
    $data = poll_data($name);
    $o = $admin == 'plugin_main' ? ''
	    : (poll_has_ended($name) ? $ptx['caption_ended'] : $ptx['caption_voted'])."\n"
		.$ptx['caption_results']."\n";
    $o .= '<ul class="poll_results">'."\n";
    foreach ($data['votes'] as $key => $count) {
	$percentage = 100 * $count / $data['total'];
	$o .= '<li><div class="poll_results">'.sprintf($ptx['label_result'], htmlspecialchars($key), $percentage, $count).'</div>'
		.'<div class="poll_bar" style="width: '.$percentage.'%">&nbsp;</div></li>'."\n";
    }
    $o .= '</ul>'."\n";
    return $o;
}

/**
 * Returns the poll view.
 *
 * @access public
 *
 * @param string $name  The name of the poll.
 * @return string  The (X)HTML.
 */
function poll($name) {
    global $e, $plugin_tx;

    if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
	$e = '<li><strong>'.sprintf($plugin_tx['poll']['error_invalid_name'], $name).'</strong></li>'."\n";
	return FALSE;
    }
    $o = '';
    if (poll_has_ended($name) || poll_has_voted($name)) {
	$o .= poll_results_view($name);
    } elseif (poll_is_voting($name)) {
	poll_vote($name);
	$o .= poll_results_view($name);
    } else {
	$o .= poll_voting_view($name);
    }
    return $o;
}

?>
