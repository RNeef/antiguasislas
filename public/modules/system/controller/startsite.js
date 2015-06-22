/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    
    var height = window.innerHeight;
    document.getElementsByTagName('body')[0].style.height = height - 20 + 'px';
    
    $('span').on(
        'click',
        function(sender){
            
            var sSender = sender.currentTarget.className;
            
            switch(sSender){
                
                case 'first':
                    CreateRegistrierung();
                    break;
                case 'second':
                    CreateNotification();
                    break;
            }
        }
    );
    
    function CreateRegistrierung(){
        
        var oHeadElement = document.getElementsByClassName('mainarea')[0];
        var oChildElement = document.createElement('div');

        if(document.getElementById('second')){

            var oElement = document.getElementById('second');
            oHeadElement.removeChild(oElement);
            
        }
        
        oChildElement.setAttribute('id', 'second');
        oChildElement.setAttribute('class', 'regist');
        oHeadElement.appendChild(oChildElement);
        var oCloseElement = document.createElement('img');
        oCloseElement.setAttribute('id', 'closebutton');
        oCloseElement.setAttribute('src', 'images/startsite/x-button.png');
        oChildElement.appendChild(oCloseElement);
        oHeadText = document.createElement('h1');
        oHeadText.textContent = 'Registrierung';
        oChildElement.appendChild(oHeadText);
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        var oLabel = document.createElement('label');
        oLabel.innerHTML = 'Nutzername:';
        oLine.appendChild(oLabel);
        oInput = document.createElement('input');
        oLine.appendChild(oInput);
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        var oLabel = document.createElement('label');
        oLabel.innerHTML = 'Passwort:';
        oLine.appendChild(oLabel);
        oInput = document.createElement('input');
        oInput.setAttribute('type', 'password')
        oLine.appendChild(oInput);
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        var oLabel = document.createElement('label');
        oLabel.innerHTML = 'Passwort wiederholen:';
        oLine.appendChild(oLabel);
        oInput = document.createElement('input');
        oInput.setAttribute('type', 'password')
        oLine.appendChild(oInput);
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        var oLabel = document.createElement('label');
        oLabel.innerHTML = 'E-Mail:';
        oLine.appendChild(oLabel);
        oInput = document.createElement('input');
        oLine.appendChild(oInput);
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        oButton = document.createElement('button');
        oButton.setAttribute('id', 'reg');
        oButton.innerHTML = 'Senden';
        oLine.appendChild(oButton);
        
        $('#reg').on(
          'click',
          function(){
              RegisterUser();
          }
        ); 

        $('#closebutton').on(
            'click',
            function(){
                $('#second').remove();
            }
        );
    }
    
    function RegisterUser(){
        
        var iMaxLetters = 15;
        var iMinLetters = 5
        
        if(
            document.getElementsByTagName('input')[0].value.length < iMinLetters
            || document.getElementsByTagName('input')[0].value.length > iMaxLetters
        ){
            
            ShowMessage('Username muss mind. ' +  iMinLetters+ ' Zeichen haben, \n\
                         und nicht mehr als ' + iMaxLetters + '.', 'false');
            return false;
        }
        
        var iNumbers = document.getElementsByTagName('input').length;
        var iCounter = 0;
        
        for(iCounter; iNumbers < iCounter; iCounter++){
            if(document.getElementsByTagName('input')[0].length === 0){
                ShowMessage('Bitte alle Felder ausfüllen', 'false');
                return false;
            }
            
        }
        
        if(document.getElementsByTagName('input')[1].value !== document.getElementsByTagName('input')[1].value){
            
            ShowMessage('Passwörter stimmen nicht überein.', 'false');
            return false;
            
        }
        
        
        var regEx = /([a-z0-9])/gi;
        var aResult = document.getElementsByTagName('input')[0].value.match(regEx);
        if(aResult.length < document.getElementsByTagName('input')[0].value.length){
            
            ShowMessage('Es sind keine Sonderzeichen erlaubt.', 'false');
            return false;
        }
        
        $.ajax({
            url: 'php/webservices/registrierung.php',
            method: 'POST',
            data: {
                action: 'register',
                name: document.getElementsByTagName('input')[0].value,
                password: document.getElementsByTagName('input')[1].value,
                mail: document.getElementsByTagName('input')[2].value
            },
            dataType: 'json',
            success: function(oResult){
                
                ShowMessage(oResult.message, oResult.success);
                
            }
        });
    }
    
    function ShowMessage(sText, sSuccess){
        
        var oBody = document.getElementsByClassName('mainarea')[0];
        
        if(document.getElementById('innermessage')){
            
            var oChildElement = document.getElementById('innermessage');
            oBody.removeChild(oChildElement);
            
        }
        var oMessage = document.createElement('div');
        var oButton = document.createElement('button');
        var oTextLine = document.createElement('p');
        oMessage.setAttribute('class', 'middle');
        oMessage.id = 'innermessage';
        oButton.id = 'innerclosebutton';
        oTextLine.id = 'messagetext';
        oButton.innerHTML = 'Ok';
        
        if(sSuccess === 'false'){
            oMessage.style.color = "#FF0000";
        }else{
            oMessage.style.color = "#00FF00";
        }
        
        oBody.appendChild(oMessage);
        oMessage.appendChild(oTextLine);
        oTextLine.innerHTML = sText;
        oMessage.appendChild(oButton);
        
        $('#innerclosebutton').on(
                'click',
                function(){
                    oBody.removeChild(oMessage);
                }
        );
        
    }
    
    function CreateNotification(){
        
        var oHeadElement = document.getElementsByClassName('mainarea')[0];
        var oChildElement = document.createElement('div');

        if(document.getElementById('second')){

            var oElement = document.getElementById('second');
            oHeadElement.removeChild(oElement);
            
        }
        
        oChildElement.setAttribute('id', 'second');
        oChildElement.setAttribute('class', 'regist');
        oHeadElement.appendChild(oChildElement);
        var oCloseElement = document.createElement('img');
        oCloseElement.setAttribute('id', 'closebutton');
        oCloseElement.setAttribute('src', 'images/startsite/x-button.png');
        oChildElement.appendChild(oCloseElement);
        oHeadText = document.createElement('h1');
        oHeadText.textContent = 'Login';
        oChildElement.appendChild(oHeadText);
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        var oLabel = document.createElement('label');
        oLabel.innerHTML = 'Nutzername:';
        oLine.appendChild(oLabel);
        oInput = document.createElement('input');
        oLine.appendChild(oInput);
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        var oLabel = document.createElement('label');
        oLabel.innerHTML = 'Passwort:';
        oLine.appendChild(oLabel);
        oInput = document.createElement('input');
        oInput.setAttribute('type', 'password')
        oLine.appendChild(oInput);
        
        oLine = document.createElement('p');
        oChildElement.appendChild(oLine);
        oButton = document.createElement('button');
        oButton.setAttribute('id', 'reg');
        oButton.innerHTML = 'Senden';
        oLine.appendChild(oButton);
        
        $('#reg').on(
          'click',
          function(){
              LoginUser();
          }
        ); 

        $('#closebutton').on(
            'click',
            function(){
                $('#second').remove();
            }
        );
        
    }
    
    function LoginUser(){
        
        $.ajax({
            url: 'php/webservices/registrierung.php',
            method: 'POST',
            data: {
                action: 'login',
                name: document.getElementsByTagName('input')[0].value,
                password: document.getElementsByTagName('input')[1].value,
            },
            dataType: 'json',
            success: function(oResult){
                
                if(oResult.success === 'true'){
                    $("#mainarea").innerHTML = "";
                    $("#mainarea").css({
                       'background-image': 'url(images/maps/01,01.jpg)' 
                    });
                    $("#mainarea").load("game.html");
                }else{
                    
                    ShowMessage(oResult.message, oResult.success);
                }
            }
        });
    }
    
});