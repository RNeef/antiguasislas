<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserSql
 *
 * @author Rolf
 */
class UserSql {
    
    private $oDatabase = null;
    
    public function __construct($oDatabase) {
        
        $this->oDatabase = $oDatabase;
        
    }
    
    public function GetAllUser(){
    
        $aResult = array();
        
        $sQuery = '
            SELECT
                `name`,
                `mail`
            FROM
                `users`';
        
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->execute();
		
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] !== '00000'){

            return array(
                'success' => 'false', 
                'message' => 'Es ist ein Interner fehler aufgetreten.'
            );
            
            // Mail versand zu Developer kommt noch.
        }
        
        while($aRow = $rHandler->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            
            $aResult[] = $aRow; 
                    
        }
       
        return array(
          'success' => 'true',
          'datas' => $aResult
        );
        
    }
    
    public function SaveNewUser($aUserDatas){
        
        $sDate = date('Y-m-d H:i:s', time());
        $iLastInsertId = 0;

        $sQuery = '
            INSERT INTO
                `users`
                (
                    `name`,
                    `password`,
                    `mail`,
                    `register_date`,
                    `activhash`
                )VALUES(
                    :name,
                    :password,
                    :mail,
                    :date,
                    :hash
                )';

        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->bindParam(':name', $aUserDatas['name'], PDO::PARAM_STR);
        $rHandler->bindParam(':password', $aUserDatas['password'], PDO::PARAM_STR);
        $rHandler->bindParam(':mail', $aUserDatas['mail'], PDO::PARAM_STR);
        $rHandler->bindParam(':date', $sDate, PDO::PARAM_STR);
        $rHandler->bindParam(':hash', $aUserDatas['hash'], PDO::PARAM_STR);
        $this->oDatabase->beginTransaction();
        try{
            
            $rHandler->execute();
            
            $iLastInsertId = $this->oDatabase->lastInsertId();
            
            $this->oDatabase->commit();

        }catch(Exception $e){
            
            $this->oDatabase->rollback();
            return array(
                'success' => 'false',
                'message' => 'Leider ist ein Fehler aufgetreten, versuche es zu einem späteren Zeizpunkt noch mal.'
            );
        }

        if($iLastInsertId > 0){

            $sQuery = '
                INSERT INTO
                    `user_game_datas`
                    (
                        `userId`
                    )VALUES(
                        :userid
                    )';
            
            $rHandler = $this->oDatabase->prepare($sQuery);
            
            $rHandler->bindParam(':userid', $iLastInsertId, PDO::PARAM_INT);
            
            try{
                
                $this->oDatabase->beginTransaction();
            
                $rHandler->execute();

                $this->oDatabase->commit();
                
            }catch(Exception $ex){
                
                $this->oDatabase->rollback();
                return array(
                    'success' => 'false',
                    'message' => 'Leider ist ein Fehler aufgetreten, versuche es zu einem späteren Zeizpunkt noch mal.'
                );
            }
        }
        
        return array(
            'success' => 'true',
            'message' => 'Sie haben sich erfolgreich registriert, sie haben eine Mail erhalten, '
            . 'folgen sie bitte die Anweisung darin, es könnte auch sein das sie in ihrem Spamordnder schauen müssen.'
        );
 
    }
    
    public function ActivateUser($sHash){
 
        $iCount = 0;
       
        $sQuery = '
            UPDATE
                `users`
            SET
                `activhash` = "",
                `activ` = "b(1)"
            WHERE
                `activhash` = :hash';
   
        $rHandler = $this->oDatabase->prepare($sQuery);
               
        $rHandler->bindParam(':hash', $sHash, PDO::PARAM_STR);
        
        try{
                
            $this->oDatabase->beginTransaction();

            $rHandler->execute();

            $iCount = $rHandler->rowCount();
           
            $this->oDatabase->commit();

        }catch(Exception $ex){

            $this->oDatabase->rollback();
            return array(
                'success' => 'false',
                'message' => 'Leider ist ein Fehler aufgetreten, versuche es zu einem späteren Zeizpunkt noch mal.'
            );
        }

        if($iCount === 1){
            return array(
                'success' => 'true',
                'message' => 'Die Aktivierung war erfolgreich, sie werden gleich zum Spiel weiter geleitet.'
            );
            
        }elseif($iCount === 0){
           return array(
                'success' => 'false',
                'message' => 'Entweder haben sie Ihren Account schon aktiviert, oder es ist leider ist ein Fehler aufgetreten, versuche es zu einem späteren Zeizpunkt noch mal.'
            ); 
        }
            
    }
    
    public function LoginUser($aUserData){

        $sQuery = '
            SELECT
                COUNT(*) AS "numbers",
                `id`,
                `activhash`,
                `activ`
            FROM
                `users`
            WHERE
                `name` = :name
            AND
                `password` = :password';
                
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->bindParam(':name', $aUserData['name'], PDO::PARAM_STR);
        $rHandler->bindParam(':password', $aUserData['password'], PDO::PARAM_STR);
        
        $rHandler->execute();
        
        $aRow = $rHandler->fetch(PDO::FETCH_ASSOC);
 
        if($aRow['numbers'] === '1'){

            if(
                $aRow['activ'] === '0'
                || $aRow['activhash'] !== ''
            ){
                return array(
                    'success' => 'false',
                    'message' => 'Sie müssen zuerst ihren Account aktivieren.'
                );

            }
            // Hier werden später die restelich userdate geholt
            return array(
                'success' => 'true'
            );

        }else{
            return array(
                    'success' => 'false',
                    'message' => 'Sie haben hier keinen Account.'
            );
        }
        
    }
    
    private function FetchAllUserDatas($iUserId){
        
        
    }
}