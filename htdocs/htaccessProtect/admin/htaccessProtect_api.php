<?php
/* Copyright (C) 2007-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2015 Teddy Andreotti <125155@supinfo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *      \file       dev/skeletons/htaccessprotectip_script.php
 *		\ingroup    mymodule
 *      \brief      This file is an example for a command line script
 *					Initialy built by build_class_from_table on 2015-01-26 14:20
 */

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
    echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
    exit(-1);
}

// Global variables
$version='1.0';
$error=0;


// -------------------- START OF YOUR CODE HERE --------------------
@set_time_limit(0);							// No timeout for this script
define('EVEN_IF_ONLY_LOGIN_ALLOWED',1);		// Set this define to 0 if you want to lock your script when dolibarr setup is "locked to admin user only".

// Include and load Dolibarr environment variables
require_once($path."../../../htdocs/master.inc.php");
// After this $db, $mysoc, $langs, $conf and $hookmanager are defined (Opened $db handler to database will be closed at end of file).
// $user is created but empty.

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs->load("main");				// To load language file for default language

// Load user and its permissions
$result=$user->fetch('','admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
if (! $result > 0) { dol_print_error('',$user->error); exit; }
$user->getrights();
// Start of transaction
$db->begin();

// Examples for manipulating class skeleton_class
require_once("../htaccessprotectip.class.php");

$htaccessprotectip = new Htaccessprotectip($db);
$action = GETPOST('action');
switch($action) {
    case 'create':
        $htaccessprotectip->name = GETPOST('name');
        $htaccessprotectip->ip = GETPOST('ip');
        $htaccessprotectip->trusted = GETPOST('trusted');
        $id = $htaccessprotectip->create($user);
        $return = dol_json_encode($htaccessprotectip);
        break;
    case 'delete':
        $htaccessprotectip = $htaccessprotectip->fetch(GETPOST('id'));
        $result = $htaccessprotectip->delete($user);
        $return = dol_json_encode($result);
        break;
}


$db->commit();
$db->close();

print $return;