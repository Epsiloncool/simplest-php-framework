<?php

require_once dirname(__FILE__).'/startup.php';
require_once dirname(__FILE__).'/router.php';

global $cuser;

$cuser = DetectCurrentUser();

// Router
RouteRequest();
