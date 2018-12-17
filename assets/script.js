function validateForm(form){
	var isValid = true;
	for(var i=0; i<form.elements.length; i++){
		if(form.elements[i].name == 'url[]' && isValid){
			isValid = isValidUrl(form.elements[i].value);
			if(!isValid){
				alert('Il valore "'+form.elements[i].value+'" non è una URL valida.');
			}
		}
	}
	return isValid;
}
function isValidUrl(string) {
	if (string && string.length > 1 && string.slice(0, 2) == '//') {
		string = 'http:' + string; //dummy protocol so that URL works
	}
	try {
		var url = new URL(string);
		return url.hostname && url.hostname.match(/^([a-z0-9])(([a-z0-9-]{1,61})?[a-z0-9]{1})?(\.[a-z0-9](([a-z0-9-]{1,61})?[a-z0-9]{1})?)?(\.[a-zA-Z]{2,4})+$/) ? true : false;
	} catch (_) {
		return false;
	}
}

document.addEventListener('DOMContentLoaded', function(){
	var inputs = document.getElementsByTagName('input');
	for(var i=0; i<inputs.length; i++){
		if(inputs[i].id.indexOf('url_') != -1){
			inputs[i].addEventListener("keyup", actionKeyUp);
		}
	}
}, false);

function actionKeyUp(object){
	countCharacters(object);
	wordPositions(object);
}

function countCharacters(object){
	var id = object.target.id,
		value = object.target.value,
		tot = 0,
		labels = document.getElementsByTagName('label');

	for(var i=0; i<labels.length; i++){
		if(labels[i].htmlFor == id){
			labels[i].children[0].children[0].innerHTML = value.length;
			tot += value.length;
		}
		else if(labels[i].htmlFor.indexOf('url_') != -1){
			tot+= parseInt(labels[i].children[0].children[0].innerHTML);
		}
	}
	
	document.getElementById('totchars').children[0].innerHTML = tot;
}

function wordPositions(object){
	var id = object.target.id,
		value = object.target.value,
		alphabet = 'abcdefghijklmnopqrstwxyz',
		labels = document.getElementsByTagName('label'),
		composition = [];
		
	for(var i=0; i<labels.length; i++){
		if(labels[i].htmlFor == id){
			if(value.length > 0){
				for(var e=0; e<value.length; e++){
					position = alphabet.indexOf(value[e].toLowerCase()) + 1;
					if(position > 0){
						composition.push(position);
					}
				}
			}
			else{
				composition.push('');
			}
			
			labels[i].children[0].children[1].innerHTML = composition.join(' + ');
		}
	}
}