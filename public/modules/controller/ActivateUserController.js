angular.module('AntiguasIslas').controller('ActivateUserController', function () {
    
});
//alert(navigator.userAgent);
var height = window.innerHeight;
document.getElementsByTagName('body')[0].style.height = height - 20 + 'px';

function activatet(){
    var aHttppGetVars = new Array();
    var sGetParams = document.location.search.substr(1,document.location.search.length);
    var aParams = sGetParams.split('&');
    var iCounter = 0;
    
    if(sGetParams !== ''){
   
        for(iCounter; iCounter < aParams.length; iCounter++){

            var sValue = '';
            var vArr = aParams[iCounter].split('=');
            if(vArr.length > 1){
                sValue = vArr[1];
            }
            aHttppGetVars[unescape(vArr[0])]=unescape(sValue);
        }
        
        $.ajax({
            url: 'php/webservices/registrierung.php',
            method: 'POST',
            data: {
                action: 'active',
                hash: aHttppGetVars['action']
            },
            dataType: 'json',
            success: function(oResult){
                if(oResult.success === 'true'){
                    
                    window.setTimeout("ForwardSite()", 1000);
                    
                }else{
                    
                    ShowMessage(oResult.message, oResult.success);
                    
                }
            }
        });
        
    }else{
        
//        ShowMessage('Die URL ist nicht korrekt.', 'false');
    }
}

function ForwardSite(){ 
    
   location.href='http://ruheloser.bplaced.net/index.html';
   
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