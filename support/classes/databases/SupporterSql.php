<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SupporterSql
 *
 * @author Rolf
 */
class SupporterSql {
    
    private $iSupporterRights;
    private $oDatabase = null;
   
    /**
     * Konstructor, hier wird nur das Objekt der Datenbank übergeben 
     * 
     * @param opject $oDatebase
     * @return void
     */
    public function __construct($oDatabase) {
        
        $this->oDatabase = $oDatabase;
        
    }
    
    /**
     * Funktion prüft die Usereingaben bei der Anmeldung, bei Erfolgreicher
     * Anmeldung wird sofort das array ausgegeben die das Menü aufbaut
     * 
     * @author Rolf Neef
     * @param array $aInputArray
     * @param object $oDatebase
     * @return array $aResultAssoc
     */
    public function Login($aInputArray){
        
        $aResultAssoc = array();
        
        $sQuery = 
            'SELECT
                `id`,
                `name`,
                `rightId`
             FROM
                `supporter`
             WHERE
                BINARY `name` = :name
             AND
                BINARY `password` = :password
            ';
        
        $rHandler = $this->oDatabase->prepare($sQuery);
		
        $rHandler->bindParam(':name', $aInputArray['name'], PDO::PARAM_STR);
        $rHandler->bindParam(':password', $aInputArray['password'], PDO::PARAM_STR);
		
        $rHandler->execute();
		
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] !== '00000'){

            return array(
                'success' => 'false', 
                'message' => 'Es ist ein Interner fehler aufgetreten.'
            );	
        }elseif($rHandler->rowCount() === 0) {
            
            return array(
                'success' => 'false', 
                'message' => 'Passwort und Name stimmen nicht überein'
            );
            
        }else{
                
            $aResult = $rHandler->fetch(PDO::FETCH_ASSOC);
            
            $this->iSupporterRights = $aResult['rightId'];
            
            $aMenueTreeAssoc = $this->GetMenueTree();

            $aResultAssoc = array(
                'success' => 'true',
                'tree' => $aMenueTreeAssoc
            );
            
            return $aResultAssoc;
        }
    }
    
    /**
     * Funktion holt alle Menüeinträge die ein Supporter durchführen kann, je
     * nach seinen rechten
     * 
     * @author Rolf Neef
     * @param object $oDatebase
     * @return array $aMenueTreeAssoc
     */
    private function GetMenueTree(){
        
        $aMenueTreeAssoc = array();
        
        $sQuery = '
            SELECT 
                n.`name`,
                n.`action`,
                COUNT(*)-1 AS level,
                ROUND ((n.right_site - n.left_site - 1) / 2) AS offspring
            FROM 
                `menu_points` AS n,
                `menu_points`  AS p
            WHERE 
                n.left_site BETWEEN p.left_site AND p.right_site
            AND
               n.`rightId` >= :supportRight 
            GROUP BY n.left_site
            ORDER BY n.left_site;';
        
        $rHandler = $this->oDatabase->prepare($sQuery);
		
        $rHandler->bindParam(':supportRight', $this->iSupporterRights, PDO::PARAM_STR);
		
        $rHandler->execute();
        
        while ($aRowAssoc = $rHandler->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            
            $aMenueTreeAssoc[] = $aRowAssoc;
            
        }

        return $aMenueTreeAssoc;
    }
    
    /**
     * Funktion holt die nöglichen Rechte aus der Datebank für das anlegen neuer
     * Supporter
     * 
     * @author Rolf Neef
     * @param object $oDatebase
     * @return array $aRowAssoc
     */
    public function GetRights(){
        
        $aRightsAssoc = array();
        
        $sQuery = '
            SELECT
                `id`,
                `name`
            FROM
                `rights`';
        
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->execute();
        
        while($aRowAssoc = $rHandler->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){
            
            $aRightsAssoc[] = $aRowAssoc;
            
        }

        return $aRightsAssoc;
  
    }
    
    /**
     * Funktion speiechert einen neuen Supporter
     * 
     * @author Rolf Neef
     * @param array $aSupporterAsscoc
     * @return array
     */
    public function SaveNewSupporter($aSupporterAsscoc){
        
        $aSupporterExist = $this->CheckSupporterExists($aSupporterAsscoc['name'], $aSupporterAsscoc['mail']);
 
        if($aSupporterExist['success'] === 'false'){
           
            return array(
                'success' => 'false', 
                'message' => $aSupporterExist['message']
            );
        }
        
        $sQuery = '
            INSERT INTO
                `supporter`
                (`name`,
                 `rightId`,
                 `password`,
                 `e-mail`
                )VALUES(
                 :name,
                 :rights,
                 :password,
                 :mail
                );';
              
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->bindParam(':name', $aSupporterAsscoc['name'], PDO::PARAM_STR);
        $rHandler->bindParam(':rights', $aSupporterAsscoc['right'], PDO::PARAM_INT);
        $rHandler->bindParam(':password', $aSupporterAsscoc['password'], PDO::PARAM_STR);
        $rHandler->bindParam(':mail', $aSupporterAsscoc['mail'], PDO::PARAM_STR);
        
        $rHandler->execute();
        
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] !== '00000'){

            return array(
                'success' => 'false', 
                'message' => 'Der neue User konnte nicht angelegt werden.'
            );
            
        }else{
            
            return array(
                'success' => 'true',
                'message' => 'Neuer User ist angelegt.'
            );
        }
    }
    
    /**
     * Funktio prüft ob es schon einen Supporter mit dem eingegeben Namen oder
     * Mail-Adresse gibt
     * 
     * @author Rolf Neef
     * @param string $sName
     * @param string $sEmail
     * @return array
     */
    private function CheckSupporterExists($sName, $sEmail){

        $sQuery = '
            SELECT
                COUNT(*)
            FROM
                `supporter`
            WHERE
                `name` = :name
            OR
                `e-mail` = :mail';
        
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->bindParam(':name', $sName, PDO::PARAM_STR);
        $rHandler->bindParam(':mail', $sEmail, PDO::PARAM_STR);
        
        $rHandler->execute();
         
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] !== '00000'){

            return array(
                'success' => 'false', 
                'message' => 'Ein Interner Fehler ist aufgetreten.'
            );
        }

        if($rHandler->fetchColumn() > 0){
            
            return array(
                'success' => 'false', 
                'message' => 'Es existiert schon ein Nutzer mit diesen Namen oder Mail-Adresse.'
            );
        }
        
        return array(
            'success' => 'true'
        ); 
    }
}
