"use strict";

/* 
 * Get a array of objects and sort from city
 * made for @wfpaisa
 * Colombia Dane municipios
 */

let columnState = "DEPARTAMENTO",
	columnCity	= "DESCRIPCION",
	columnCode 	= "CODIGO DANE",
	columnOrder = "CODIGO SION",
	url			= "./Maestro de geografia.csv";


// Get csv
var getJSON = function(url, callback) {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url, true);
	// xhr.responseType = 'json';
	xhr.onload = function() {
		var status = xhr.status;
		if (status === 200) {
		callback(null, xhr.response);
		} else {
		callback(status, xhr.response);
		}
	};
	xhr.send();
};

// CSV to JSON
function csvJSON(csv){

  var lines=csv.split("\n");

  var result = [];

  var headers=lines[0].split(";");

  for(var i=1;i<lines.length;i++){

	  var obj = {};
	  var currentline=lines[i].split(";");

	  for(var j=0;j<headers.length;j++){
		  obj[headers[j]] = currentline[j];
	  }

	  result.push(obj);

  }
  
  return result; //JSON
}


// Sort objects alphabetically
async function sortObjKeysAlphabetically(obj) {
  return Object.keys(obj).sort((a,b) => a > b).reduce((result, key) => {
    result[key] = obj[key];
	return result;
  }, {});
}


// Proceso el recurso
getJSON( url,
async function(err, data) {
	
	let municipios 	= await csvJSON(data),
		categories 	= {},
		catOrdened 	= {};
		
	// Agrupando
	municipios.forEach(function(municipio,key){
		
		if(!municipio[columnState]) return

		if(!categories[municipio[columnState]]) categories[municipio[columnState]] = [];
		
		// Remove child states

		// 	newObj = municipio;
		// delete newObj.state;
		categories[municipio[columnState]].push({code:municipio[columnCode], city:municipio[columnCity]});
		
	})
	
	let sortMunicipios = await sortObjKeysAlphabetically(categories);
	

	// Ordenando 
	for (var prop in sortMunicipios) {

		// Cada array de cada deparamento
		var ordArr = categories[prop].sort(function(a,b){
			// compara la ciudades
			return a.city.localeCompare(b.city);
		});


		
		// borra y convierto a minusculas y paso a capitalizar
		ordArr.map(function(robj){
			delete robj[columnState];
			
			robj.city = robj.city.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
			return robj;
		})
		
		var stateLowerCase = prop.toLocaleLowerCase();
		catOrdened[stateLowerCase] = ordArr;
		
	}



	// Imprimo en pantalla
	var toString = JSON.stringify(catOrdened, null, "\t");
	
	toString = toString.replace(/\{\n\t\t\t/g,'{');
	toString = toString.replace(/\,\n\t\t\t/g,',');
	toString = toString.replace(/\n\t\t\}/g,'}');

	document.getElementById("render").innerHTML = toString;
	

});