<?php

abstract class modGenerateHtaccess
{
    var $ipblack;
    var $ipwhite;
    var $accountList;

    var $langs;


    var $name = "generique";
    var $desc = "desc generic";

    function __construct($bddips, $accountList, $langs)
    {
        $this->ipblack = $bddips->fetchAllBlack();
        $this->ipwhite = $bddips->fetchAllWhite();
        $this->accountList = $accountList->fetchAll();
        $this->langs = $langs;
    }

    function getEtat(){
        $info = $this->Info();
        $return = "";

        switch($info[0]){
            case 1 :
                $return .= img_picto("", "statut4");
                break;
            case 2 :
                $return .= img_picto("", "statut1");
                break;
            case 3 :
                $return .= img_picto("", "statut8");
                break;
            default:
                $return .= img_picto("", "statut5");
        }

        return  $return . $info[1];
    }

    function getMD5(){
        return md5($this->GenerateFileContent());
    }

    /**
     *  Generate htaccess file content
     *
     *  @return string
     */
    function GenerateFileContent(){
        return "";
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


        return $return;
    }

}