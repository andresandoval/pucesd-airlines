/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */

function setCalendarDaysValues(calendarObjectName){
    var daysOfMonth = [31,28,31,30,31,30,31,31,30,31,30,31];
    var year = document.getElementById(calendarObjectName+'_year').value;
    var month = document.getElementById(calendarObjectName+'_month').value;
    var day = document.getElementById(calendarObjectName+'_day');
    var lDay;
    for (i=0 ; i< day.options.length; i++){
        day.options[i]=null;
    }
    day.options[0] = new Option('-- dia --','',false,false);
    if ((year === '') || (month === '')){ return null; }
    
    daysOfMonth[1] =((year%4) === 0)? 29 : 28;
    for(j=1; j <= daysOfMonth[month-1]; j++){
        lDay = (j<10)? '0'+j : j;
        day.options[j] = new Option(lDay,lDay,false,false);
    }
    
}

function setCalendarValue(calendarObjectName,calendarTime){
    var year = document.getElementById(calendarObjectName+'_year').value;
    var month = document.getElementById(calendarObjectName+'_month').value;
    var day = document.getElementById(calendarObjectName+'_day').value;
    if (calendarTime){
         var hour = document.getElementById(calendarObjectName+'_hour').value;
         var minute = document.getElementById(calendarObjectName+'_minute').value;
         document.getElementById(calendarObjectName).value = ((year==='') || (month==='') || (day==='') || (hour==='') || (minute===''))? '': year+'-'+month+'-'+day+' '+hour+':'+minute+':00';
     }else{
         document.getElementById(calendarObjectName).value = ((year==='') || (month==='') || (day===''))? '': year+'-'+month+'-'+day;
     }
}
function getCalendarHelp(calendarTime){
    var msg='\tCALENDARIO<br/>Debes seleccionar los valores en este orden:<br/><br/>-Año<br/>-Mes<br/>-Dia<br/>';
    msg +=(calendarTime)? '-Hora<br/>-Minuto<br/>':'';
    msg += '<br/>Desarrollado por Andres Sandoval®';
    var overlay = document.getElementById('overlay');
    overlay.innerHTML = windowOverlay('Calendario', msg, 'overlay', false);
    overlay.style.visibility = 'visible';
}


function setCalendarDefaultValue(){
    
}
