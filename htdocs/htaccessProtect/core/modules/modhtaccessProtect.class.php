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

/**
 * 	\defgroup   htaccessProtect     Module MyModule
 *  \brief      Example of a module descriptor.
 *  \file       htdocs/htaccessProtect/core/modules/modhtaccessProtect.class.php
 *  \ingroup    htaccessProtect
 *  \brief      Description and activation file for module MyModule
 */
include_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

class modhtaccessProtect extends DolibarrModules{

    /**
     *   Constructor. Define names, constants, directories, boxes, permissions
     *
     *   @param      DoliDB		$db      Database handler
     */
    function __construct($db) {
        global $langs, $conf;

        $langs->load("htaccessProtect@htaccessProtect");

        $this->db = $db;

        $this->numero = 260026;
        $this->rights_class = 'htaccessProtect';
        $this->family = "Devensys Secure Suite";
        $this->name = "Protection Htaccess/Htpasswd";
        $this->description = $langs->trans("DescModule");
        $this->version = '1.0.1';
        $this->const_name = 'MAIN_MODULE_'.strtoupper(preg_replace('/^mod/i','',get_class($this)));
        $this->special = 2;
        $this->picto = 'lock@htaccessProtect';

        // Defined all module parts (triggers, login, substitutions, menus, css, etc...)
        // for default path (eg: /doliwaste/core/xxxxx) (0=disable, 1=enable)
        // for specific path of parts (eg: /doliwaste/core/modules/barcode)
        // for specific css file (eg: /doliwaste/css/doliwaste.css.php)
        //$this->module_parts = array(
        //                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
        //							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
        //							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
        //							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
        //							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
        //                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
        //							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
        //							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
        //							'css' => array('/doliwaste/css/doliwaste.css.php'),	// Set this to relative path of css file if module has its own css file
        //							'js' => array('/doliwaste/js/doliwaste.js'),          // Set this to relative path of js file if module must load a js on all pages
        //							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
        //							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
        //							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@doliwaste')) // Set here all workflow context managed by module
        //                        );

        // Dependencies
        $this->hidden = false;   // A condition to hide module
        $this->depends = array();  // List of modules id that must be enabled if this module is enabled
        $this->requiredby = array(); // List of modules id to disable if this one is disabled
        $this->conflictwith = array(); // List of modules id this module is in conflict with
        $this->phpmin = array(5, 0);     // Minimum version of PHP required by module
        $this->need_dolibarr_version = array(3, 6); // Minimum version of Dolibarr required by module
        $this->langfiles = array("mylangfile@htaccessProtect");


        // Dictionnaries
        /*
        if (!isset($conf->doliwaste->enabled)) {
            $conf->doliwaste = new stdClass();
            $conf->doliwaste->enabled = 0;
        }
        $this->dictionnaries = array();



        //Example:
        if (! isset($conf->doliwaste->enabled)) $conf->doliwaste->enabled=0;	// This is to avoid warnings
        $this->dictionnaries=array(
            'langs'=>'doliwaste@doliwaste',
            'tabname'=>array(MAIN_DB_PREFIX."doliwaste_wastetipe",MAIN_DB_PREFIX."doliwaste_paymenttipe"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Repercusión","Tipo de pago"),													// Label of tables
            'tabsql'=>array('SELECT id as rowid, nombre FROM '.MAIN_DB_PREFIX.'doliwaste_paymenttipe','SELECT id as rowid, nombre FROM '.MAIN_DB_PREFIX.'doliwaste_paymenttipe'),	// Request to select fields
            'tabsqlsort'=>array("nombre ASC","nombre ASC"),																					// Sort order
            'tabfield'=>array("nombre","nombre"),																					// List of fields (result of select to show dictionnary)
            'tabfieldvalue'=>array("nombre","nombre"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("nombre","nombre"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("id","id"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->doliwaste->enabled,$conf->doliwaste->enabled)												// Condition to show each dictionnary
        );

          */


        // Permissions
        $this->rights = array();  // Permission array used by this module
        $r = 0;
        //Perms
        $this->rights[$r][0] = 260027;
        $this->rights[$r][1] = 'Modifier  htaccessProtect';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'canmodif';




        $this->config_page_url = array('htaccessProtect_setupapage.php@htaccessProtect');
    }

    /**
     * 		Function called when module is enabled.
     * 		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     * 		It also creates data directories
     *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
     *      @return     int             	1 if OK, 0 if KO
     */
    function init($options = '') {
        global $langs, $conf;
        $sql = array();
        require_once DOL_DOCUMENT_ROOT . "/htaccessProtect/htaccessprotectip.class.php";
        require_once DOL_DOCUMENT_ROOT . "/htaccessProtect/htaccessprotectaccount.class.php";

        if (empty($conf->global->MAIN_MODULE_HTACCESSPROTECT_MODGENERATE)) {
            dolibarr_set_const($this->db, "MAIN_MODULE_HTACCESSPROTECT_MODGENERATE", 'None','chaine',0,'',$conf->entity);
        }

        // Configuration files creation
        $classname = 'modGenerateHtaccess_'.$conf->global->MAIN_MODULE_HTACCESSPROTECT_MODGENERATE ;
        $dir = DOL_DOCUMENT_ROOT . "/htaccessProtect/modHtaccess/";
        require_once $dir.$classname.'.class.php';
        $htaccessprotectip = new Htaccessprotectip($this->db);
        $htaccessprotectaccount = new Htaccessprotectaccount($this->db);
        $obj = new $classname($htaccessprotectip, $htaccessprotectaccount, $langs);
        file_put_contents(DOL_DOCUMENT_ROOT."/.htaccess", $obj->GenerateFileContent());
        file_put_contents(DOL_DOCUMENT_ROOT."/.htpasswd", $htaccessprotectaccount->GenerateFileContent());

        $result = $this->_load_tables('/htaccessProtect/sql/');
        return $this->_init($sql, $options);
    }

    /**
     * 		Function called when module is disabled.
     *      Remove from database constants, boxes and permissions from Dolibarr database.
     * 		Data directories are not deleted
     *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
     *      @return     int             	1 if OK, 0 if KO
     */
    function remove($options = '') {
        // Configuration files delete
        unlink(DOL_DOCUMENT_ROOT."/.htaccess");
        unlink(DOL_DOCUMENT_ROOT."/.htpasswd");
        $sql = array();
        return $this->_remove($sql, $options);
    }

}
