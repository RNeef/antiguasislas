<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TabellenSql
 *
 * @author Rolf
 */
class TabelsSql {
    
    private $oDatabase = null;
    private $bError = false;
    private $aTableFields = array();
    private $aTableValues = array();
    private $aForeigenValues = array();
    private $sTable = '';
    private $aErrorAssoc = array(
        'success' => 'false', 
        'message' => 'Es ist ein Interner Fehler aufgetreten.'
    );
    
    public function __construct($oDatabase) {
        
        $this->oDatabase = $oDatabase;
        
    }
    
    /**
     * Funktion holt alle Daten zum Aufbau der Tabelle und des Formulars in  
     * 
     * @author Rolf Neef
     * @param string $sTable
     * @return array
     */
    public function GetTableAllValues($sTable){
            
        $this->GetTableHead($sTable);
        $this->GetTableValues($sTable);
        
        if(empty($this->aTableFields)
           || $this->bError === true){
            
          return $this->aErrorAssoc;
            
        }else{
            foreach($this->aTableFields as $key => $value){
                
                $this->aTableFields[$key] = ucfirst($value);
                
            }
            return array(
                'success' => 'true',
                'header' => $this->aTableFields,
                'value' => $this->aTableValues,
                'foreigen' => $this->aForeigenValues,
                'table' => $this->sTable
            );
            
        }
    } 
    
    /**
     * Holt alle Datensätze der ausgewählten Tabelle
     * 
     * @author Rolf Neef
     * @param string $sTable
     * return void
     */
    public function GetTableValues($sTable){
        
        $sQuery = '
            SELECT 
                `'.implode('`,`', $this->aTableFields).'`
            FROM
                `'.$sTable.'`;';
        
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->execute();
		
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] === '00000'){
            
            while($aRowAssoc = $rHandler->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){

                $this->aTableValues[] = $aRowAssoc;
                
            } 
            
        }else{
            
            $this->bError = true;
            
        }
    }
    
    /**
     * Speichert einen neuen Datensatz der entsprechenden Tabelle
     * 
     * @author Rolf Neef
     * @param array $aRowDatasAssoc
     * @return array
     */
    public function InsertTableRow($aRowDatasAssoc){
 
        array_shift($aRowDatasAssoc['datas']);
        array_shift($aRowDatasAssoc['cells']);

        $sQuery = '
            INSERT INTO
                ' . $aRowDatasAssoc['table'] . '
                (
                   `'.implode('`,`', $aRowDatasAssoc['cells']).'`
                )
                VALUES
                (';
        foreach($aRowDatasAssoc['cells'] as $mValue){
            $sQuery.= ':'.$mValue.', ';
        }
        $sQuery = substr($sQuery, 0, -2);
 
        $sQuery.= ')';

        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $iNumbers = count($aRowDatasAssoc['cells']);

        for($iCounter = 0; $iCounter < $iNumbers; $iCounter++){
            
            $rHandler->bindParam(':'.($aRowDatasAssoc['cells'][$iCounter]), $aRowDatasAssoc['datas'][$iCounter], PDO::PARAM_STR);
        
        }
        try{
            
            $this->oDatabase->beginTransaction();
        
            $rHandler->execute();
            
            $this->oDatabase->commit();
            
        }catch(Exception $e){
            
            $this->oDatabase->rollback();
            
            return array(
                'success' => 'false',
                'message' => 'Speichern nicht erfolgreich da Datensatz offen.'
            );
        }
        
        $resultArray = $this->oDatabase->errorInfo();
        
        if($resultArray[0] === '00000'){
            
            return array(
                    'success' => 'true',
                    'message' => 'Datensatz erfolgreich gespeichert.'
                   );
            
        }else{
            
            return $this->aErrorAssoc;
            
        }
        
    }
    
    /**
     * Ändert den ausgewählten Datensatz der entsprechender Tabelle
     * 
     * @author Rolf Neef
     * @param array $aRowDataAssoc
     * @return array ob Erfolg oder nicht
     */
    public function UpdateTableRow($aRowDataAssoc){
        
        array_shift($aRowDataAssoc['cells']);
        
        $iRowId = array_shift($aRowDataAssoc['datas']);
        $iNumbers = count($aRowDataAssoc['cells']);
        $iCounter = 0;
        
        $sQuery = '
            UPDATE 
                `' . $aRowDataAssoc['table'] .'`
            SET ';
        
        for($iCounter; $iCounter < $iNumbers; $iCounter++){
            
            $sQuery.= '`' . $aRowDataAssoc['cells'][$iCounter] . '` = :' . $aRowDataAssoc['cells'][$iCounter] .', ';
        }
        
        $sQuery = substr($sQuery, 0, -2);
        
        $sQuery.= ' WHERE `id` = ' . intval($iRowId);
        
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        for($iCounter = 0; $iCounter < $iNumbers; $iCounter++){
            
            $rHandler->bindParam(':'.($aRowDatasAssoc['cells'][$iCounter]), $aRowDatasAssoc['datas'][$iCounter], PDO::PARAM_STR);
        
        }
        
        try{
            
            $this->oDatabase->beginTransaction();
        
            $rHandler->execute();
            
            $this->oDatabase->commit();
            
        }catch(Exception $e){
            
            $this->oDatabase->rollback();
            
            return array(
                'success' => 'false',
                'message' => 'Änderung konnte nicht gespeichert werden, da Datensatz offen.'
            );
        }
    
    }
    
    /**
     * Holt die Spaltennamen der ausgwewählten Tabelle
     * 
     * @author Rolf Neef
     * @param string $sTable
     */
    private function GetTableHead($sTable){
        
        $sQuery = '
            SHOW COLUMNS FROM '.$sTable;
        
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->execute();
		
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] === '00000'){
            
            while($aRowAssoc = $rHandler->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){

                $this->aTableFields[] = $aRowAssoc['Field'];
                if($aRowAssoc['Key'] === 'MUL'){
                    
                    $this->GetForeigenTable($sTable, $aRowAssoc['Field']);
                    
                }
                
            } 
        }
        
        $this->sTable = $sTable;
    }
    
    /**
     * Holt die Tabelle bei einem Foreigen Key un dübergibt diese an die nächste
     * Methode
     * 
     * @author Rolf Neef
     * @param string $sTable
     * @param string $sColName
     * @retun array
     */
    private function GetForeigenTable($sTable, $sColName){
        
        $sForeigenTable = "";
        
        $sQuery = '
            SELECT 
                `REFERENCED_TABLE_NAME`  
            FROM 
                `information_schema`.`KEY_COLUMN_USAGE`
            WHERE 
                `COLUMN_NAME` = :col
            AND 
                `TABLE_NAME` = :table';
        
        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->bindParam(':col', $sColName, PDO::PARAM_STR);
        $rHandler->bindParam(':table', $sTable, PDO::PARAM_STR);
        
        $rHandler->execute();
 
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] === '00000'){
            
            while($aRowAssoc = $rHandler->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){

                $sForeigenTable = $aRowAssoc['REFERENCED_TABLE_NAME'];
                
            }

            $this->GetForeigenValues($sForeigenTable, ucfirst($sColName));
            
        }else{
            
            $this->bError = true;
            
        }
    }
    
    /**
     * Holt die wichstigen Information des Foreigen Keys, wird benötigt um 
     * Select-Felder in des Tabellen-Formulars zu füllen in Javascript
     * 
     * @author Rolf Neef
     * @param string $sTable
     * @param string $sOriginCol
     */
    private function GetForeigenValues($sTable, $sOriginCol){

        $sQuery = '
            SELECT
                `id`,
                `name`
            FROM
                `'. $sTable .'`';

        $rHandler = $this->oDatabase->prepare($sQuery);
        
        $rHandler->execute();
        
        $resultArray = $this->oDatabase->errorInfo();

        if($resultArray[0] === '00000'){
            
            while($aRowAssoc = $rHandler->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){
 
                
                $this->aForeigenValues[$sOriginCol][] = $aRowAssoc;
                
            }
            
        }else{
            
            $this->bError = true;
            
        }
    }
}
