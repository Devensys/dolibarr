<?php
/* Copyright (C) 2014-2015  Teddy Andreotti <125155@supinfo.com>
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

require_once DOL_DOCUMENT_ROOT .'/htaccessProtect/modHtaccess/module_htaccessgenerator.php';

class modGenerateHtaccess_BlacklistPrompt extends modGenerateHtaccess
{
    function __construct($bddips, $accountList, $langs)
    {
        parent::__construct($bddips, $accountList, $langs);
        $this->name = preg_split("/_/", get_class($this))[1];
        $this->desc = $this->langs->trans("BlacklistPromptDesc");
    }

    /**
     *  Generate htaccess file content
     *
     *  @return string
     */
    function GenerateFileContent(){
        $file = "";
        $file .= "Order Allow,Deny \n";
        $file .= "Allow from all \n";
        if(count($this->ipblack)) {
            $file .= "\n";
            foreach ($this->ipblack as $ipb) {
                $file .= "Deny from " . $ipb->ip . "\n";
            }
        }
        $file .= "\n";
        $file .= "<IfModule mod_rewrite.c> \n";
        $file .= "	RewriteEngine On \n";
        $file .= "	AuthType Basic \n";
        $file .= "	AuthName \"restricted area\" \n";
        $file .= "	AuthUserFile ".DOL_DOCUMENT_ROOT."/.htpasswd \n";
        $file .= "	require valid-user \n";
        $file .= "</IfModule> \n";
        $file .= "\n";
        $file .= "Satisfy all";
        return $file;
    }

    /**
     *  Generate htaccess file content
     *
     *  @return Array()
     *          [0] => 1 :ok , 2 : attention , 3 : error
     *          [1] => message display
     */
    function Info(){
        $return = Array();

        if(!count($this->ipwhite) && count($this->ipblack) && count($this->accountList)){
            $return[0] = 1;
            $return[1] = $this->langs->trans("ConfigurationOk");
            return $return;
        }

        else if(!count($this->accountList)){
            $return[0] = 3;
            $return[1] = $this->langs->trans("ConfigurationUserNeeded");
            return $return;
        }

        else if(count($this->ipwhite)){
            $return[0] = 2;
            $return[1] = $this->langs->trans("ConfigurationWhiteNotSupported");
            return $return;
        }

        else{
            $return[0] = 3;
            $return[1] = $this->langs->trans("Error");
            return $return;
        }
    }
}