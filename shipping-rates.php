<?php 
/**
 * 	Plane icon theme
 *	Copyright (C) 2017  wfpaisa
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <https://www.gnu.org/licenses/gpl-3.0.en.html>.
 *
 * @author    @wfpaisa
 * @copyright 2017 Plane icon theme
 */

// Notas
// Pedir a tcc activar cuenta de paquetería y mensajería
// Pedir la clave
// 


$url = "http://clientes.tcc.com.co/servicios/liquidacionacuerdos.asmx?wsdl";

/* 
 * Initialize webservice TCC WSDL 
 */
$client = new SoapClient($url);


/* 
 * Get TCC functions and types functions 
 */
// print_r($client->__getFunctions());
// print_r($client->__getTypes()); 

print_r('<br>');

$params = array(
	'Clave' => 'MedClave',					// Clave de TCC (solicitar)
	'Liquidacion' => array(
 		'tipoenvio' => '0',
 		'fecharemesa' => date('Y-m-d'),
 		'idunidadestrategicanegocio' => '2',// 2 Mensajería, 1 Paquetería, - Mensajeria: <= 5kg, Paquetería: >= 5kg
		'cuenta' => '5342200', 				// Cuenta de mensajería: 5342200, Cuenta de Paquetería: 1331900, (solicitar)
		'idciudadorigen' => '05001000', 	// Medellin (ver en ./cities/index.html) el listado
		'idciudaddestino' => '11001000', 	// Bogota (ver en ./cities/index.html) el listado
		'valormercancia' => '10000',
		'recogida' => '0', 					// Indica si la mercancía se recoge en origen True=si, False= no
		'traecd' => '0', 					// Indica si el cliente lleva el paquete hasta TCC True=si, False=no
		'recogecd' => '0',					// Indica si el remitente recoge el paquete en TCC True=si, False=no
		'boomerang' => '0',
		'unidades' => array(
			array(
				'numerounidades' => '1',
				'pesoreal' => '1',			// 1 Kilo
				'tipoempaque'=>'1',
				'pesovolumen'=>'1', 		// Pasar a metros= alto * largo * ancho * 400 
				'alto'=>'10', 				// CMs
				'largo'=>'10',				// CMs
				'ancho'=>'10',				// CMs
			)
		)
	),
	'Respuesta'=>'0',
	'Mensaje'=>'0',

);

/* 
 * Invoke webservice method 
 */
$response_raw = $client->__soapCall("consultarliquidacion", array($params));

$res = $response_raw->consultarliquidacionResult;

if( $res->respuesta->codigo == '-1'){
	print( '<b>Mensaje:</b> ' . $res->respuesta->mensaje );
}else{
	print( '<b>Mensaje:</b> ' . $res->respuesta->mensaje . '<br>');
	print( '<b>Kilos:</b> ' . $res->total->totalpesofacturado . '<br>');
	print( '<b>Flete:</b> ' . $res->subtotales->ConceptoAgrupado[0]->valor . '<br>');
	print( '<b>Flete manejo:</b> ' . round($res->subtotales->ConceptoAgrupado[1]->valor,0) . '<br>');
	// print( '<b>Valor envío:</b> ' . $res->total->totaldespacho . '<br>');
	print( '<b>Valor envío:</b> ' . (round((float)$res->total->totaldespacho,0)) . '<br>');
}

print('<br> <br><smal style="color:gray">Para ver el objeto retornado ver la consola javascript (Alt + F12)</small>');

print('<script>');
print('console.log('. json_encode( $res ) .')');
print('</script>');

// print_r(round((float)$responseXml->valortotal / 100) *100);


?>
