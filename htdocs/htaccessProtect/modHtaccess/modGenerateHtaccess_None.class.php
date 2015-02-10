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

class modGenerateHtaccess_None extends modGenerateHtaccess
{
    function __construct($bddips, $accountList, $langs)
    {
        parent::__construct($bddips, $accountList, $langs);
        $this->name = preg_split("/_/", get_class($this))[1];
        $this->desc = $this->langs->trans("NoneDesc");
    }

    /**
     *  Generate htaccess file content
     *
     *  @return string
     */
    function GenerateFileContent(){
        return '';
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
        $return[0] = 1;
        $return[1] = $this->langs->trans("ConfigurationOk");
        return $return;
    }
}