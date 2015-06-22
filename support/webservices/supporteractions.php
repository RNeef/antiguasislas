<?php
    spl_autoload_register('MyLoader');
    
    function MyLoader($sClass){

        if(strstr($sClass, 'Sql')){

            require_once '../classes/databases/'. $sClass .'.php';

        }else{

            require_once '../classes/'. $sClass .'.php';

        }
    }

    $oDatabase_connection = new Database();

    $oDatabase = $oDatabase_connection->Connect();

    switch($_POST['action']){
        
        case 'getRights':
            $oSupporter = new Supporter();
            $aResultAssoc = $oSupporter->GetRightsList($oDatabase);
            break;
        case 'saveSupporter':
            $aResultAssoc = SaveSupporter($oSupporter, $oDatabase);
            break;
        case 'table':
            $oTable = new Table();
            $aResultAssoc = $oTable->GetTableValues($_POST['table'], $oDatabase);
            break;
        case 'save':
            $oTable = new Table();
            $aResultAssoc = $oTable->SaveTableRow($_POST, $oDatabase);
            break;
    }
    
    echo json_encode($aResultAssoc);
    
    /**
     * Funktion überprüft ob alle Felder ausgefüllt sind und überprüft die 
     * Mail-Adresse auf Gültigkeit
     * 
     * @author Rolf Neef
     * @param object $oSupporter
     * @param object $oDatabase
     * @return array $aResultAssoc
     */
    function SaveSupporter($oSupporter, $oDatabase){
        $oHelper = new Helper();
        $aFilterAssoc = array(
            'name'   => FILTER_SANITIZE_ENCODED,
            'action' => FILTER_SANITIZE_ENCODED,
            'password' => FILTER_SANITIZE_ENCODED,
            'mail' => FILTER_SANITIZE_EMAIL,
            'right' => FILTER_SANITIZE_ENCODED
        );
        $aUserInputsAssoc = filter_input_array(INPUT_POST, $aFilterAssoc);
        $aEmptyResult = $oHelper->CheckEmptyInputs($aUserInputsAssoc);
        $aValidateMail = $oHelper->CheckValidateMail($aUserInputsAssoc['mail']);
        
        if($aEmptyResult['success'] === 'false'){

            $aResultAssoc = $aEmptyResult;
            
        }elseif($aValidateMail['success'] === 'false'){
            
            $aResultAssoc = $aValidateMail;
            
        }else{

            $aResultAssoc = $oSupporter->SaveNewSupporter($aUserInputsAssoc, $oDatabase);
        }
        
        return $aResultAssoc;
    }