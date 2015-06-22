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

    $oSupporter = new Supporter();
    $bError = false; // Wenn false dann Fehlerfrei
    $sMessage = "";

    $aFilterAssoc = array(
        'name'   => FILTER_SANITIZE_ENCODED,
        'action' => FILTER_SANITIZE_ENCODED,
        'password' => FILTER_SANITIZE_ENCODED

    );

    $aUserInputsAssoc = filter_input_array(INPUT_POST, $aFilterAssoc);

    if($aUserInputsAssoc['action'] === 'register'){

        $aResultAssoc = $oSupporter->Login($aUserInputsAssoc, $oDatabase);

        echo json_encode($aResultAssoc);
    }

?>