<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once("../../filefunc.inc.php");
require_once("../htaccessprotectip.class.php");


/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
/* Copyright (C) 2014-2015  Teddy Andreotti <125155@supinfo.com>
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

if (GETPOST('action')) {
    switch(GETPOST('action')) {
        case 'create':
            $htaccessprotectip->name = GETPOST('name');
            $htaccessprotectip->ip = GETPOST('ip');
            $htaccessprotectip->trusted = (GETPOST('trusted') == 'on');
            $id = $htaccessprotectip->create($user);
            $db->commit();
            //$return = dol_json_encode($htaccessprotectip);
            break;
        case 'delete':
            $deleteIp = $htaccessprotectip->fetch(GETPOST('id'));
            var_dump(GETPOST('id'));
            var_dump($htaccessprotectip);
            var_dump($deleteIp); die;
            $result = $deleteIp->delete($user);
            //$return = dol_json_encode($result);
            break;
    }
}

if ($o == 1) {
    $ipList = $htaccessprotectip->fetchAll();
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
    print '    <td>' . $right . '</td>';
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("FileExisteHtaccess").'</td>';
    if(file_exists(DOL_DOCUMENT_ROOT."/.htaccess")){
        if(true != false){
            print '    <td>' . $langs->trans("fileok") . '</td>';
        }else{
            print '    <td>' . $langs->trans("fileko") . '</td>';
        }
    }else{
        print '    <td>' . $langs->trans("filemissing") . '</td>';
    }
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("FileExisteHtpasswd").'</td>';
    if(file_exists(DOL_DOCUMENT_ROOT."/.htpasswd")){
        if(true != false){
            print '    <td>' . $langs->trans("fileok") . '</td>';
        }else{
            print '    <td>' . $langs->trans("fileko") . '</td>';
        }
    }else{
        print '    <td>' . $langs->trans("filemissing") . '</td>';
    }
    print '  </tr>';
    print '</table>';

    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("Ipfiltreinfo").'</td>';
    print '    <td align="right" width="160">&nbsp;</td>';
    print '  </tr>';
    $var = true;


    // TODO faire vraie requette
    $i = 0;

    while($i < 10){

        $i++;
        $ip = rand(0,255) . ".".rand(0,255) . ".".rand(0,255) . ".".rand(0,255);
        $whitelite = rand(0,1);

        print '  <tr '.$bc[$var].'>';
        print '    <td width="60%">'.$ip.'</td>';
        if($whitelite) // TODO modifier ondition
            print '    <td>'.img_picto("ok", "tick").$langs->trans("whitelist"). '</td>';
        else
            print '    <td>'.img_picto("ko", "delete").$langs->trans("blacklist"). '</td>';
        print '  </tr>';
        $var=!$var;
    }
    print '</table>';
}

// Tab EditConf
if($o==1){
    $ipList = $htaccessprotectip->fetchAll();
    print '<form id="ip_create" action="" method="POST">';
    print '<input style="display: none;" name="action" value="create"/>';
    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("name").'</td>';
    print '    <td>'.$langs->trans("IP").'</td>';
    print '    <td>'.$langs->trans("whitelist").'</td>';
    print '    <td>&nbsp;</td>';
    //print '    <td>'.img_picto("ok", "tick").'</td>';
    print '  </tr>';
    $var = true;

    foreach($ipList as $ip) {
        $list = ($ip->trusted)?img_picto("ok", "tick").$langs->trans("whitelist"):img_picto("ko", "delete").$langs->trans("blacklist");

        print '  <tr '.$bc[$var].'>';
        print '    <td width="60%">' . $ip->name . '</td>';
        print '    <td>' . $ip->ip . '</td>';
        print '    <td>' . $list . '</td>';
        print '    <td>';
        print '      <a href="?o=1&action=delete&id=' . $ip->id . '" class="ip_delete">'.img_picto($langs->trans("delete"), "delete").'</a>';
        print '    </td>';
        print '  </tr>';
        $var=!$var;
    }

    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">';
    print '      <input class="flat" id="name" name="name" placeholder="' . $langs->trans("name") . '"/>';
    print '    </td>';
    print '    <td>';
    print '      <input class="flat" id="ip" name="ip" placeholder="' . $langs->trans("IP") . '"/>';
    print '    </td>';
    print '    <td>';
    print '      <input type="checkbox" class="flat" name="trusted" checked="checked"/>';
    print '    </td>';
    print '    <td>';
    print '      <input type="submit" class="flat" value="' . $langs->trans("add") . '"/>';
    print '    </td>';
    print '  </tr>';

    print '</table>';
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

                /*jQuery(".ip_delete").click(function() {
                    jQuery.ajax({
                        type: "POST",
                        url: "/htaccessProtect/admin/htaccessProtect_api.php",
                        data: {action: "delete", id: jQuery(this).attr("data-id")}
                    }).done(function(data) {
                        console.log(data);
                    });

                    return false;
                });*/
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
    // TODO recup vraie info
    print 'Order deny,allow
Allow from 82.127.50.242
Deny from 109.190.14.152
Deny from 109.190.151.33
Deny from 109.190.36.128

AuthType Basic
AuthName "Restricted Area"
AuthUserFile .htpasswd
Require valid-user

Deny from all
Satisfy any';

    print '    </code></pre></td>';
    print '  </tr>';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("ContenuHtpassword").'</td>';
    print '  </tr>';
    print '  <tr>';
    print '    <td><pre style="padding: 5px"><code>';
    // TODO recup vraie info
    print 'test:$apr1$cn.tFYWL$tqaKkRGhdA2YmOW07Zfx4/';

    print '    </code></pre></td>';
    print '  </tr>';
    print '</table>';

}
dol_fiche_end();
// End of page
llxFooter();
$db->close();
