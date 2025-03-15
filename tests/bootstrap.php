<?php

require_once './vendor/autoload.php';

require_once '../../cmsimple/functions.php';

require_once '../plib/classes/Response.php';
require_once '../plib/classes/SystemChecker.php';
require_once '../plib/classes/View.php';
require_once '../plib/classes/FakeSystemChecker.php';

require_once './classes/DataService.php';
require_once './classes/Dic.php';
require_once './classes/InfoController.php';
require_once './classes/MainAdminController.php';
require_once './classes/Poll.php';
require_once './classes/WidgetController.php';

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const CMSIMPLE_ROOT = "/";
const POLL_VERSION = "1.0beta3";
