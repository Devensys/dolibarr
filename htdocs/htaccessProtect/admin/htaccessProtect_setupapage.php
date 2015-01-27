<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

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
    print '<form action="">';
    print '<table class="noborder" width="100%">';
    print '  <tr class="liste_titre">';
    print '    <td>'.$langs->trans("name").'</td>';
    print '    <td>'.$langs->trans("IP").'</td>';
    print '    <td>'.$langs->trans("whitelist").'</td>';
    print '    <td>&nbsp;</td>';
    //print '    <td>'.img_picto("ok", "tick").'</td>';
    print '  </tr>';
    $var = true;


    // TODO faire vraie requette
    $i = 0;

    while($i < 10){

        $i++;
        $ip = rand(0,255) . ".".rand(0,255) . ".".rand(0,255) . ".".rand(0,255);
        $whitelite = rand(0,1);

        print '  <tr '.$bc[$var].'>';
        print '    <td width="60%">Un nom</td>';
        print '    <td>'.$ip.'</td>';
        if($whitelite) // TODO modifier ondition
            print '    <td>'.img_picto("ok", "tick").$langs->trans("whitelist"). '</td>';
        else
            print '    <td>'.img_picto("ko", "delete").$langs->trans("blacklist"). '</td>';
        print '    <td>';
        print '      <a href="" class="ip_delete">'.img_picto($langs->trans("delete"), "delete").'</a>';
        print '    </td>';
        print '  </tr>';
        $var=!$var;
    }

    print '  <tr '.$bc[$var].'>';
    print '    <td width="60%">';
    print '      <input class="form-control" id="name" placeholder="' . $langs->trans("name") . '"/>';
    print '    </td>';
    print '    <td>';
    print '      <input class="form-control" id="ip" placeholder="' . $langs->trans("IP") . '"/>';
    print '    </td>';
    print '    <td>';
    print '      <input type="checkbox" class="form-control" id="whitelist" checked="checked"/>';
    print '    </td>';
    print '    <td>';
    print '      <a href="" id="ip_create">'.img_picto($langs->trans("add"), "edit_add").'</a>';
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
                jQuery("#ip_create").click(function(e) {
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
                        jQuery.ajax({
                            type: "POST",
                            url: "/htaccessProtect/admin/htaccessProtect_api.php",
                            data: {action: "create", name: jQuery("#name").val(), ip: jQuery("#ip").val(), trusted: (jQuery("#whitelist").prop("checked"))?1:0}
                        }).done(function(data) {
                            console.log(data);
                            //TODO: ajouter la ligne du nouvel élément dans le tableau
                        });
                    }
                });

                jQuery(".ip_delete").click(function() {
                    jQuery.ajax({
                        type: "POST",
                        url: "/htaccessProtect/admin/htaccessProtect_api.php",
                        data: {action: "delete", id: jQuery(this).attr("data-id")}
                    }).done(function(data) {
                        console.log(data);
                    });

                    return false;
                });
            });
            </script>';
}

// Tab HtaccessContent
if($o==2){


}
dol_fiche_end();
/*
// Part to create
if ($action == 'create')
{


    dol_fiche_head();

    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

    print '<table class="border centpercent">'."\n";
    print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>';
    print '<input class="flat" type="text" size="36" name="label" value="'.$label.'">';
    print '</td></tr>';

    print '</table>'."\n";

    print '<br>';

    print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

    print '</form>';

    dol_fiche_end();
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
    dol_fiche_head();

    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<input type="hidden" name="id" value="'.$object->id.'">';


    print '<br>';

    print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"></div>';

    print '</form>';

    dol_fiche_end();
}



// Part to show record
if ($id && (empty($action) || $action == 'view'))
{
    dol_fiche_head();



    dol_fiche_end();


    // Buttons
    print '<div class="tabsAction">'."\n";
    $parameters=array();
    $reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
    if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

    if (empty($reshook))
    {
        if ($user->rights->mymodule->write)
        {
            print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
        }

        if ($user->rights->mymodule->delete)
        {
            if ($conf->use_javascript_ajax && empty($conf->dol_use_jmobile))	// We can't use preloaded confirm form with jmobile
            {
                print '<div class="inline-block divButAction"><span id="action-delete" class="butActionDelete">'.$langs->trans('Delete').'</span></div>'."\n";
            }
            else
            {
                print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
            }
        }
    }
    print '</div>'."\n";


}
*/

// End of page
llxFooter();
$db->close();
