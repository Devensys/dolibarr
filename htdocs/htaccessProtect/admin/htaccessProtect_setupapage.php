<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);


/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014-2015  Teddy Andreotti <125155@supinfo.com>
 * Copyright (C) 2014-2015  Virgile Cabane <129596@supinfo.com>
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

require_once("../../filefunc.inc.php");
require_once("../htaccessprotectip.class.php");
require_once("../htaccessprotectaccount.class.php");

$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
dol_include_once('/module/class/skeleton_class.class.php');

// Load traductions files requiredby by page
$langs->load("htaccessProtect@htaccessProtect");

// Load user and its permissions
$result=$user->fetch('','admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
if (! $result > 0) { dol_print_error('',$user->error); exit; }
$user->getrights();

// Get parameters
$o			= GETPOST('o','int');
if(empty($o)) $o = 0;


// Protection if external user
if ($user->societe_id > 0)
{
    echo "OKKKK";
    //accessforbidden();
}




/*******************************************************************
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 ********************************************************************/

// Start of transaction
$db->begin();
$htaccessprotectip = new Htaccessprotectip($db);
$htaccessprotectaccount = new Htaccessprotectaccount($db);

if (GETPOST('action')) {
    switch(GETPOST('action')) {
        case 'create':
            $htaccessprotectip->name = GETPOST('name');
            $htaccessprotectip->ip = GETPOST('ip');
            $htaccessprotectip->trusted = (GETPOST('trusted') == 'on');
            $id = $htaccessprotectip->create($user);
            $db->commit();
            break;
        case 'delete':
            $htaccessprotectip->fetch(GETPOST('id'));
            $result = $htaccessprotectip->delete($user);
            $db->commit();
            break;
    }
}

//$db->close();




/***************************************************
 * VIEW
 *
 * Put here all code to build page
 ****************************************************/

llxHeader('','Htaccess Protect','');

//Adding jquery code
/*
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {

});
</script>';
*/

//print_fiche_titre($langs->trans("Htaccess Protect"));
print_fiche_titre("Htaccess Protect");

dol_fiche_head(array(array("?o=0", $langs->trans("ConfActive"), "ActiveConf"),
                     array("?o=1", $langs->trans("EditConf"), "ModConf"),
                     array("?o=2", $langs->trans("HtaccessContent"), "AffFiles")), $o);


//print info_admin($langs->trans("Display Info "));

// Tab confActive
if($o==0){
    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("generalinfo").'</td>';
    print '    <td width="50" ></td>';
    print '    <td align="right" width="160">&nbsp;</td>';
    print '  </tr>';
    $var = true;

    $right = substr(sprintf('%o',fileperms(DOL_DOCUMENT_ROOT)), -3);

    if($right < 755){
        print info_admin($langs->trans("Display Info "));
        //TODO changer le text et faire le filtrage des droit correctement ...
    }

    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("DirRight").'</td>';
    print '    <td align="right">' . img_picto("e", "delete" ) . '</td>';
    print '    <td>' . $right . '</td>';
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("DirRight").'</td>';
    $verapache = apache_get_version();
    print '    <td align="right">' . img_picto("e", strpos($verapache, '2.4') !== false ? "tick" : "delete" ) . '</td>';
    print '    <td>' . apache_get_version() . '</td>';
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("FileExisteHtaccess").'</td>';
    if(file_exists(DOL_DOCUMENT_ROOT."/.htaccess")){
        if(true != false){
            print '    <td align="right">' . img_picto("e", "tick" ) . '</td>';
            print '    <td>' . $langs->trans("fileok") . '</td>';
        }else{
            print '    <td align="right">' . img_picto("e", "delete" ) . '</td>';
            print '    <td>' . $langs->trans("fileko") . '</td>';
        }
    }else{
        print '    <td align="right">' . img_picto("e", "delete" ) . '</td>';
        print '    <td>' . $langs->trans("filemissing") . '</td>';
    }
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("FileExisteHtpasswd").'</td>';
    if(file_exists(DOL_DOCUMENT_ROOT."/.htpasswd")){
        if(true != false){
            print '    <td align="right">' . img_picto("e", "tick" ) . '</td>';
            print '    <td>' . $langs->trans("fileok") . '</td>';
        }else{
            print '    <td align="right">' . img_picto("e", "delete" ) . '</td>';
            print '    <td>' . $langs->trans("fileko") . '</td>';
        }
    }else{
        print '    <td align="right">' . img_picto($langs->trans("filemissing"), "delete" ) . '</td>';
        print '    <td>' . $langs->trans("filemissing") . '</td>';
    }
    print '  </tr>';
    print '</table>';
}

// Tab EditConf
if($o==1){
    $ipList = $htaccessprotectip->fetchAll();
    $accountList = $htaccessprotectaccount->fetchAll();

    // IP Table
    print '<form id="ip_create" action="htaccessProtect_setupapage.php?o=1" method="POST">';
    print '  <input style="display: none;" name="action" value="create"/>';
    print '  <table class="noborder" width="100%">';
    print '    <tr class="liste_titre">';
    print '      <td>'.$langs->trans("name").'</td>';
    print '      <td>'.$langs->trans("IP").'</td>';
    print '      <td>'.$langs->trans("whitelist").'</td>';
    print '      <td>&nbsp;</td>';
    print '    </tr>';
    $var = true;

    if (sizeof($ipList)) {
        foreach($ipList as $ip) {
            $list = ($ip->trusted)?img_picto("ok", "tick").$langs->trans("whitelist"):img_picto("ko", "delete").$langs->trans("blacklist");

            print '    <tr '.$bc[$var].'>';
            print '      <td width="60%">' . $ip->name . '</td>';
            print '      <td>' . $ip->ip . '</td>';
            print '      <td>' . $list . '</td>';
            print '      <td>';
            print '        <a href="htaccessProtect_setupapage.php?o=1&action=delete&id=' . $ip->id . '" class="ip_delete">'.img_picto($langs->trans("delete"), "delete").'</a>';
            print '      </td>';
            print '    </tr>';
            $var=!$var;
        }
    } else {
        print '    <tr '.$bc[$var].' style="color:grey; font-style: italic;">';
        print '      <td width="60%">' . $langs->trans("noip") . '</td>';
        print '      <td>&nbsp;</td>';
        print '      <td>&nbsp;</td>';
        print '      <td>&nbsp;</td>';
        print '    </tr>';
        $var=!$var;
    }

    print '    <tr '.$bc[$var].'>';
    print '      <td width="60%">';
    print '        <input class="flat" id="name" name="name" placeholder="' . $langs->trans("name") . '"/>';
    print '      </td>';
    print '      <td>';
    print '        <input class="flat" id="ip" name="ip" placeholder="' . $langs->trans("IP") . '"/>';
    print '      </td>';
    print '      <td>';
    print '        <input type="checkbox" class="flat" name="trusted" checked="checked"/>';
    print '      </td>';
    print '      <td>';
    print '        <input type="submit" class="flat" value="' . $langs->trans("add") . '"/>';
    print '      </td>';
    print '    </tr>';

    print '  </table>';
    print '</form>';


    // Account Table
    print '<form id="account_create" action="htaccessProtect_setupapage.php?o=1" method="POST">';
    print '  <input style="display: none;" name="action" value="create"/>';
    print '  <table class="noborder" width="100%">';
    print '    <tr class="liste_titre">';
    print '      <td>'.$langs->trans("pseudo").'</td>';
    print '      <td>'.$langs->trans("password").'</td>';
    print '      <td>&nbsp;</td>';
    print '    </tr>';
    $var = true;

    if (sizeof($accountList)) {
        foreach($accountList as $account) {
            print '    <tr '.$bc[$var].'>';
            print '      <td width="60%">' . $account->pseudo . '</td>';
            print '      <td>' . $account->passwd . '</td>';
            print '      <td>';
            print '        <a href="htaccessProtect_setupapage.php?o=1&action=delete&entity=account&id=' . $account->id . '">'.img_picto($langs->trans("delete"), "delete").'</a>';
            print '      </td>';
            print '    </tr>';
            $var=!$var;
        }
    } else {
        print '    <tr '.$bc[$var].' style="color:grey; font-style: italic;">';
        print '      <td width="60%">' . $langs->trans("noaccount") . '</td>';
        print '      <td>&nbsp;</td>';
        print '      <td>&nbsp;</td>';
        print '    </tr>';
        $var=!$var;
    }

    print '    <tr '.$bc[$var].'>';
    print '      <td width="60%">';
    print '        <input class="flat" name="pseudo" placeholder="' . $langs->trans("pseudo") . '"/>';
    print '      </td>';
    print '      <td>';
    print '        <input class="flat" name="passwd" placeholder="' . $langs->trans("password") . '"/>';
    print '      </td>';
    print '      <td>';
    print '        <input type="submit" class="flat" value="' . $langs->trans("add") . '"/>';
    print '      </td>';
    print '    </tr>';

    print '  </table>';
    print '</form>';

    print '<div id="dialog-confirm" title="Erreur" style="display: none;">';
    print '  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Veuillez renseigner tous les champs</p>';
    print '</div>';

    print '<div id="dialog-confirm-ip" title="Erreur" style="display: none;">';
    print '  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>L\'adresse IP renseignée est mal formée</p>';
    print '</div>';


    print ' <script type="text/javascript" language="javascript">
            jQuery(document).ready(function() {
                jQuery("#ip_create").submit(function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if(jQuery("#name").val() == "" || jQuery("#ip").val() == "") {
                        jQuery("#dialog-confirm").dialog({
                            resizable: false,
                            modal: true,
                            buttons: {
                                Ok: function() {
                                    jQuery( this ).dialog( "close" );
                                }
                            }
                        });
                    } else if(!jQuery("#ip").val().match(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/)) {
                        jQuery("#dialog-confirm-ip").dialog({
                            resizable: false,
                            modal: true,
                            buttons: {
                                Ok: function() {
                                    jQuery( this ).dialog( "close" );
                                }
                            }
                        });
                    } else {
                        $(this).unbind("submit").submit();
                    }
                });
            });
            </script>';
}

// Tab HtaccessContent
if($o==2){
    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("ContenuHtaccess").'</td>';
    print '  </tr>';
    print '  <tr>';
    print '    <td><pre style="padding: 5px"><code>';
    print htmlentities(file_get_contents(DOL_DOCUMENT_ROOT."/.htaccess"));

    print '    </code></pre></td>';
    print '  </tr>';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("ContenuHtpassword").'</td>';
    print '  </tr>';
    print '  <tr>';
    print '    <td><pre style="padding: 5px"><code>';
    print htmlentities(file_get_contents(DOL_DOCUMENT_ROOT."/.htpasswd"));

    print '    </code></pre></td>';
    print '  </tr>';
    print '</table>';

}
dol_fiche_end();
// End of page
llxFooter();
$db->close();
