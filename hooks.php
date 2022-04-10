<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * Zepson SMS - https://portal.zepsonsms.co.tz/
 * Zepson SMS Portal - https://zepsonsms.co.tz/
 * Version 1.0
 *
 *
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

require_once("api.php");
$api = new zepsonsms();
$lists = $api->getLists();

foreach($lists as $lists){
    add_hook($lists['hook'], 1, $lists['function'], "");
}
