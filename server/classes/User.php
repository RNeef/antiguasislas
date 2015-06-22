<?php
/**
 * Description of User
 *
 * @author Rolf
 */
class User {
    
    private $oDatabase = null;
    private $aResultArray = array();
    private $sRegisterText = '';
    private $sForgottText = '';
    private $sPassword = '';
    
    public function __construct($oDatabse) {
        
        $this->oDatabase = $oDatabse;
        
    }
    
    /**
     * Prüft ob es diesen Namen und EMail - Adresse schon in der Datenbank gibt
     * 
     * @version 1.0
     * @author Rolf Neef rolf.neef@onlinehome.de
     * @param String $sName
     * @param String $sMail
     * @return Array
     */
    private function CheckOfDoubleNameMail($sName, $sMail){
        
        $oUserDataBase = new UserSql($this->oDatabase);
        $aUserArray = $oUserDataBase->GetAllUser();

        if($aUserArray['success'] === 'false'){

            return $aUserArray;
            
        }
        $iCounter = count($aUserArray['datas']);
        
        if($iCounter < 1){

            return array(
                'success' => 'true'
            );
        }
        
        foreach($aUserArray['datas'] as $iKey => $sValue){
            
            if(strstr($aUserArray['datas'][$iKey]['name'], $sName) !== false){
                return array(
                    'success' => 'false',
                    'message' => 'Dieser Nutzer Name existiert bereits.'
                );
            }
            
            if(stristr($aUserArray['datas'][$iKey]['mail'], $sMail) !== false){
                return array(
                    'success' => 'false',
                    'message' => 'Diese Mail-Adresse existiert bereits.'
                );
            }
        }
        
        return array(
            'success' => 'true'
        );
    }
    
    /**
     * Der Neue Account wird hier Aktiviert
     * 
     */
    public function ActivateAccount($sHash){
        
        $aHash = filter_var($sHash, FILTER_SANITIZE_STRING);
        
        $oUserDataBase = new UserSql($this->oDatabase);
        
        $aResult = $oUserDataBase->ActivateUser($sHash);
        
        return $aResult;
        
    }
    
    /**
     * Prüft ob die Mail korrekt geschrieben wurde ud es die Domain für Mails
     * überhaupt gibt
     * 
     * @version 1.0
     * @author Rolf Neef rolf.neef@onlinehome.de
     * @param String $sMail
     * @return Array
     */
    private function CheckMailAdress($sMail){
        
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
    
    /**
     * Sendet eine Mail zu User
     * 
     * @version 1.0
     * @author Rolf Neef rolf.neef@onliehome.de
     * @param String $sMail
     * @return Array
     */
    public function SendMail($sMail){
        
        $oMail = new Mailer();
	
	$oMail->setFrom("AI - Browsergame", "noreply@ai.de");
	$oMail->addRecipient('You', $sMail);
	$oMail->fillSubject("Deine Registrierung");
	$oMail->fillMessage($this->sRegisterText);
        
        $oMail->send();

        return array(
            'success' => 'true',
            'message' => 'Sie haben eine Mail erhalten'
        );
    }
    
    /**
     * Hier werden die eingegebene Daten des Users bearbeitet und zu den prüfenden
     * Methoden weiter gegeben, ist alles in Ordnung wird in die Datenbank geschrieben.
     * 
     * @version 1.0
     * @author Rolf Neef rolf.neef@onlinehome.de
     * @param Array $aRegisterDatas
     * @return Array
     */
    public function RegisterUser($aRegisterDatas){
       
        foreach($aRegisterDatas as $iKey => $sValue){
            
            if($aRegisterDatas[$iKey] !== 'mail'){
               $aRegisterDatas[$iKey] = filter_var($sValue, FILTER_SANITIZE_STRING); 
            }
        }
      
        $aResult = $this->CheckMailAdress($aRegisterDatas['mail']);
        
        if($aResult['success'] === 'false'){
            return $aResult;
        }
        
        $aResult = $this->CheckOfDoubleNameMail($aRegisterDatas['name'], $aRegisterDatas['mail']);
        if($aResult['success'] === 'false'){
            return $aResult;
        }

        $oUserDataBase = new UserSql($this->oDatabase);
        
        $aRegisterDatas['password'] = $this->ConvertWord($aRegisterDatas['password'], 'password');
        
        $aRegisterDatas['hash'] = $this->ConvertWord($aRegisterDatas['name'], 'activhash');
        
        $aResult = $oUserDataBase->SaveNewUser($aRegisterDatas);
        
        if($aResult['success'] === 'true'){
            
            $this->CreateMailText($aRegisterDatas['name'], $aRegisterDatas['hash']);
            
            $aResult = $this->SendMail($aRegisterDatas['mail']);
            
        }
        
        return $aResult;
        
    }
    
    public function LoginUser($aUserData){
        
        $oUserSql = new UserSql($this->oDatabase);
        
        foreach($aUserData as $iKey => $sValue){
            
            if($aUserData[$iKey] !== 'mail'){
               $aUserData[$iKey] = filter_var($sValue, FILTER_SANITIZE_STRING); 
            }
        }
        
        $aUserData['password'] = $this->ConvertWord($aUserData['password'], 'passwort');
        
        $aResult = $oUserSql->LoginUser($aUserData);
        
        return $aResult;
        
    }
    
    /**
     * Erstell aus dem übergebenen Wort ein verschlüsselten String der nicht 
     *  
     * @version 1.0
     * @author Rolf Neef rolf.neef@onlinehome.de
     * @param String $sWord
     * @param String $sOrigin
     * @return String $sHashed
     */
    private function ConvertWord($sWord, $sOrigin = 'password'){
        
        switch($sOrigin){
            
            case 'password':
                $sSalt = 'hello_user';
                break;
            case 'activhash':
                $sSalt = 'hello_activ';
                break;
        }

        $sHashed = hash('sha256', $sWord . $sSalt);

        return $sHashed;

    }
    
    /**
     * Baut den Mail-Text der Registrierung zusammen
     * 
     * @version 1.0
     * @author Rolf Neef rolf.neef@onlinehome.de
     * @param String $sUsername
     * @param String $sHash
     * @return void 
     */
    private function CreateMailText($sUsername, $sHash){
        
        $sLink = $_SERVER['HTTP_HOST'].'/activate.html?action='.$sHash;
        $sSpielname = 'AI';
        
        $this->sRegisterText = '<html>Hallo '. $sUsername .',<p>
                vielen Dank für Deine Regristierung. Wir freuen uns Dich als neues Mitglied in unserem Spiel begrüßen zu dürfen.</p>
                <p>Für die endgültige Registrierung musst Du nur auf den Link klicken.</p>
                <p><a href="'. $sLink .'" /></p>
                <p>Danach kannst du Dich mit deinem Benutzernamen und Passwort einloggen und die Welt von ' . $sSpielname . ' einsteigen</p>
                <p>Wenn die aktivierung nicht innerhalb von 24 Stunde erfolgt werden die Daten gelöscht.</p>
                <p>Ebenso wenn du diese Mail unberechtigter Weise erhalten hast.</p>
                <p>Eine Antwort auf diese Mail-Adresse hat keine Wirkung</p>
                <p></p>
                <p>Lieben Gruß</p>
                <p>Dein'. $sSpielname .'Team</html>';
    }
    
    private function SetNewPassword(){
        
        $iCounter = 0;
        $sString = '';
        
        for($iCounter; $iCounter < 12; $iCounter++){
            
            $iRandom = mt_rand(10, 120);
            
            $sString.= chr($iRandom);
            
        }
        
        $this->sPassword = $this->ConvertWord($sString, 'password');
        
    }
}
