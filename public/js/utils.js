//Date for updating cards
var today=new Date();
var day=today.getDate();
var month=today.getMonth()+1;
var year=today.getFullYear();
var sToday = year+"-"+month+"-"+day;

$(document).ready(function() {
	
	$.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
	
	$(document).ajaxSend(function(event, request, settings) {
		$("#PopupMask").show();
	});

	$(document).ajaxComplete(function(event, request, settings) {
		$("#PopupMask").hide();
	});

	//Toutes les dates de cette classe ont un date picker
	$(".jqdate").datepicker({"helper":"datePickerup","jQueryParams":{"dateFormat":"yyyy-mm-dd"},"options":[]});
	
	
	//Detection de la touche entree
	$("input.tr").keydown(function(event) {
		/*
		if ( event.which == 13 ) {
			event.preventDefault();
			document.getElementById('formsearch').submit();
		}  
		*/
		
		//Remplace les virgules par des points
		if(event.keyCode == 188){
			event.preventDefault();
			$(this).val($(this).val() + '.');
		}
		//Supprime les espaces
		if(event.keyCode == 32){
			event.preventDefault();
		}
	});
	
	
});

/* Formate les chiffres correctement ex:1000->1 000.00 */
function format(num){
    var n = num.toFixed(2).toString(), p = n.indexOf('.');
    return n.replace(/\d(?=(?:\d{3})+(?:\.|$))/g, function($0, i){
        return p<0 || i<p ? ($0+' ') : $0;
    });
}

/* Formate les chiffres correctement ex:1 000->1000.00 */
function numformat(num){
    return num.toString().replace(' ','');
}


/**
 * Shuffles array in place. ES6 version
 * @param {Array} a items An array containing the items.
 */
function shuffle(a) {
	for (let i = a.length - 1; i > 0; i--) {
		const j = Math.floor(Math.random() * (i + 1));
		[a[i], a[j]] = [a[j], a[i]];
	}
}

////////////////////////////////////////////////////////
//Edition des cartes
function setDescription(){
	switch ($("#game_id").val()){
		case "1":
			$("#description").val("[]");
			break;
			
		case "2":
			var oItem = JSON.parse($("#description").html());
			for (var k=1; k<=10; k++){
				oItem["word"+k] = $("#description"+k).val();
			}
			$("#description").html(JSON.stringify(oItem));
			break;
			
		case "3":
			var oItem = JSON.parse($("#description").html());
			for (var k=1; k<=5; k++){
				oItem["word"+k] = $("#description"+k).val();
			}
			$("#description").html(JSON.stringify(oItem));
			break;
			
		case "4":
			var oItem = JSON.parse($("#description").html());
			for (var k=1; k<=1; k++){
				oItem["word"+k] = $("#description"+k).val();
			}
			$("#description").html(JSON.stringify(oItem));
			break;
	}
}

function refreshDescription(){
	switch ($("#game_id").val()){
		case "1":
			$("#blocdescription").hide();
			break;
			
		case "2":
			if ($("#description").html() != ""){
				var oItem = JSON.parse($("#description").html());
				$("#blocdescription").show();
				$(".myword").hide();
				for (var k=1; k<=10; k++){
					$("#description"+k).show();
					$("#description"+k).val(oItem["word"+k]);
				}
			}
			break;
			
		case "3":
			if ($("#description").html() != ""){
				var oItem = JSON.parse($("#description").html());
				$("#blocdescription").show();
				$(".myword").hide();
				for (var k=1; k<=5; k++){
					$("#description"+k).show();
					$("#description"+k).val(oItem["word"+k]);
				}
			}
			break;
			
		case "4":
			if ($("#description").html() != ""){
				var oItem = JSON.parse($("#description").html());
				$("#blocdescription").show();
				$(".myword").hide();
				for (var k=1; k<=1; k++){
					$("#description"+k).show();
					$("#description"+k).val(oItem["word"+k]);
				}
			}
			break;
	}
}

function biography(sPerson){
	var re = / /gi;
	return encodeURI(sPerson.replace(re, "_"));
}



function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function eraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999;';  
}