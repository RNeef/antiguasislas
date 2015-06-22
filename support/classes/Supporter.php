<?php
    /**
     * Klassse für das Object Supporter
     * 
     * @author Rolf Neef
     */
    class Supporter{
        
        private $iSupporterId;
        private $iRights;
        
        /**
         * Konstruktor bleibt leer, da keine interne Parameter bearbeitet werden.
         * 
         * @author Rolf Neef
         */
        public function __construct(){
            
        }
        
        /**
         * Hier logt sich der Supporter ein, bei erfolgreichen Login wird per
         * Javascript das Support-Tool aufgebaut
         * 
         * @param array $aUserInputsAssoc
         * @param object $oDatabase
         * @return array $aSupporterAssoc
         */
        public function Login($aUserInputsAssoc, $oDatabase){
            
            $oSupporterSql = new SupporterSql($oDatabase);
                                 
            $aUserInputsAssoc['password'] = $this->ConvertPassword($aUserInputsAssoc['password']);
            
            $aSupporterAssoc = $oSupporterSql->Login($aUserInputsAssoc, $oDatabase);
            
            return $aSupporterAssoc;
        }
        
        /**
         * Funktion gibt alle Rechte-Gruppen an Javascript weiter
         * 
         * @author Rolf Neef
         * @param object $oDatabase
         * @return array $aRightsAssoc
         */
        public function GetRightsList($oDatabase){
            
            $oSupporterSql = new SupporterSql($oDatabase);
            
            $aRightsAssoc = $oSupporterSql->GetRights($oDatabase);
            
            return $aRightsAssoc;
        }
        
        /**
         * Gibt das Ergebnis des speicherns an Javascript weiter
         * 
         * @author Rolf Neef
         * @param array $aInputArrayAssoc
         * @param object $oDatabase
         * @return array $aSupporterAssoc
         */
        public function SaveNewSupporter($aInputArrayAssoc, $oDatabase){
            
            $oSupporterSql = new SupporterSql($oDatabase);
                                 
            $aInputArrayAssoc['password'] = $this->ConvertPassword($aInputArrayAssoc['password']);
            
            $aSupporterAssoc = $oSupporterSql->SaveNewSupporter($aInputArrayAssoc, $oDatabase);
            
            return $aSupporterAssoc;
            
        }
        
        /**
        * Funktion conventiert das eingegeben Passwort in einen Hash-String
        * 
        * @author Rolf Neef
        * 
        * @param string $password
        * 
        * @return string
        */	
        private function ConvertPassword($sPassword){

            $sSalt      = 'hello_supporter';
            $sHashed    = hash('sha256', $sPassword . $sSalt);

            return $sHashed;

        }
    }
?>
