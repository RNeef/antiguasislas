<?php

/**
 * Diese Klasse ist für Functionen die man an verschiedene Stellen braucht.
 *
 * @author Rolf Neef
 */
class Helper {
    
    public function __construct() {
        
    }
    
    /**
     * Hier wird geprüft ob alle Felder ausgefüllt sind
     * 
     * @author Rolf Neef
     * @param array $aInputAssoc
     * @return array
     */
    public function CheckEmptyInputs($aInputAssoc){
        
        foreach($aInputAssoc as $mValue){

            if($mValue === ""){
                
                return array(
                    'success' => 'false',
                    'message' => 'Bitte fülle alle Felder aus.'
                );
                
            }
        }

        return array(
            'success' => 'true'
        );
    }
    
    /**
     * Funktion prüft auf eine Gültige Mail-Adresse und on es die Domain der
     * Mailadresse gibt.
     * 
     * @author Rolf Neef
     * @param string $sMail
     * @return array 
     */
    public function CheckValidateMail($sMail){
        
        $aMxHostsAssoc = array();

        if (filter_var($sMail, FILTER_VALIDATE_EMAIL) === false) {
            
            return array(
                'success' => 'false',
                'message' => 'Das ist keine gültige Mail-Adresse.'
            );
        }
        
	$aDoimanNumbers  =  preg_split ('/@/', $sMail); 
        getmxrr($aDoimanNumbers[1] , $aMxHostsAssoc);
        
        if (empty($aMxHostsAssoc)) {
            
            return array(
                'success' => 'false',
                'message' => 'Diese Mail-Domain existiert nicht.'
            );
        }
        
        return array(
            'success' => 'true'
        );
    }
   
}
