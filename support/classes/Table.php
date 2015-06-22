<?php
    /**
     * Klassse für das Object Table, alles was die Tabelleninhalte in der 
     * Datenbank manipuliert
     * 
     * @author Rolf Neef
     */
    class Table{
        
        public function __construct() {
            
        }
        
        public function GetTableValues($sTableName, $oDatabase){
            
            $oTableSql = new TabelsSql($oDatabase);
            
            $aResultAssoc = $oTableSql->GetTableAllValues($sTableName);
            
            return $aResultAssoc;
        }
        
        public function SaveTableRow($aPostDatas, $oDatabase){
            
            $oHelper = new Helper();
            
            $oTableSql = new TabelsSql($oDatabase);
            
            $aResultAssoc  = $oHelper->CheckEmptyInputs($aPostDatas['datas']);

            if($aResultAssoc['success'] === 'false'){
                
                return $aResultAssoc;
                
            }
            
            foreach($aPostDatas['cells'] as $key => $value){
                
                $aPostDatas['cells'][$key] = lcfirst($value);
                
            }
 
            if($aPostDatas['datas']['0'] !== '0'){
                
                $aResultAssoc = $oTableSql->UpdateTableRow($aPostDatas);
                
            }else{
                
                $aResultAssoc = $oTableSql->InsertTableRow($aPostDatas);
                
            }
            
            return $aResultAssoc;
        }
        
        
    }

