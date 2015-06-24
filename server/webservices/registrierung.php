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

    $oUser = new User($oDatabase);
    
    switch($_POST['action']){
        
        case 'register':
            $aResult = $oUser->RegisterUser($_POST);
            break;
        
        case 'active':
            $aResult = $oUser->ActivateAccount($_POST['hash']);
            break;
        
        case 'login':
            $aResult = $oUser->LoginUser($_POST);
            break;
        
        case 'newpw':
            $aResult = $oUser->SetNewPassword($_POST);
            break;
        
        default:
            $aResult = array(
                'success' => 'false',
                'message' => 'Sie haben was verbotenes probiert'
            );
            break;
    }
    
    echo json_encode($aResult);