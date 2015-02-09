<?php

require_once DOL_DOCUMENT_ROOT .'/htaccessProtect/modHtaccess/module_htaccessgenerator.php';

class modGenerateHtaccess_AllowWhiteBlackPrompt extends modGenerateHtaccess
{
    function __construct($bddips, $accountList, $langs)
    {
        parent::__construct($bddips, $accountList, $langs);
        $this->name = preg_split("/_/", get_class($this))[1];
        $this->desc = $this->langs->trans("AllowWhiteBlackPromptDesc");
        //TODO traduire toute le module.
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
        $file .= "	<If \"";
        if(count($this->ipwhite)) {
            foreach ($this->ipwhite as $ipw) {
                $file .= "%{REMOTE_ADDR} != '" . $ipw->ip . "' && ";
            }
        }
        $file = substr($file, 0 , -4);
        $file .= "\"> \n";
        $file .= "		AuthType Basic \n";
        $file .= "		AuthName \"restricted area\" \n";
        $file .= "		AuthUserFile ".DOL_DOCUMENT_ROOT."/.htpasswd \n";
        $file .= "		require valid-user \n";
        $file .= "	</If> \n";
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

        if(count($this->ipwhite) && count($this->ipblack) && count($this->accountList)){
            $return[0] = 1;
            $return[1] = "";
            return $return;
        }

        else if(count($this->ipwhite) && count($this->ipblack)){
            $return[0] = 3;
            $return[1] = "Il faut au moins un comptes utilisateur";
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