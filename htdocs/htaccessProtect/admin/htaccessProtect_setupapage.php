<?php
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
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

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

$dir = DOL_DOCUMENT_ROOT . "/htaccessProtect/modHtaccess/";


/*******************************************************************
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 ********************************************************************/

// Start of transaction

$htaccessprotectip = new Htaccessprotectip($db);
$htaccessprotectaccount = new Htaccessprotectaccount($db);

if (GETPOST('action')) {
    if (GETPOST('entity')) {
        switch(GETPOST('action')) {
            case 'create':
                $htaccessprotectaccount->pseudo = GETPOST('pseudo');
                $htaccessprotectaccount->passwd = crypt(GETPOST('passwd'));
                $id = $htaccessprotectaccount->create($user);
                break;

            case 'delete':
                $htaccessprotectaccount->fetch(GETPOST('id'));
                $result = $htaccessprotectaccount->delete($user);
                break;
        }
    } else {
        switch(GETPOST('action')) {
            case 'create':
                $htaccessprotectip->name = GETPOST('name');
                $htaccessprotectip->ip = GETPOST('ip');
                $htaccessprotectip->trusted = (GETPOST('trusted') == 'on');
                $id = $htaccessprotectip->create($user);
                break;

            case 'delete':
                $htaccessprotectip->fetch(GETPOST('id'));
                $result = $htaccessprotectip->delete($user);
                break;

            case 'change':
                while(!dolibarr_set_const($db, "MAIN_MODULE_HTACCESSPROTECT_MODGENERATE", GETPOST("name"))); // RAGE MODE //TODO a modifier lol ^^
                break;

            case 'sync':
                if (GETPOST('file')) {
                    if (GETPOST('file') == "htaccess") {
                        $classname = 'modGenerateHtaccess_'.$conf->global->MAIN_MODULE_HTACCESSPROTECT_MODGENERATE ;
                        require_once $dir.'/'.$classname.'.class.php';
                        $obj = new $classname($htaccessprotectip, $htaccessprotectaccount, $langs);
                        file_put_contents(DOL_DOCUMENT_ROOT."/.htaccess", $obj->GenerateFileContent());
                    } elseif (GETPOST('file') == "htpasswd") {
                        file_put_contents(DOL_DOCUMENT_ROOT."/.htpasswd", $htaccessprotectaccount->GenerateFileContent());
                    }
                }
                break;
        }
    }
}

$ipList = $htaccessprotectip->fetchAll();
$accountList = $htaccessprotectaccount->fetchAll();

switch ($o) {
    case 0:
        $fe_htaccess = file_exists(DOL_DOCUMENT_ROOT."/.htaccess");
        $fe_htpasswd = file_exists(DOL_DOCUMENT_ROOT."/.htpasswd");
        $right = substr(sprintf('%o',fileperms(DOL_DOCUMENT_ROOT)), -3);
        $verapache = apache_get_version();

        // Création du HTML pour les droits
        if($right == 775){
            $rightHTML = img_picto($langs->trans("Ok"), "statut4");
        } else if ($right == 777) {
            $infoAdmin = '<b>' . $langs->trans("TakeCare") . '</b><br/>- ' . $langs->trans("HighRootFolderRights") . '<br/>- ' . $langs->trans("SetRootFolderRights") . ' ' . $langs->trans("RightsBecauseHigh");
            $rightHTML = img_picto($langs->trans("ModificationNeeded"), "statut7");
        } else {
            $infoAdmin = '<b>' . $langs->trans("TakeCare") . '</b><br/>- ' . $langs->trans("LowRootFolderRights") . '<br/>- ' . $langs->trans("SetRootFolderRights") . ' ' . $langs->trans("RightsBecauseLow");
            $rightHTML = img_picto($langs->trans("Error"), "statut8");
        }

        // Création du HTML pour la version du serveur
        if (strpos($verapache, '2.4') === false) {
            $infoAdmin .= '<br/>- ' . $langs->trans("BadApacheVersion");
            $serverHTML = img_picto($langs->trans("Error"), "statut8");
        } else {
            $serverHTML = img_picto($langs->trans("Ok"), "statut4");
        }

        // Création du HTML pour le fichier .htaccess
        if($fe_htaccess){
            $classname = 'modGenerateHtaccess_'.$conf->global->MAIN_MODULE_HTACCESSPROTECT_MODGENERATE ;
            require_once $dir.'/'.$classname.'.class.php';
            $obj = new $classname($htaccessprotectip, $htaccessprotectaccount, $langs);

            if($obj->getMD5() == md5_file(DOL_DOCUMENT_ROOT."/.htaccess")){
                $htaccessHTML = img_picto($langs->trans("Ok"), "statut4") . $langs->trans("FileOk");
            } else {
                $htaccessHTML = img_picto($langs->trans("ModificationNeeded"), "statut7") . $langs->trans("FileKo");
            }
        }else {
            $htaccessHTML = img_picto($langs->trans("Error"), "statut8") . $langs->trans("MissingFile");
        }

        // Création du HTML pour le fichier .htpasswd
        if($fe_htpasswd){
            if($htaccessprotectaccount->getMD5() == md5_file(DOL_DOCUMENT_ROOT."/.htpasswd")){
                $htpasswdHTML = img_picto($langs->trans("Ok"), "statut4") . $langs->trans("FileOk");
            } else{
                $htpasswdHTML = img_picto($langs->trans("ModificationNeeded"), "statut7") . $langs->trans("FileKo");
            }
        }else {
            $htpasswdHTML = img_picto($langs->trans("Error"), "statut8") . $langs->trans("MissingFile");
        }

        $rightHTML .= ' ' . $right;
        $serverHTML .= ' ' . $verapache;

        break;

    case 1:
        // Charge tableau des modules generation
        clearstatcache();
        $handle=opendir($dir);
        $i=1;
        if (is_resource($handle))
        {
            while (($file = readdir($handle))!==false)
            {
                if (preg_match('/(modGenerateHtaccess_[a-z]+)\.class\.php/i',$file,$reg))
                {
                    // Chargement de la classe
                    $classname = $reg[1];
                    require_once $dir.'/'.$file;
                    $obj = new $classname($htaccessprotectip, $htaccessprotectaccount, $langs);
                    $arrayhandler[$obj->name]=$obj;
                    $i++;
                }
            }
            closedir($handle);
        }
        break;

    case 2:
        $fe_htaccess = file_exists(DOL_DOCUMENT_ROOT."/.htaccess");
        $fe_htpasswd = file_exists(DOL_DOCUMENT_ROOT."/.htpasswd");
        break;
}




/***************************************************
 * VIEW
 *
 * Put here all code to build page
 ****************************************************/

llxHeader('',$langs->trans('Title'),'');

//Adding jquery code
/*éé
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {

});
</script>';
*/

print_fiche_titre($langs->trans('Title'));

dol_fiche_head(array(array("?o=0", $langs->trans("GeneralInfo"), "ActiveConf"),
                     array("?o=1", $langs->trans("Configuration"), "ModConf"),
                     array("?o=2", $langs->trans("FileContent"), "AffFiles")), $o);

// Tab confActive
if($o==0){
    if (isset($infoAdmin)) {
        print info_admin($infoAdmin);
    }

    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("GeneralInfo").'</td>';
    print '    <td>&nbsp;</td>';
    print '  </tr>';
    $var = true;

    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("DirectoryRight").'</td>';
    print '    <td>' . $rightHTML. '</td>';
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("ServerVersion").'</td>';
    print '    <td>' . $serverHTML . '</td>';
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("HtaccessFileExist").'</td>';
    print '    <td>' . $htaccessHTML . '</td>';
    print '  </tr>';

    $var=!$var;
    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">'.$langs->trans("HtpasswdFileExist").'</td>';
    print '    <td>' . $htpasswdHTML . '</td>';
    print '  </tr>';
    print '</table>';
}

// Tab EditConf
if($o==1){
    print '  <table class="noborder" width="100%">';
    print '    <tr class="liste_titre">';
    print '      <td>'.$langs->trans("Name").'</td>';
    print '      <td>'.$langs->trans("Description").'</td>';
    print '      <td>'.$langs->trans("Etat").'</td>';
    print '      <td style="text-align: center;">'.$langs->trans("Action").'</td>';
    print '    </tr>';
    $var = true;

    foreach($arrayhandler as $module){
        print '    <tr '.$bc[$var].'>';
        print '      <td>'.$module->name.'</td>';
        print '      <td>'.$module->desc.'</td>';
        print '      <td>'.$module->getEtat().'</td>';
        if($conf->global->MAIN_MODULE_HTACCESSPROTECT_MODGENERATE == $module->name){
            print '      <td style="text-align: center;">'.img_picto('', "tick").'</td>';
        }else{
            print '      <td style="text-align: center; font-weight: bold;"><a href="htaccessProtect_setupapage.php?o=1&action=change&name='.$module->name.'">'.$langs->trans("Activate").'</a></td>';
        }
        print '    </tr>';
        $var = !$var;
    }

    print '  </table>';

    // IP Table
    print '<form id="ip_create" action="htaccessProtect_setupapage.php?o=1" method="POST">';
    print '  <input style="display: none;" name="action" value="create"/>';
    print '  <table class="noborder" width="100%">';
    print '    <tr class="liste_titre">';
    print '      <td>'.$langs->trans("Name").'</td>';
    print '      <td>'.$langs->trans("Ip").'</td>';
    print '      <td style="text-align: center;">'.$langs->trans("Whitelist").'</td>';
    print '      <td style="text-align: center;">'.$langs->trans("Action").'</td>';
    print '    </tr>';
    $var = true;

    if(count($ipList)){
        foreach($ipList as $ip) {
            print '    <tr '.$bc[$var].'>';
            print '      <td width="60%">' . $ip->name . '</td>';
            print '      <td>' . $ip->ip . '</td>';
            print '      <td style="text-align: center;">' . (($ip->trusted)?img_picto($langs->trans("Whitelist"), "tick"):img_picto($langs->trans("Blacklist"), "delete")) . '</td>';
            print '      <td style="text-align: center;">';
            print '        <a href="htaccessProtect_setupapage.php?o=1&action=delete&id=' . $ip->id . '" class="ip_delete">'.img_picto($langs->trans("Delete"), "delete").'</a>';
            print '      </td>';
            print '    </tr>';
            $var=!$var;
        }
    } else {
        print '    <tr '.$bc[$var].' style="color:grey; font-style: italic;">';
        print '      <td colspan="4" style="text-align: center;">' . $langs->trans("NoIp") . '</td>';
        print '    </tr>';
        $var=!$var;
    }

    print '    <tr '.$bc[$var].'>';
    print '      <td width="60%">';
    print '        <input class="flat" id="name" name="name" placeholder="' . $langs->trans("Name") . '"/>';
    print '      </td>';
    print '      <td>';
    print '        <input class="flat" id="ip" name="ip" placeholder="' . $langs->trans("Ip") . '"/>';
    print '      </td>';
    print '      <td style="text-align: center;">';
    print '        <input type="checkbox" class="flat" name="trusted" checked="checked"/>';
    print '      </td>';
    print '      <td style="text-align: center;">';
    print '        <input type="submit" class="flat" value="' . $langs->trans("Add") . '" style="box-shadow:none;"/>';
    print '      </td>';
    print '    </tr>';

    print '  </table>';
    print '</form>';


    // Account Table
    print '<form action="htaccessProtect_setupapage.php?o=1" method="POST">';
    print '  <input style="display: none;" name="action" value="create"/>';
    print '  <input style="display: none;" name="entity" value="account"/>';
    print '  <table class="noborder" width="100%">';
    print '    <tr class="liste_titre">';
    print '      <td>'.$langs->trans("Pseudo").'</td>';
    print '      <td>'.$langs->trans("Password").'</td>';
    print '      <td style="text-align: center;">'.$langs->trans("Action").'</td>';
    print '    </tr>';
    $var = true;

    if (count($accountList)) {
        foreach($accountList as $account) {
            print '    <tr '.$bc[$var].'>';
            print '      <td width="60%">' . $account->pseudo . '</td>';
            print '      <td>' . $account->passwd . '</td>';
            print '      <td style="text-align: center;">';
            print '        <a href="htaccessProtect_setupapage.php?o=1&action=delete&entity=account&id=' . $account->id . '">'.img_picto($langs->trans("Delete"), "delete").'</a>';
            print '      </td>';
            print '    </tr>';
            $var=!$var;
        }
    } else {
        print '    <tr '.$bc[$var].' style="color:grey; font-style: italic;">';
        print '      <td colspan="4" style="text-align: center;">' . $langs->trans("NoAccount") . '</td>';
        print '    </tr>';
        $var=!$var;
    }

    print '    <tr '.$bc[$var].'>';
    print '      <td width="60%">';
    print '        <input class="flat" name="pseudo" placeholder="' . $langs->trans("Pseudo") . '"/>';
    print '      </td>';
    print '      <td>';
    print '        <input class="flat" name="passwd" placeholder="' . $langs->trans("Password") . '"/>';
    print '      </td>';
    print '      <td style="text-align: center;">';
    print '        <input type="submit" class="flat" value="' . $langs->trans("Add") . '"/>';
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

    $classname = 'modGenerateHtaccess_'.$conf->global->MAIN_MODULE_HTACCESSPROTECT_MODGENERATE ;
    require_once $dir.'/'.$classname.'.class.php';
    $obj = new $classname($htaccessprotectip, $htaccessprotectaccount, $langs);

    // tableau aff conf generer
    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("GenerationHtaccess").'</td>';
    print '  </tr>';
    print '  <tr>';
    print '    <td><pre style="padding: 5px"><code>';
    print        htmlentities($obj->GenerateFileContent());
    print '    </code></pre></td>';
    print '  </tr>';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("GenerationHtpasswd").'</td>';
    print '  </tr>';
    print '  <tr>';
    print '    <td><pre style="padding: 5px"><code>';
    print        htmlentities($htaccessprotectaccount->GenerateFileContent());
    print '    </code></pre></td>';
    print '  </tr>';
    print '</table>';

    // tableau aff actuel
    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("ContenuHtaccess").'</td>';
    print '    <td align="right" style="padding-right: 5px">';
    if ($fe_htaccess) {
        if ($obj->getMD5() == md5_file(DOL_DOCUMENT_ROOT . "/.htaccess")) {
            print img_picto("", "tick") . $langs->trans("FileOk");
        } else {
            print '<a id="linksynchtaccess" href="htaccessProtect_setupapage.php?o=2&action=sync&file=htaccess">'. img_picto("", "refresh") . $langs->trans("ReplaceFile") . '</a>';
        }
    } else {
        $linksynchtaccess = ($htaccessprotectaccount->GenerateFileContent() != "" && $fe_htpasswd ) ? "linksynchtaccess" : "linksynchtaccessM";
        print '<a id="'.$linksynchtaccess.'" href="htaccessProtect_setupapage.php?o=2&action=sync&file=htaccess">'. img_picto("", "refresh") . $langs->trans("GenerateFile") . '</a>';
    }
    print '    </td>';
    print '  </tr>';
    print '  <tr>';
    print '    <td colspan="2"><pre style="padding: 5px"><code>';
    print $fe_htaccess ? htmlentities(file_get_contents(DOL_DOCUMENT_ROOT . "/.htaccess")) : $langs->trans("MissingFile") ;
    print '    </code></pre></td>';
    print '  </tr>';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("ContenuHtpasswd").'</td>';
    print '    <td align="right" style="padding-right: 5px">';
    if($fe_htpasswd){
        if($htaccessprotectaccount->getMD5() == md5_file(DOL_DOCUMENT_ROOT."/.htpasswd")){
            print img_picto("", "tick") . $langs->trans("FileOk");
        }else{
            print '<a id="linksynchtpasswd" href="htaccessProtect_setupapage.php?o=2&action=sync&file=htpasswd">'. img_picto("", "refresh") . $langs->trans("ReplaceFile") . '</a>';
        }
    }
    else{
        print '<a id="linksynchtpasswd" href="htaccessProtect_setupapage.php?o=2&action=sync&file=htpasswd">'. img_picto("", "refresh") . $langs->trans("GenerateFile") . '</a>';
    }
    print '    </td>';
    print '  </tr>';
    print '  <tr>';
    print '    <td colspan="2"><pre style="padding: 5px"><code>';
    print $fe_htpasswd ? htmlentities(file_get_contents(DOL_DOCUMENT_ROOT."/.htpasswd")) : $langs->trans('MissingFile');
    print '    </code></pre></td>';
    print '  </tr>';
    print '</table>';

    print '<p style="text-align: right"><a id="linkgeneration">';

    print '</a></p>';

    print '<div id="dialog-confirm2" title="Erreur" style="display: none;">';
    print '  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'.$langs->trans('AlertReplace').'</p>';
    print '</div>';

    print '<div id="dialog-confirm3" title="Erreur" style="display: none;">';
    print '  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'.$langs->trans('AlertHtpasswdMissing').'</p>';
    print '</div>';

    print ' <script type="text/javascript" language="javascript">
            jQuery(document).ready(function() {

                jQuery("#linksynchtpasswd, #linksynchtaccess ").click(function(e) {
                    var that = this;
                    e.preventDefault();
                    e.stopPropagation();
                    jQuery("#dialog-confirm2").dialog({
                        resizable: false,
                        modal: true,
                        width : "450px",
                        buttons: {
                            Non : function(){
                                jQuery(this).dialog("close");
                            },
                            Oui: function() {
                                jQuery( this ).dialog( "close" );
                                document.location.href =  jQuery(that).attr("href");
                            }
                        }
                    });
                });

                jQuery("#linksynchtaccessM").click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    jQuery("#dialog-confirm3").dialog({
                        resizable: false,
                        modal: true,
                        width : "600px",
                        buttons: {
                            OK : function(){
                                jQuery(this).dialog("close");
                            }
                        }
                    });

                });
            });
            </script>';
}
dol_fiche_end();
// End of page
llxFooter();
$db->close();
