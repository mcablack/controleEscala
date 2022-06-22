<?php 

use Carbon\Carbon;

function formatarData($date,$format){

    return Carbon::parse($date)->format($format);
}
/**   
 *Operador1 = P653652 , Supervisor1 =  P687265 , Coordenador1 = P934843 ;
 *Operador2 = P643227 , Supervisor 2 = P935439 , Coordenador2 = P934843 ;
 */

function operador(){
    return 'P643227';
}
function supervisor(){
    return 'P687265 ';
}
function coordenador(){
    return 'P934843 ';
}













