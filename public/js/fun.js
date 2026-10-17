////////////////////////////////////////////////////////
// Effets ludiques des jeux : ambiance de fond, feux d'artifice, confettis, sons et petites animations.
// Meme comportement que l'application Android.

var Fun = (function () {
    var AMBIANCES = {
        timesup: {symbols: ["⏳", "⌛", "⏰", "🎭"]},
        brainstorm: {symbols: ["💡", "🧠", "❓", "✨"]},
        taboo: {symbols: ["🤫", "🚫", "🙊", "💬"]},
        pictionary: {symbols: ["✏️", "🎨", "🖍️", "🖌️"]},
        loupgarou: {night: true},
        home: {symbols: ["🎲", "⏳", "💡", "🤫", "🎨", "🐺", "🃏"]}
    };
    var COLORS = ["#6366f1", "#ffc107", "#00bcd4", "#8bc34a", "#ff5722", "#9c27b0", "#ffeb3b", "#ffffff"];
    var LAST_SECONDS = 5;

    function random(iMin, iMax) {
        return iMin + Math.random() * (iMax - iMin);
    }

    function pick(arr) {
        return arr[Math.floor(Math.random() * arr.length)];
    }

    /* Sons propres aux effets (public/sounds/*.wav) */
    function sound(sName) {
        playSound("/sounds/" + sName + ".wav");
    }

    /* Relance une animation CSS sur un element, meme si elle vient d'etre jouee */
    function replay(oElement, sClass) {
        var o = $(oElement);
        o.removeClass(sClass);
        void (o[0] && o[0].getBoundingClientRect()); // force le recalcul pour repartir de zero
        o.addClass(sClass);
    }

    /* Fond du jeu : degrade, symboles qui flottent, ou ciel etoile pour le Loup-Garou */
    function ambiance(sGame) {
        var oConf = AMBIANCES[sGame];
        if (!oConf) {
            return;
        }
        $("body").addClass("fun fun-" + sGame);
        var oLayer = $("<div class='fun-ambiance' aria-hidden='true'></div>");
        if (oConf.night) {
            for (var i = 0; i < 70; i++) {
                $("<span class='fun-star'></span>").css({
                    left: random(0, 100) + "%",
                    top: random(0, 85) + "%",
                    animationDuration: random(1.5, 4) + "s",
                    animationDelay: -random(0, 4) + "s"
                }).appendTo(oLayer);
            }
            oLayer.append("<div class='fun-moon'></div>");
        } else {
            for (var k = 0; k < 10; k++) {
                $("<span class='fun-floater'></span>").text(oConf.symbols[k % oConf.symbols.length]).css({
                    left: random(0, 92) + "%",
                    fontSize: random(28, 48) + "px",
                    animationDuration: random(14, 26) + "s",
                    animationDelay: -random(0, 26) + "s"
                }).appendTo(oLayer);
            }
        }
        $(".page-header .mask").first().after(oLayer);
    }

    /* Tic et pulsation du chrono sur les dernieres secondes */
    function tick(iTimeLeft) {
        if (iTimeLeft < 1 || iTimeLeft > LAST_SECONDS) {
            return;
        }
        sound("tick");
        var oChrono = $("#chrono");
        var oTarget = oChrono.closest(".timer");
        replay(oTarget.length ? oTarget : oChrono, "fun-pulse");
    }

    /* Carte qui arrive en glissant avec un rebond */
    function cardIn(sSelector) {
        replay(sSelector || "#cardname", "fun-card-in");
    }

    /* Trophee anime au-dessus du texte du gagnant */
    function trophy(arrWinners, sBefore) {
        $(".fun-trophy").remove();
        $("<div class='fun-trophy' aria-hidden='true'></div>")
            .text(arrWinners.length > 1 ? "🤝" : "🏆")
            .insertBefore(sBefore || "#finishset");
    }

    ////////////////////////////////////////////////////////
    // Feux d'artifice et confettis (canvas plein ecran, ne capte pas les clics)

    var GRAVITY = 320;
    var oCanvas = null;
    var oContext = null;
    var arrParticles = [];
    var bRunning = false;
    var fEnd = 0;
    var bFireworks = false;
    var bConfetti = false;
    var fNextRocket = 0;
    var fConfettiBudget = 0;
    var fLast = 0;
    var fLastBoom = 0;

    function ensureCanvas() {
        if (oCanvas) {
            return;
        }
        oCanvas = document.createElement("canvas");
        oCanvas.className = "fun-canvas";
        document.body.appendChild(oCanvas);
        oContext = oCanvas.getContext("2d");
        resize();
        window.addEventListener("resize", resize);
    }

    function resize() {
        var fRatio = window.devicePixelRatio || 1;
        oCanvas.width = window.innerWidth * fRatio;
        oCanvas.height = window.innerHeight * fRatio;
        oContext.setTransform(fRatio, 0, 0, fRatio, 0, 0);
    }

    function party(iDurationMs, bWithFireworks, bWithConfetti) {
        ensureCanvas();
        var fNow = performance.now();
        fEnd = Math.max(fEnd, fNow + iDurationMs);
        bFireworks = bFireworks || bWithFireworks;
        bConfetti = bConfetti || bWithConfetti;
        if (!bRunning) {
            bRunning = true;
            fLast = 0;
            fNextRocket = 0;
            requestAnimationFrame(frame);
        }
    }

    function frame(fNow) {
        var fDt = fLast ? Math.min(0.05, (fNow - fLast) / 1000) : 0;
        fLast = fNow;
        var w = window.innerWidth;
        var h = window.innerHeight;
        var bActive = fNow < fEnd;

        if (bActive && bFireworks && fNow >= fNextRocket) {
            var fRise = h * random(0.35, 0.7);
            arrParticles.push({kind: "rocket", x: w * random(0.15, 0.85), y: h, vx: random(-30, 30),
                vy: -Math.sqrt(2 * GRAVITY * fRise), color: pick(COLORS), age: 0, life: 10});
            fNextRocket = fNow + random(350, 800);
        }
        if (bActive && bConfetti) {
            fConfettiBudget += fDt * 70;
            while (fConfettiBudget >= 1) {
                fConfettiBudget--;
                arrParticles.push({kind: "confetti", x: w * Math.random(), y: -10, vx: 0, vy: random(90, 180),
                    color: pick(COLORS), age: 0, life: 10, phase: Math.random() * 6.28});
            }
        }

        update(fDt, h, fNow);
        draw(w, h);

        if (!bActive && arrParticles.length == 0) {
            bRunning = false;
            bFireworks = false;
            bConfetti = false;
            oContext.clearRect(0, 0, w, h);
            return;
        }
        requestAnimationFrame(frame);
    }

    function update(fDt, h, fNow) {
        var arrBorn = [];
        arrParticles = arrParticles.filter(function (p) {
            p.age += fDt;
            if (p.kind == "rocket") {
                p.vy += GRAVITY * fDt;
                if (p.vy >= -25) {
                    explode(p, arrBorn, fNow);
                    return false;
                }
            } else if (p.kind == "spark") {
                p.vy += GRAVITY * 0.35 * fDt;
                var fDrag = 1 - 1.4 * fDt;
                p.vx *= fDrag;
                p.vy *= fDrag;
            } else {
                p.vx = Math.sin(p.age * 2.5 + p.phase) * 40;
            }
            p.x += p.vx * fDt;
            p.y += p.vy * fDt;
            return p.age < p.life && p.y < h + 20;
        });
        arrParticles = arrParticles.concat(arrBorn);
    }

    function explode(oRocket, arrInto, fNow) {
        // Evite un mur de son si plusieurs fusees explosent en meme temps
        if (fNow - fLastBoom > 150) {
            sound("firework");
            fLastBoom = fNow;
        }
        var arrColors = [oRocket.color, pick(COLORS)];
        var iCount = 70;
        for (var i = 0; i < iCount; i++) {
            var fAngle = 2 * Math.PI * i / iCount + Math.random() * 0.1;
            var fSpeed = random(110, 240);
            arrInto.push({kind: "spark", x: oRocket.x, y: oRocket.y, vx: Math.cos(fAngle) * fSpeed,
                vy: Math.sin(fAngle) * fSpeed, color: arrColors[i % 2], age: 0, life: random(1.0, 1.7)});
        }
    }

    function draw(w, h) {
        var c = oContext;
        c.clearRect(0, 0, w, h);
        c.lineCap = "round";
        arrParticles.forEach(function (p) {
            var fAlpha = Math.max(0, Math.min(1, 1 - p.age / p.life));
            if (p.kind == "rocket") {
                c.globalAlpha = 0.5;
                c.strokeStyle = p.color;
                c.lineWidth = 3;
                c.beginPath();
                c.moveTo(p.x, p.y);
                c.lineTo(p.x - p.vx * 0.05, p.y - p.vy * 0.05);
                c.stroke();
                c.globalAlpha = 1;
                c.fillStyle = "#fff";
                c.beginPath();
                c.arc(p.x, p.y, 3, 0, 2 * Math.PI);
                c.fill();
            } else if (p.kind == "spark") {
                c.globalAlpha = fAlpha * 0.6;
                c.strokeStyle = p.color;
                c.lineWidth = 2;
                c.beginPath();
                c.moveTo(p.x, p.y);
                c.lineTo(p.x - p.vx * 0.06, p.y - p.vy * 0.06);
                c.stroke();
                c.globalAlpha = fAlpha;
                c.fillStyle = p.color;
                c.beginPath();
                c.arc(p.x, p.y, 2.5, 0, 2 * Math.PI);
                c.fill();
            } else {
                // Confetti : largeur variable pour l'effet de papier qui tourne
                var fWidth = 8 * Math.max(0.2, Math.abs(Math.cos(p.age * 6 + p.phase)));
                c.globalAlpha = 1;
                c.fillStyle = p.color;
                c.save();
                c.translate(p.x, p.y);
                c.rotate((p.age * 360 + p.phase * 57) * Math.PI / 180);
                c.fillRect(-fWidth / 2, -5, fWidth, 10);
                c.restore();
            }
        });
        c.globalAlpha = 1;
    }

    return {
        ambiance: ambiance,
        sound: sound,
        tick: tick,
        cardIn: cardIn,
        trophy: trophy,
        /* Fin de tour reussie */
        confetti: function () {
            party(2500, false, true);
        },
        /* Fin de partie : fanfare, feu d'artifice, confettis et trophee */
        celebrate: function (arrWinners, sBefore) {
            sound("fanfare");
            if (arrWinners) {
                trophy(arrWinners, sBefore);
            }
            party(7000, true, true);
        }
    };
})();
