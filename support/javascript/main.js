$( document ).ready(function() {
    
    /**
     * Wenn man bei der Anmeldung die Entertaste drückt, gleicher Effekt wie
     * beim klicken auf de Button
     * 
     * @author Rolf Neef
     */
    $('#body').on(
        "keydown",
        function(){
            if(event.keyCode === 13){
                
                CheckInputs();
                
            }
        }
    );
    
    /**
     * Wenn bei der Anmeldung der Button geklickt wird, werde die Daten überprüft,
     * Bei erfolgreicher Anmeldung wird das Support-Tool aufgebaut.
     * 
     * @author Rolf Neef
     */
    $(".register").on(
        "click",
        function(){
            
           CheckInputs();
            
        }
    );
    
    /**
     * Funktion überprüft ob das Anmelden korrekt durchgeführt wurde, wenn ja 
     * werden diese anschließen durch die Funktion RegisterUser() geprüft
     * 
     * @author Rolf Neef
     * 
     * @returns void
     */
    function CheckInputs(){
        
        var numbers = $(".reg").length;
        var start = 0;
        var error = false;

        for(start; start < numbers; start++){

            if($(".reg:eq( " + start + ")").val() === ""){

                error = true;

            }
        }

        if(error === true){

            ShowMessage("Bitte füllen sie alle Felder aus");

        }else if(error === false){

            RegisterUser();

        }
    }
    
    /**
     * Schließt das DIV mit der Fehlermeldung
     * 
     * @author Rolf Neef
     */
    $("#closebutton").on(
    	"click",
    	function(){
            
            $("#message").css({
                    display: 'none'
            });
            
    	}
    );
    
    /**
     * Funktion überprüft mittels Ajax die Anmeldung des Users, bei erfolgreicher 
     * Anmeldung wird anschließend das Support-Tool aufgebaut
     * 
     * @author Rolf Neef
     * @returns void
     */
    function RegisterUser(){
        
        $.ajax({
           url: 'webservices/register.php',
           method: 'POST',
           data: {
               action: 'register',
               name: $(".name").val(),
               password: $(".password").val()
           },
           dataType: 'json',
           success: function(oResult){
           		// result.success was auch immer mit gegeben wird kann man zum ändern
           		// von Farben nehmen
                if(oResult.success === 'true'){

                    BuildSupportWindow(oResult.tree);
                    
                }else{
                    
                   ShowMessage(oResult.message);
               
               }
           }
           
        });
    }
    
    /**
     * Zeigt eine Fehlermeldung an wenn bei der Anmeldung etwas nicht richtig war
     * oder etwas nicht funktioniert hat an.
     * 
     * @author Rolf Neef
     * @param string sText
     * @returns void
     */
    
    function ShowMessage(sText){
    	
    	$("#messagetext").text(sText);
    	
    	$("#message").css({
    		
            display: 'block'
    		
    	});
    	
    }
    
    /**
     * Hier werden die drei Hauptbereiche des Tools erstellt.
     * 
     * @author Rolf Neef
     * @param object oTree
     * @returns void
     */
    function BuildSupportWindow(oTree){
 
        $('#body').empty();
        
        var oElementHeader = document.createElement('header');
        var oElementNavigate = document.createElement('nav');
        var oElementContet = document.createElement('section');
        
        document.body.appendChild(oElementHeader);
        document.body.appendChild(oElementNavigate);
        document.body.appendChild(oElementContet);
        
        var iWindowWidth = window.innerWidth;
        
        $('header').css({
            width: iWindowWidth - parseInt($('nav').css('width')) - 18 + 'px'
        });
        
        $('section').css({
            width: iWindowWidth - parseInt($('nav').css('width')) - 18 + 'px',
        });
        
        $('header').text("Hier werden später immer die aktuellen Tickets angezeigt");
        
        $('section').text("Hier kommt die Tätigkeiten hin. Und auch Listen zum  Beispiel User");
        
        SetWorkList(oTree);
    }
    
    /**
     * Hier wird nun das Menü zusammengestellt, welches dem eingeloggten Supporter
     * zu Verfügug steht
     * 
     * @author Rolf Neef
     * @param object oTree
     * @returns void
     */
    function SetWorkList(oTree){
        var a = 0;
        var key = null;
        var oHauptElement = document.getElementsByTagName('nav')[0];
        for(key in oTree){
            if(oTree[key].level === '0'){
                a++;
                var oListPoint = document.createElement('li');
                oListPoint.origin = 'under' + a;
                oListPoint.innerHTML = oTree[key].name;
                oListPoint.id = 'point' + a;
                oHauptElement.appendChild(oListPoint);
                $('#point' + a).addClass('over');
                var oDivElement = document.createElement('ul');
                oDivElement.id = 'under' + a;
                oHauptElement.appendChild(oDivElement);
                $('#under' + a).addClass('underlist');
            }else{
                var oUnderList = document.createElement('li');
                oUnderList.innerHTML = oTree[key].name;
                if(oTree[key].name.search('Tabelle') > -1){
                    oUnderList.action = "table";
                    oUnderList.table = oTree[key].action;
                }else{
                    oUnderList.action = oTree[key].action;
                } 
                oUnderList.setAttribute('class', 'middleList');
                oDivElement.appendChild(oUnderList);
                
            }
        }
        $('.over').on(
            'click',
            function(sender){
                if($('#' + sender.currentTarget.origin).css('display') === 'none'){
                     $('#' + sender.currentTarget.origin).removeClass('underlist');
                     $('#' + sender.currentTarget.origin).addClass('underlistsee');

                }else{
                   $('#' + sender.currentTarget.origin).removeClass('underlistsee');
                     $('#' + sender.currentTarget.origin).addClass('underlist'); 
                }
            }
        );
    
        $('.middleList').on(
            'click',
            function(sender){
               
                switch(sender.currentTarget.action){

                    case 'newsupporter':
                        CreateSupporterForm();
                        break;
                    case 'table':
                        CreateTableForm(sender.currentTarget.table)
                        break;
                }

            }

        );
    }
    
    /**
     * Hier wird nun das Formular zusammengestellt um einen neuen Supporter zu
     * registrieren
     * 
     * @author Rolf Neef
     * @returns {void}
     */
    function CreateSupporterForm(){

        var oHauptElement = document.getElementsByTagName('section')[0];
        var oFieldSet = document.createElement('fieldset');
        var oLegend = document.createElement('legend');
        var oNameInput = document.createElement('input');
        var oPasswordInput = document.createElement('input');
        var oMailInput = document.createElement('input');
        var oSelect = document.createElement('select');
        var oNameLabel = document.createElement('label');
        var oPasswordLabel = document.createElement('label');
        var oMailLabel = document.createElement('label');
        var oSelectLabel = document.createElement('label');
        var oLine = document.createElement('p');
        var oButton = document.createElement('button');
        
        oFieldSet.setAttribute('class', 'supFieldset');
        oNameInput.setAttribute('class', 'inputSup name');
        oPasswordInput.setAttribute('class', 'inputSup password');
        oMailInput.setAttribute('class', 'inputSup mail');
        oSelect.setAttribute('class', 'select');
        oButton.setAttribute('class', 'newSup');
        
        oLegend.innerHTML = 'Supporter anlegen';        
        oNameLabel.innerHTML = 'Name:';
        oPasswordLabel.innerHTML = 'Passwort:';
        oMailLabel.innerHTML = 'E-Mail:';
        oSelectLabel.innerHTML = 'Rechte als:';
        oButton.innerHTML = 'Speichern';
        
        oHauptElement.innerHTML = "";
        oHauptElement.appendChild(oFieldSet);
        oFieldSet.appendChild(oLegend);
        oFieldSet.appendChild(oNameLabel);
        oFieldSet.appendChild(oNameInput);
        oFieldSet.appendChild(oPasswordLabel);
        oFieldSet.appendChild(oPasswordInput);
        oFieldSet.appendChild(oMailLabel);
        oFieldSet.appendChild(oMailInput);
        oFieldSet.appendChild(oSelectLabel);
        oFieldSet.appendChild(oSelect);
        oFieldSet.appendChild(oLine);
        oLine.appendChild(oButton);
        
        SupporterRights(oSelect);
        
        $('.newSup').on(
            'click',
            function(){
                $.ajax({
                    url: 'webservices/supporteractions.php',
                    method: 'POST',
                    data:{
                        action: 'saveSupporter',
                        name: $('.name').val(),
                        password: $('.password').val(),
                        mail: $('.mail').val(),
                        right: $('.select').val()
                    },
                    dataType: 'json',
                    success: function(oResult){
                        
                        CreateMessage(oResult);
                        
                    }
                });
            }
     );

    }

    /**
     * Funktion holt sich per Ajax alle Rechte und baut daraus die Options-Tags
     * für das Select-Feld
     * 
     * @author Rolf Neef
     * @param {object} oSelect HTML-Element
     * @returns {void}
     */
    function SupporterRights(oSelect){
        
        $.ajax({
            url: 'webservices/supporteractions.php',
            method: 'POST',
            data: {
                action: 'getRights'
            },
            dataType: 'json', 
            success: function(oRights){

                for(key in oRights){
                    var oOption = document.createElement('option');
                    oOption.innerHTML = oRights[key].name;
                    oOption.value = oRights[key].id;
                    oSelect.appendChild(oOption);
                }

            }

        });
    }
    
    /**
     * Funktion erstellt die Message-Box falls schon eine existiert wird diese
     * zuerst gelöscht.
     * 
     * @author Rolf Neef
     * @param {object} oResult
     * @returns {void}
     */
    function CreateMessage(oResult){
        
        var oBody = document.getElementById('body');
        
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
        
        if(oResult.success === 'false'){
            
            oMessage.setAttribute('class', 'middle warning'); 
            
        }else{
            
            oMessage.setAttribute('class', 'middle successfull');
        
        }
        
        oBody.appendChild(oMessage);
        oMessage.appendChild(oTextLine);
        oTextLine.innerHTML = oResult.message;
        oMessage.appendChild(oButton);
        
        $('#innerclosebutton').on(
                'click',
                function(){
                    oBody.removeChild(oMessage);
                }
        );
    	
    }
    
    /**
     * Die Function holt mit Ajax alle Werte aus der Datenbank die für das
     * dynamische Anzeige der Tabelle und des Formulars erforderlich sind 
     * 
     * @author Rolf Neef
     * @param {string} sTable
     * @returns {void}
     */
    function CreateTableForm(sTable){
        
        $.ajax({
            url: 'webservices/supporteractions.php',
            method: 'POST',
            data: {
                action: 'table',
                table: sTable
            },
            dataType: 'json', 
            success: function(oResult){
                
                if(oResult.success === 'false'){
                    
                    CreateMessage(oResult);
                    
                }else{
                    
                    CreateTable(oResult.header, oResult.value, oResult.foreigen, oResult.table);
                    
                }
            }
        });
    }
    
    /**
     * Funktion erzeugt dei Tabelle un ddas entsprechende Formular, ganz
     * dynamisch
     * 
     * @author Rolf Neef
     * @param {object} oHeader
     * @param {object} oValues
     * @param {multi, obeject or array} mForeigen
     * @param {string} sTable
     * @returns {void}
     */
    function CreateTable(oHeader, oValues, mForeigen, sTable){
        
        var oHauptElement = document.getElementsByTagName('section')[0];
        oHauptElement.innerHTML = "";
        
        if(document.getElementById('table')){
            
            var oChildElement = document.getElementById('table');
            oHauptElement.removeChild(oChildElement);
            
        }
    
        var oTable = document.createElement('table');
        oTable.id = 'table';
        var oRow = document.createElement('tr');
        var a = 0;
        var aArray = [];
        oHauptElement.appendChild(oTable);
        oTable.appendChild(oRow);
        for(var key in oHeader){
            var oHeader = document.createElement('th');
            oHeader.innerHTML = oHeader[key];
            oRow.appendChild(oHeader);
            aArray.push(oHeader.offsetWidth);
        }
        
        var oHeader = document.createElement('th');
        oHeader.innerHTML = 'Funktionen';
        oRow.appendChild(oHeader);
        aArray.push(oHeader.offsetWidth);
        var oTBody = document.createElement('tbody');
        oTBody.setAttribute('class', 'scroll');
        
        oTable.appendChild(oTBody);
        for(var valueKeys in oValues){
            var oRow = document.createElement('tr');
            oTBody.appendChild(oRow);
            a = 0;
            for(var cell in oValues[valueKeys]){
                var oCell = document.createElement('td');
                oCell.innerHTML = oValues[valueKeys][cell];
                oRow.appendChild(oCell);
                if(oValues[valueKeys][cell].length === 1){
                    oCell.setAttribute('class', 'center cell' + a +'');
                }else{
                    oCell.setAttribute('class', 'normal cell' + a +'');
                }
                if(oCell.offsetWidth < aArray[a]){
                    
                    oCell.style.width = aArray[a] + 'px';
                    
                }else{
                    if(oCell.offsetWidth > aArray[a]){
                        aArray[a] = oCell.offsetWidth;
                        oCell.style.width = '0px';
                    }
                }
                
                a++;
            }

            var oCell = document.createElement('td');
            oCell.setAttribute('class', 'image cell' + a);
            var oImage = document.createElement('img');
            var oEditImage = document.createElement('img');
            oImage.setAttribute('src', 'images/rubbish_bin.png');
            oImage.setAttribute('title', 'Löschen');
            oImage.setAttribute('class', 'firstimage tablebutton');
            oImage.action = 'delete';
            oImage.id = oValues[valueKeys]['id'];
            oEditImage.setAttribute('src', 'images/edit.png');
            oEditImage.setAttribute('title', 'Bearbeiten');
            oEditImage.setAttribute('class', 'tablebutton');
            oEditImage.action = 'edit';
            oEditImage.id = oValues[valueKeys]['id'];
            
            oRow.appendChild(oCell);
            oCell.appendChild(oImage);
            oCell.appendChild(oEditImage);
        }

        for(var test in aArray){
            var cell = document.getElementsByClassName('cell' + test);
            var numbers = document.getElementsByClassName('cell' + test).length;
            document.getElementsByTagName('th')[test].style.width = aArray[test] + 'px';
            for(var start = 0; numbers > start; start++){
                cell[start].style.width = aArray[test] + 'px';
            }
        }
        
        $('.tablebutton').on(
            'click',
            function(sender){
  
                if(sender.currentTarget.action === 'edit'){
                    
                    var iNumbers = sender.target.parentElement.parentElement.childNodes.length - 1;
                    var iCounter = 0;
                    
                    for(iCounter; iCounter < iNumbers; iCounter++){
                        
                        var oTargetElement = document.getElementsByClassName('inputSup')[iCounter];
                        var oOriginElemet = sender.target.parentElement.parentElement.childNodes[iCounter];
                        
                        if(oTargetElement.nodeName === 'SELECT'){
                            
                            var iOptionsCounter = 0;
                            var iOptionNumbers = oTargetElement.childNodes.length;
                            
                            for(iOptionsCounter; iOptionsCounter < iOptionNumbers; iOptionsCounter++){
                                
                                var oOptionsElement = oTargetElement.childNodes[iOptionsCounter];
                                
                                if(oOptionsElement.value === oOriginElemet.innerHTML){
                                    
                                    oOptionsElement.setAttribute('selected', 'selected');
                                    
                                }
                            }
                            
                        }else{
                            
                           oTargetElement.value = oOriginElemet.innerHTML; 
                           
                        }                        
                    }
                }
            }
        );
        
        var oFieldElement = document.createElement('fieldset');
        oFieldElement.setAttribute('class', 'newDataSet');
        var oLegendElement = document.createElement('legend');
        oLegendElement.innerHTML = 'Neuer Datensatz';
        oHauptElement.appendChild(oFieldElement);
        oFieldElement.appendChild(oLegendElement);
        for(key in oHeader){
            var oLine = document.createElement('p');
            var oLabelElement = document.createElement('label');
            oLabelElement.innerHTML = oHeader[key] + ':';

            if(mForeigen.hasOwnProperty(oHeader[key])){
                for(var ObjKey in mForeigen){
                    if(ObjKey === oHeader[key]){
                        var oInputField = document.createElement('select');
                        for(var innKey in mForeigen[ObjKey]){
                            var oOptions = document.createElement('option');
                            oOptions.innerHTML = mForeigen[ObjKey][innKey].name + 
                                    ' (' + mForeigen[ObjKey][innKey].id + ')';
                            oOptions.setAttribute('value', mForeigen[ObjKey][innKey].id); 
                            oInputField.appendChild(oOptions);
                        }
                    }
                }
            }else{
               var oInputField = document.createElement('input');
                if(oHeader[key] === 'Id'){
                    oInputField.setAttribute('readonly', 'readonly');
                    oInputField.setAttribute('disabled', 'disabled');
                }
                oInputField.setAttribute('value', '');
            }
            oInputField.setAttribute('class', 'inputSup');
            oInputField.setAttribute('id', oHeader[key]);
            oFieldElement.appendChild(oLine);
            oLine.appendChild(oLabelElement);
            oLine.appendChild(oInputField);
        }
        var oLine = document.createElement('p');
        var oButton = document.createElement('button');
        oButton.setAttribute('class', 'newSup');
        oButton.innerHTML = 'Speichern';
        oFieldElement.appendChild(oLine);
        oLine.appendChild(oButton);
        
        $('.newSup').on(
            'click',
            function(){
                var aSaveDatas = [];
                var counter = 0;
                $.each($(".inputSup"), function() {

                    if(counter === 0 && $(".inputSup:eq(" + counter + ")").val() === ""){
                        
                       aSaveDatas.push('0');
                       
                    }else{
                        
                        aSaveDatas.push($(".inputSup:eq(" + counter + ")").val());
                        
                    }
                    counter++;
                });
            
                $.ajax({
                    url: 'webservices/supporteractions.php',
                    method: 'POST',
                    data: {
                        action: 'save',
                        datas: aSaveDatas,
                        cells: oHeader,
                        table: sTable
                    },
                    dataType: 'json',
                    success: function(oResult){
                        
                    }
                });
            }
        );
    }
});