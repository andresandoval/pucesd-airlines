/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */

var waiting = '<div align="center" class="waiting"></div>';
var defaultFilterValue = 'Buscar registros almacenados';
var currentForm = null;

function closeOverlay(overlayId){
    var overlay = document.getElementById(overlayId);
    overlay.innerHTML = '';
    overlay.style.visibility = 'hidden';
    overlay.setAttribute('onclick','');
}

function windowOverlay(title, body, overlayId, innerOverlay){
    var content = '<div class="windowOverlayContainer">';
        content += '<div class="windowOverlay">';
        content += '<div class="windowOverlayHeader">';
        content += '<div class="windowOverlayHeaderCloseButton" onclick="closeOverlay(\''+overlayId + '\');"></div>';
        content += '<div class="windowOverlayHeaderText">';
        content += title;
        content += '</div>';
        content += '</div>';
        content += '<div class="windowOverlayBody">';
        content += body;
        content += '</div>';
        content += (innerOverlay)? '<div id="innerOverlay" class="overlay"></div>' : '';
        content += '</div>';
        content += '</div>';
    return content;
}

function welcomeMessage(){
    window.setTimeout(function(){
        document.getElementById('overlay').innerHTML = windowOverlay('Inicio', '¡ Bienvenido al sistema !','overlay',false);
        document.getElementById('overlay').style.visibility = 'visible';
    },250);
    window.setTimeout(function(){closeOverlay('overlay');}, 2000);
}

function waitingOverlay(op, overlayId){
    var overlay = document.getElementById(overlayId);
    overlay.innerHTML = (op)? '<div class="waitingOverlay"></div>' : '';
    overlay.style.visibility = (op)? 'visible':'hidden';
}

function alert(title, body, overlayId){
    var overlay = document.getElementById(overlayId);
    overlay.innerHTML = windowOverlay(title, body, overlayId, false);
    overlay.style.visibility = 'visible';
    window.setTimeout(function(){closeOverlay(overlayId);}, 2000);
}

function confirmar(){
    return confirm('¿ Seguro ?');
}

function resetLookUp(){
    if( document.getElementById("filter").value === defaultFilterValue ){
        return;
    }
    document.getElementById("filter").value= defaultFilterValue;
    document.frmLookUp.submit();
}

function get_XmlHttp() {
    var xmlHttp = null;                            
    if(window.XMLHttpRequest) {
        xmlHttp = new XMLHttpRequest();
    }
    else if(window.ActiveXObject) {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    return xmlHttp;
}
//<editor-fold>
//</editor-fold>

function getDetails(what_, detailOf_){
    var id_= document.getElementById(detailOf_).value;    
    if(id_ === ''){
        document.getElementById(response).innerHTML = '';
        return;
    }
    var response = detailOf_+'Details';
    document.getElementById(response).innerHTML = '<div align="center" class="waitingSmall"></div>';
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            document.getElementById(response).innerHTML=xmlhttp.responseText;
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send('action=detail&what='+what_+'&detailOf='+detailOf_+'&id='+id_);
}

function getBackUp(){
    showModalDialog('backUp.php',null,'status:no;resizable:no;');
}

function getForm(formId){
    var overlay = document.getElementById('overlay');
    var body = document.getElementById('bodyMenu');
    if(formId != 3) body.innerHTML = waiting;
    else {overlay.style.visibility = 'visible';overlay.innerHTML = '<div class="waitingOverlay"></div>';}
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            if(formId != 3){
                body.innerHTML = xmlhttp.responseText;
                if(formId === 2 ) lookUp_('user');
                else if(formId === 5) lookUp_('route');
                else if(formId === 7) lookUp_('plane');
                else if(formId === 9) lookUp_('pilot');
                else if(formId === 10) lookUp_('reservation');
                else if(formId === 11) lookUp_('res_incumplidas');
                else if(formId === 12) lookUp_('event');
                for (i=0; i<13; i++) try{document.getElementById('mnuItem'+i).style.background = '#eeeeee';}catch(ex){}
                document.getElementById('mnuItem'+formId).style.background = '#ff7777';
            }else overlay.innerHTML = windowOverlay('BackUp',xmlhttp.responseText,'overlay',false);               
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send('form='+formId);
    currentForm = formId;
}

function create_(what_){
    var postString = "";
    var state;
    if(what_ === 'user'){
            var userId = document.getElementById('userId').value;
            var userName = document.getElementById('userName').value;
            var userLastname = document.getElementById('userLastname').value;
            var userCi = document.getElementById('userCi').value;
            var userGender = document.getElementById('userGender').value;
            var userBirthDate = document.getElementById('userBirthDate').value;
            var userNationality = document.getElementById('userNationality').value;
            var userEmail = document.getElementById('userEmail').value;
            var userPhoneNumber = document.getElementById('userPhoneNumber').value;
            var userType = document.getElementById('userType').value;
            var userPassword_1 = document.getElementById('userPassword_1').value;
            var userPassword_2 = document.getElementById('userPassword_2').value;
            if (userPassword_1 !== userPassword_2){
                alert ('Crear Usuario','¡ Las contraseñas no coinciden !', 'overlay');
                return;
            }
            postString = "&userId="+userId+"&userName="+userName+"&userLastname="+userLastname+"&userCi="+userCi+"&userGender="+userGender+"&userBirthDate="+userBirthDate+"&userNationality="+userNationality+"&userEmail="+userEmail+"&userPhoneNumber="+userPhoneNumber+"&userType="+userType+"&userPassword="+userPassword_1;            
    }else if(what_ === 'route'){
        var routeId = document.getElementById('routeId').value;
        var planeId = document.getElementById('planeId').value;
        var pilotCi = document.getElementById('pilotCi').value;
        var routeDestiny = document.getElementById('routeDestiny').value;
        var routeDepartureDateTime = document.getElementById('routeDepartureDateTime').value;
        var routeDepartureSidewalk = document.getElementById('routeDepartureSidewalk').value;
        var routePrice = document.getElementById('routePrice').value;
        postString = "&routeId="+routeId+"&planeId="+planeId+"&pilotCi="+pilotCi+"&routeDestiny="+routeDestiny+"&routeDepartureDateTime="+routeDepartureDateTime+"&routeDepartureSidewalk="+routeDepartureSidewalk+"&routePrice="+routePrice;
    }else if(what_ === 'plane'){
        var planeId = document.getElementById('planeId').value;
        var planeBrand = document.getElementById('planeBrand').value;
        var planeModel = document.getElementById('planeModel').value;
        var planeCapacity = document.getElementById('planeCapacity').value;
        var planeObservations = document.getElementById('planeObservations').value;
        postString = "&planeId="+planeId+"&planeBrand="+planeBrand+"&planeModel="+planeModel+"&planeCapacity="+planeCapacity+"&planeObservations="+planeObservations;
    }else if(what_ ==='pilot'){
        var pilotCi = document.getElementById('pilotCi').value;
        var pilotName = document.getElementById('pilotName').value;
        var pilotLastName = document.getElementById('pilotLastName').value;
        var pilotBirthDate = document.getElementById('pilotBirthDate').value;
        var pilotNationality = document.getElementById('pilotNationality').value;
        var pilotEmail = document.getElementById('pilotEmail').value;
        var pilotPhoneNumber = document.getElementById('pilotPhoneNumber').value;
        postString = "&pilotCi="+pilotCi+"&pilotName="+pilotName+"&pilotLastName="+pilotLastName+"&pilotBirthDate="+pilotBirthDate+"&pilotNationality="+pilotNationality+"&pilotEmail="+pilotEmail+"&pilotPhoneNumber="+pilotPhoneNumber;
    }
    if(postString !== ""){
        waitingOverlay(true, 'overlay');
        var xmlhttp = get_XmlHttp();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
            {
                state=xmlhttp.responseText;
                waitingOverlay(false, 'overlay');
                if (state === 'ok'){
                    alert ('Crear','¡ Registro creado exitosamente. !', 'overlay');
                    getForm(currentForm);
                }else{
                    alert ('Crear : ERROR',state + '\n\nRevisa e intenta nuevamente', 'overlay');
                }
            }
        };
        xmlhttp.open('POST','',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send('action=create&what='+what_+postString);
    }
}

function lookUp_(what_){
    var filter_ = document.getElementById("filter").value;
    filter_ = (filter_ === defaultFilterValue)? '' : filter_;
    document.getElementById("searchResult").innerHTML = waiting;
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            document.getElementById("searchResult").innerHTML=xmlhttp.responseText;
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send('action=lookUp&what='+what_+'&filter='+filter_);
}

function print_(type_, filter_, what_){
    var filter__ = (filter_ === defaultFilterValue) ? '':filter_;
    var overlay = document.getElementById('overlay');
    overlay.innerHTML = '<div class="waitingOverlay"></div>';
    overlay.style.visibility = 'visible';
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            overlay.innerHTML = windowOverlay('Imprimir',xmlhttp.responseText,'overlay',false);
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("action=print&what="+what_+"&type="+type_+"&filter="+filter__);
}

function delete_(what_, id_){
    var state;
    if( !confirm('¿ Seguro deseas eliminar este registro ['+id_+']?')){ return; }
    waitingOverlay(true, 'overlay');
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            state=xmlhttp.responseText;
            waitingOverlay(false, 'overlay');
            if (state === 'ok'){
                alert ('Eliminar','¡ Registro eliminado exitosamente !', 'overlay');
                lookUp_(what_);
            }else alert ('Eliminar : ERROR',state + '\n\nRevisa e intenta nuevamente', 'overlay');
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send('action=delete&what='+what_+'&id='+id_);
}

function passwordsComparison(){
    var pswd1= document.getElementById('userPassword_1').value;
    var pswd2= document.getElementById('userPassword_2').value;
    if ((pswd1 === '') && (pswd2 === '')){
        document.getElementById('passwordsComparison').innerHTML = '<div align="center" class="invalidValue">Ingresa una contraseña.</div>';
    }else{
        if (pswd1 !== pswd2){
            document.getElementById('passwordsComparison').innerHTML = '<div align="center" class="invalidValue">Las contraseñas no coinciden.</div>';
        }else{
            document.getElementById('passwordsComparison').innerHTML = '<div align="center" class="validValue">Contraseña valida.</div>';
        }
    }
}

function modify_(what_, id_){
    var overlay = document.getElementById('overlay');
    overlay.innerHTML = '<div class="waitingOverlay"></div>';
    overlay.style.visibility = 'visible';
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            overlay.innerHTML = windowOverlay('Modificar',xmlhttp.responseText,'overlay',true);
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("action=modify&what="+what_+"&id="+id_);
}

function saveChanges_(what_){
    var postString = "";
    var state;
    if(what_ === 'user'){
        var profile = document.getElementById('profile').value;
        var userId = document.getElementById('userId').value;
        var userName = document.getElementById('userName').value;
        var userLastname = document.getElementById('userLastname').value;
        var userGender = document.getElementById('userGender').value;
        //var userBirthDate = document.getElementById('userBirthDate').value;
        var userNationality = document.getElementById('userNationality').value;
        var userEmail = document.getElementById('userEmail').value;
        var userPhoneNumber = document.getElementById('userPhoneNumber').value;
        var userType = document.getElementById('userType').value;
        var userPassword_1 = document.getElementById('userPassword_1').value;
        var userPassword_2 = document.getElementById('userPassword_2').value;
        if (userPassword_1 !== userPassword_2){
            alert ('Modificar Usuario','¡ Las contraseñas no coinciden !', 'innerOverlay');
            return;
        }
        postString = "&profile="+profile+"&userId="+userId+"&userName="+userName+"&userLastname="+userLastname+"&userGender="+userGender+"&userBirthDate&userNationality="+userNationality+"&userEmail="+userEmail+"&userPhoneNumber="+userPhoneNumber+"&userType="+userType+"&userPassword="+userPassword_1;
    }else if(what_ === 'route'){
        var routeId = document.getElementById('routeId').value;
        try {var routeDepartureSidewalk = document.getElementById('routeDepartureSidewalk').value;}catch(ex){var routeDepartureSidewalk = 0;}
        try {var routePrice = document.getElementById('routePrice').value;}catch(ex){ var routePrice = 0;}
        postString = "&routeId="+routeId+"&routeDepartureSidewalk="+routeDepartureSidewalk+"&routePrice="+routePrice;
    }else if(what_ === 'plane'){
        var planeId = document.getElementById('planeId').value;
        var planeObservations = document.getElementById('planeObservations').value;
        postString = "&planeId="+planeId+"&planeObservations="+planeObservations;
    }else if(what_ === 'pilot'){
        var pilotCi = document.getElementById('pilotCi').value;
        var pilotName = document.getElementById('pilotName').value;
        var pilotLastName = document.getElementById('pilotLastName').value;
        var pilotNationality = document.getElementById('pilotNationality').value;
        var pilotEmail = document.getElementById('pilotEmail').value;
        var pilotPhoneNumber = document.getElementById('pilotPhoneNumber').value;
        postString= "&pilotCi="+pilotCi+"&pilotName="+pilotName+"&pilotLastName="+pilotLastName+"&pilotNationality="+pilotNationality+"&pilotEmail="+pilotEmail+"&pilotPhoneNumber="+pilotPhoneNumber;
    }
    if(postString !== ""){
        waitingOverlay(true, 'innerOverlay');
        var xmlhttp = get_XmlHttp();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
            {
                state=xmlhttp.responseText;
                waitingOverlay(false, 'innerOverlay');
                if (state === 'ok'){
                    alert ('Modificar','¡ Registro actualizado exitosamente !', 'innerOverlay');
                    if(currentForm === 2 ) lookUp_('user');
                    else if(currentForm === 4) lookUp_('route');
                    else if(currentForm === 6) lookUp_('plane');
                    else if(currentForm === 8) lookUp_('pilot');
                }else alert ('Modificar : ERROR',state + '\n\nRevisa e intenta nuevamente', 'innerOverlay');
            }
        };
        xmlhttp.open('POST','',true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send('action=saveChanges&what='+what_+postString);
    }
}

function newInvoice_(reservationId){
    var overlay = document.getElementById('overlay');
    overlay.innerHTML = '<div class="waitingOverlay"></div>';
    overlay.style.visibility = 'visible';
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            overlay.innerHTML = windowOverlay('Facturar',xmlhttp.responseText,'overlay',true);
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send('action=new_invoice&what=invoice&reservationId='+reservationId);
}

function createInvoice_(){
    var state = '';
    var bankTichet = document.getElementById('bankTichet').value;
    var idReservation = document.getElementById('idReservation').value;
    var postString = "&bankTichet="+bankTichet+"&idReservation="+idReservation;
    waitingOverlay(true, 'innerOverlay');
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            state=xmlhttp.responseText;
            waitingOverlay(false, 'innerOverlay');
            if (state === 'ok'){
                alert ('Facturar','¡ Factura generada satisfactoriamente !', 'innerOverlay');
                lookUp_('reservation');
            }else alert ('Modificar : ERROR',state + '\n\nRevisa e intenta nuevamente', 'innerOverlay');
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send('action=create&what=invoice'+postString);
}

function edit_profile(id_){
    var overlay = document.getElementById('overlay');
    overlay.innerHTML = '<div class="waitingOverlay"></div>';
    overlay.style.visibility = 'visible';
    var xmlhttp = get_XmlHttp();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            overlay.innerHTML = windowOverlay('Modificar perfil de usuario: '+id_,xmlhttp.responseText,'overlay',true);
        }
    };
    xmlhttp.open('POST','',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("action=edit_profile&what=user&id="+id_);
}