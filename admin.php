<?php

/**
 * Back-End of Poll_XH
 *
 * Copyright (c) 2012, Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns (x)html plugin version information.
 *
 * @return string
 */
function poll_version() {
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Poll_XH">Poll_XH</a></h1>'."\n"
	    .tag('img src="'.$pth['folder']['plugins'].'poll/poll.png" width="128"'
	    .' height="128" alt="Plugin icon" class="poll_plugin_icon"')
	    .'<p style="margin-top: 1em">Version: '.POLL_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2012 <a href="http://3-magi.net/">Christoph M. Becker</a></p>'."\n"
	    .'<p class="poll_license">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p class="poll_license">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p class="poll_license">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns requirements information.
 *
 * @return string
 */
function poll_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('POLL_PHP_VERSION', '4.3.0');
    $ptx = $plugin_tx['poll'];
    $imgdir = $pth['folder']['plugins'].'poll/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $o = '<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, POLL_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], POLL_PHP_VERSION)
	    .tag('br')."\n";
    foreach (array('date', 'pcre') as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br').tag('br')."\n";
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'poll/'.$folder;
    }
    $folders[] = poll_data_folder();
    foreach ($folders as $folder) {
	$o .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $o;
}


/**
 * Returns the available polls.
 *
 * @return array
 */
function poll_polls() {
    $dn = poll_data_folder(); // TODO: .htaccess for data folder
    $files = glob($dn.'*.csv');
    $polls = array();
    foreach ($files as $file) {
	$polls[] = basename($file, '.csv');
    }
    return $polls;
}


function poll_hjs() {
    global $hjs, $pth, $plugin_cf;

    include $pth['folder']['plugins'].'jquery/jquery.inc.php';
    include_jquery();
    $hjs .= '<script type="text/javascript" src="'.$pth['folder']['plugins']
	    .'poll/admin.js"></script>'."\n";
}


function poll_tool_icon($name) {
    global $pth, $plugin_tx;

    $ptx = $plugin_tx['poll'];
    $o = '<a href="javascript:Poll.'.$name.'()">'
	    .tag('img src="'.$pth['folder']['plugins'].'poll/images/'.$name.'.png"'
	    .' alt="'.$ptx['tool_'.$name].'" title="'.$ptx['tool_'.$name].'"')
	    .'</a>'."\n";
    return $o;
}


function poll_edit_option($key = NULL, $count = NULL) {
    $attrs = isset($key) ? '' : ' id="poll_template" style="display: none"';
    $o = '<div class="poll_option"'.$attrs.'>'
	    .tag('input type="text" name="poll_option[]" value="'.htmlspecialchars($key, ENT_QUOTES, 'UTF-8').'" class="poll_option" onfocus="Poll.select(this)"')
	    .tag('input type="text" name="poll_count[]" value="'.$count.'" class="poll_count" onfocus="Poll.select(this)"')
	    .'</div>'."\n";

    return $o;
}


function poll_edit($name) {
    global $sn;

    poll_hjs();
    $data = poll_data($name);
    $o = $name;
    $url = $sn.'?&amp;poll&amp;admin=plugin_main&amp;action=save&amp;poll_name='.$name;
    $o .= '<form id="poll_edit" action="'.$url.'" method="POST">'."\n"
	    .'<fieldset id="poll_general">'
	    .'<legend>General</legend>'
	    .'<label>Max. Options</label>'
	    .tag('input type="text" name="poll_max" value="'.$data['max'].'"').tag('br')
	    .'<label>End Date</label>'
	    .tag('input type="text" name="poll_end" value="'.$data['end'].'"').tag('br')
	    .'<label>Total Voters</label>'
	    .tag('input type="text" name="poll_total" value="'.$data['total'].'"').tag('br')
	    .'</fieldset>'
	    .'<fieldset id="poll_options">'
	    .'<legend>Options</legend>';
    $o .= poll_edit_option();
    $o .= '<div id="poll_toolbar">';
    foreach (array('add', 'remove', 'up', 'down') as $tool) {
	$o .= poll_tool_icon($tool);
    }
    $o .= '</div>';
    foreach ($data['votes'] as $key => $count) {
	$o .= poll_edit_option($key, $count);
    }
    $o .= '</fieldset>'
	    .tag('input type="submit" class="submit"')
	    .'</form>'."\n";
    return $o;
}


function poll_save($name) {
    echo $name;
    var_dump($_POST);
}


/**
 * Returns the plugin's main administration view.
 *
 * @return string  The (X)HTML.
 */
function poll_plugin_admin() {
    $o = '<div id="poll_admin">'."\n";
    foreach (poll_polls() as $poll) {
	$o .= '<h1>'.$poll.'</h1>'.poll_results_view($poll)."\n";
    }

    $o .= '</div>'."\n";
    return $o;
}


/**
 * Handle the plugin administration.
 */
if (!empty($poll)) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
	case '':
	    $o .= poll_version().tag('hr').poll_system_check();
	    break;
	case 'plugin_main':
	    switch ($action) {
		case 'edit':
		    $o .= poll_edit($_GET['poll_name']);
		    break;
		case 'save':
		    $o .= poll_save($_GET['poll_name']);
		    break;
		default:
		    $o .= poll_plugin_admin();
	    }
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
