// CLOCK SETTINGS
var timeloader = document.getElementById('timeloader');
var seconds = 0;
var alpha = 0;
var timing = 0;
var oClockTimeout = null ;	

function drawClock() {
	if (!bPause){
		alpha++;
		alpha %= 360;
		var pi = Math.PI;
		var r = ( alpha * pi / 180 );
		var x = Math.sin( r ) * 125;
		var y = Math.cos( r ) * - 125;
		var mid = ( alpha > 180 ) ? 1 : 0;
		
		var progress = parseInt($("#progress").val());
		if (progress<=3){
			$("#timeloader").addClass("hurryup");
			if (progress<0){
				y="";
			}
		}
		var anim = 'M 0 0 v -125 A 125 125 1 ' + mid + ' 1 ' +  x  + ' '+  y  + ' z';
		timeloader.setAttribute( 'd', anim );		
	}
	oClockTimeout = setTimeout(drawClock, timing); // Redraw							
}

function initClock(piTimeLimit){
	$("#timeloader").removeClass("hurryup");
	seconds = piTimeLimit;
	alpha = 0;
	endClock();
	timing = (seconds/360 * 1000);
	drawClock();
}

function endClock(){
	if (oClockTimeout != null){
		clearTimeout(oClockTimeout);
	}
}