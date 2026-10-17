//Date for updating cards
var today=new Date();
var day=today.getDate();
var month=today.getMonth()+1;
var year=today.getFullYear();
var sToday = year+"-"+month+"-"+day;

$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(document).ajaxSend(function (event, request, settings) {
        $("#PopupMask").show();
    });

    $(document).ajaxComplete(function (event, request, settings) {
        $("#PopupMask").hide();
    });

    //Toutes les dates de cette classe ont un date picker
    if ($.fn.datepicker) $(".jqdate").datepicker({"helper":"datePickerup","jQueryParams":{"dateFormat":"yyyy-mm-dd"},"options":[]});


    //Detection de la touche entree
    $("input.tr").keydown(function (event) {
        /*
        if ( event.which == 13 ) {
            event.preventDefault();
            document.getElementById('formsearch').submit();
        }
        */

        //Remplace les virgules par des points
        if (event.keyCode == 188) {
            event.preventDefault();
            $(this).val($(this).val() + '.');
        }
        //Supprime les espaces
        if (event.keyCode == 32) {
            event.preventDefault();
        }
    });


});

/* Formate les chiffres correctement ex:1000->1 000.00 */
function format(num)
{
    var n = num.toFixed(2).toString(), p = n.indexOf('.');
    return n.replace(/\d(?=(?:\d{3})+(?:\.|$))/g, function ($0, i) {
        return p<0 || i<p ? ($0+' ') : $0;
    });
}

/* Formate les chiffres correctement ex:1 000->1000.00 */
function numformat(num)
{
    return num.toString().replace(' ','');
}


/**
 * Shuffles array in place. ES6 version
 * @param {Array} a items An array containing the items.
 */
function shuffle(a)
{
    for (let i = a.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [a[i], a[j]] = [a[j], a[i]];
    }
}

////////////////////////////////////////////////////////
//Edition des cartes

/* Nombre de mots saisis par jeu (2 = Brainstorm, 3 = Taboo, 4 = Loup Garou : extension) */
var CARD_WORDS_BY_GAME = {"2": 10, "3": 5, "4": 1};

/* Description actuelle de la carte, sous forme d'objet (vide si absente ou invalide) */
function getDescriptionObject()
{
    try {
        var oItem = JSON.parse($("#description").val());
        if (oItem !== null && typeof oItem === "object" && !Array.isArray(oItem)) {
            return oItem;
        }
    } catch (e) {
    }
    return {};
}

function setDescription()
{
    var sGame = $("#game_id").val();
    var iNbWords = CARD_WORDS_BY_GAME[sGame] || 0;

    if (sGame == "4") {//Loup Garou
        var oItem = getDescriptionObject();
        oItem["word1"] = $("#description1").val() || oItem["word1"] || "Standard";
        if (oItem["description"] === undefined) {
            oItem["description"] = "";
        }
        if (oItem["persons"] === undefined) {
            oItem["persons"] = "";
        }
        $("#description").val(JSON.stringify(oItem));
    } else if (iNbWords > 0) {
        var oItem = getDescriptionObject();
        for (var k = 1; k <= iNbWords; k++) {
            oItem["word" + k] = $("#description" + k).val();
        }
        $("#description").val(JSON.stringify(oItem));
    } else {//Timesup, Pictionary... : pas de mots
        $("#description").val("[]");
    }
    return true;
}

function refreshDescription()
{
    var sGame = $("#game_id").val();
    var iNbWords = CARD_WORDS_BY_GAME[sGame] || 0;

    if (iNbWords == 0) {
        $("#blocdescription").hide();
        return;
    }

    $("#blocdescription").show();
    $(".myword").hide();
    var oItem = getDescriptionObject();
    for (var k = 1; k <= iNbWords; k++) {
        $("#description" + k).val(oItem["word" + k] || "").show();
    }
}

function biography(sPerson)
{
    var re = / /gi;
    return encodeURI(sPerson.replace(re, "_"));
}



function setCookie(name,value,days)
{
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name)
{
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1,c.length);
        }
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length,c.length);
        }
    }
    return null;
}
function eraseCookie(name)
{
    document.cookie = name+'=; Max-Age=-99999999; path=/';
}

////////////////////////////////////////////////////////
//Outils communs aux jeux

/* Echappe un texte pour l'inserer dans du HTML */
function escapeHtml(sText)
{
    return String(sText == null ? "" : sText)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/* Mots d'une carte (word1..wordN) ; une description vide ou invalide donne une liste vide */
function getCardWords(sDescription, iMax)
{
    var oItem = null;
    try {
        oItem = JSON.parse(sDescription);
    } catch (e) {
        oItem = null;
    }
    var arrWords = [];
    if (oItem !== null && typeof oItem === "object" && !Array.isArray(oItem)) {
        for (var k = 1; k <= iMax; k++) {
            var sWord = oItem["word" + k];
            if (typeof sWord === "string" && sWord.trim() !== "") {
                arrWords.push(sWord);
            }
        }
    }
    return arrWords;
}

/* Joue un son sans erreur si le navigateur bloque la lecture automatique */
function playSound(sUrl)
{
    try {
        var promise = new Audio(sUrl).play();
        if (promise !== undefined) {
            promise.catch(function () {});
        }
    } catch (e) {
    }
}

/* Lit toutes les cartes d'un jeu dans IndexedDB (remplie par la page d'accueil).
 * Si elles n'y sont pas (acces direct, stockage efface), retour a l'accueil pour les charger. */
function loadGameCards(sStore, sErrorMessage, fnOnCards)
{
    var oIndexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || window.shimIndexedDB;
    var fnBackHome = function () {
        eraseCookie("getcard");
        alert(sErrorMessage);
        window.location.href = "/";
    };

    var open = oIndexedDB.open("MyGames", 1);
    open.onerror = fnBackHome;
    open.onsuccess = function () {
        var db = open.result;
        if (!db.objectStoreNames.contains(sStore)) {
            db.close();
            fnBackHome();
            return;
        }

        var arrCards = [];
        var tx = db.transaction(sStore);
        tx.objectStore(sStore).openCursor().onsuccess = function (event) {
            var cursor = event.target.result;
            if (cursor) {
                arrCards.push(cursor.value);
                cursor.continue();
            } else if (arrCards.length == 0) {
                fnBackHome();
            } else {
                fnOnCards(arrCards);
            }
        };
        tx.oncomplete = function () {
            db.close();
        };
    };
}

/* Historique des cartes jouees sur ce navigateur : id => date (ms) de leur derniere partie.
 * Garde dans localStorage, a part des cartes : il survit a leur mise a jour depuis le site. */
var PLAYED_CARDS_KEY = "playedCards";

function getPlayedCards()
{
    try {
        var oPlayed = JSON.parse(localStorage.getItem(PLAYED_CARDS_KEY));
        return oPlayed !== null && typeof oPlayed === "object" ? oPlayed : {};
    } catch (e) {
        return {};
    }
}

function markCardsPlayed(arrIds)
{
    var oPlayed = getPlayedCards();
    var iNow = Date.now();
    arrIds.forEach(function (iId) {
        oPlayed[iId] = iNow;
    });
    try {
        localStorage.setItem(PLAYED_CARDS_KEY, JSON.stringify(oPlayed));
    } catch (e) {
    }
}

/* Copie melangee des cartes : d'abord celles jamais jouees, puis de la plus anciennement jouee a la plus recente */
function leastPlayedFirst(arrCards)
{
    var oPlayed = getPlayedCards();
    var arrSorted = arrCards.slice();
    shuffle(arrSorted);
    // Tri stable : le melange est garde entre cartes jouees au meme moment
    return arrSorted.sort(function (a, b) {
        return (oPlayed[a.id] || 0) - (oPlayed[b.id] || 0);
    });
}

/* Numeros (1..n) des equipes ayant le meilleur score, parmi les equipes en jeu seulement */
function getWinners(arrScores)
{
    var iMax = Math.max.apply(null, arrScores);
    var arrWinners = [];
    arrScores.forEach(function (iScore, i) {
        if (iScore == iMax) {
            arrWinners.push(i + 1);
        }
    });
    return arrWinners;
}
