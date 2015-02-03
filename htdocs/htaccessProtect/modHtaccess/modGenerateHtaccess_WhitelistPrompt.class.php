<?php

require_once DOL_DOCUMENT_ROOT .'/htaccessProtect/modHtaccess/module_htaccessgenerator.php';

class modGenerateHtaccess_WhitelistPrompt extends modGenerateHtaccess
{
    function __construct($bddips, $accountList, $langs)
    {
        parent::__construct($bddips, $accountList, $langs);
        $this->name = preg_split("/_/", get_class($this))[1];
        $this->desc = $this->langs->trans("WhitelistPromptDesc");
        //TODO traduire toute le module.
    }

    /**
     *  Generate htaccess file content
     *
     *  @return string
     */
    function GenerateFileContent(){
        $file = "";
        $file .= "Order Deny,Allow \n";
        $file .= "Deny from all \n\n";
        foreach( $this->$ipblack as $ipb) {
            $file .= "Allow from " . $ipb->ip . "\n";
        }
        $file .= "\n";
        $file .= "<IfModule mod_rewrite.c> \n";
        $file .= "	RewriteEngine On \n";
        $file .= "  AuthType Basic \n";
        $file .= "	AuthName \"restricted area\" \n";
        $file .= "	AuthUserFile /var/www/develop/htdocs/.htpasswd \n";
        $file .= "	require valid-user \n";
        $file .= "</IfModule> \n";
        $file .= "Satisfy any";
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

        if(count($this->ipwhite) && !count($this->ipblack) && count($this->accountList)){
            $return[0] = 1;
            $return[1] = "";
            return $return;
        }

        else if(count($this->ipblack)){
            $return[0] = 3;
            $return[1] = "Les ips blacklist ne seront pas pris en compte";
            return $return;
        }

        else if(count($this->ipwhite)){
            $return[0] = 2;
            $return[1] = "Il n'y a pas d'ip Blacklister ";
            return $return;
        }

        else{
            $return[0] = 3;
            $return[1] = "Erreur. ";
            return $return;
        }
    }
}