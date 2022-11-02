/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * Pleins d'exemple sur :
 * https://www.e-monsite.com/forum/vos-trucs-et-astuces/que-tombe-la-neige.html
 * 
 * Fichier issu de :
 * https://circlecube.com/tutorials/snow/
 * 
 * Quelques modifications LC : Ajout de l'angle
 * 
 */

function toRadians(angle) {
    return angle * (Math.PI / 180);
}
function toDegrees(angle) {
    return angle * (180 / Math.PI);
}
function getScrollY() {
    var sy = window.scrollY;  //pas compatible IE
    if (isNaN(sy)) {
        return window.pageYOffset;
    }
    return sy;
}

$(function () {
    var canvas, context, width, height, x, y, radius = 25, clickX, clickY, drag = false;
    var total_dots = 70;
    var fps = 20;

    //canvas = $("#canvas\n\
    var canvas = document.getElementById('canvas-neige');
    context = canvas.getContext("2d");
    var dots = new Array();
    var drag_i = -1;
    var gravity = .05;
    var friction = .98;
    var bounce = -.96;
    var wrap = true;
    var float = true;

    var cos_min = 0.3;

    var imgs = new Array();
    var img1 = new Image();
    var img2 = new Image();
    var img3 = new Image();
    img1.src = "theme/defaut/noel/neige/snowflake_1.png";
    img2.src = "theme/defaut/noel/neige/snowflake_2.png";
    img3.src = "theme/defaut/noel/neige/snowflake_3.png";
    imgs[0] = img1;
    imgs[1] = img2;
    imgs[2] = img3;
    var this_dot = {};
    for (var i = 0; i < total_dots; i++) {
        createDot();
    }
    function createDot(x, y, r, vx, vy) {
        var this_dot = {
            x: typeof (x) != 'undefined' ? x : Math.random() * canvas.width,
            //y: 		typeof(y) != 'undefined' ? y : Math.random()*-canvas.height,
            y: typeof (y) != 'undefined' ? y : Math.random() * canvas.height,
            radius: typeof (r) != 'undefined' ? r : 25,
            scale: Math.floor(20 + (1 + 50 - 20) * Math.random()),
            scale2: 1, //0.2+Math.random()*1,
            angle: Math.random() * 360,
            vx: typeof (vx) != 'undefined' ? vx : Math.random() * 3 - 1,
            vy: typeof (vy) != 'undefined' ? vy : Math.random() * 3,
            src: (dots.length % 3) + 1,
            r: 0,
            vr: 0
        };
        dots.push(this_dot);
    }

    draw();
    /*
     $("#canvas-neige").mousedown(function (event) {
     createDot(event.pageX - this.offsetLeft-25, event.pageY - this.offsetTop-25);
     });
     
     
     $("#canvas-neige").mouseup(function (event) {
     drag = false;
     drag_i = -1;
     });
     */
    function update() {
        for (var i = 0; i < dots.length; i++) {
            if (drag_i != i) {
                var this_dot = dots[i];
                //A REMETTRE
                if (float) {
                    this_dot.vx += Math.random() - .5;
                    this_dot.vy += Math.random() - .5;
                    this_dot.vr += Math.random() * .01 - .005;
                }
                this_dot.vx *= friction;
                this_dot.vy = this_dot.vy * friction + gravity;
                this_dot.x += this_dot.vx;
                this_dot.y += this_dot.vy;
                this_dot.r += this_dot.vr;

                this_dot.angle = this_dot.angle + Math.random() * 5;
                var cos = Math.cos(toRadians(this_dot.angle));

                //permer de réduire le temps ou le flocon est tout plat
                if (cos > 0 && cos < cos_min) {
                    this_dot.angle = this_dot.angle + Math.random() * 5;
                }
                if (cos < 0 && cos > -cos_min) {
                    this_dot.angle = this_dot.angle + Math.random() * 5;
                }

                this_dot.angle = (this_dot.angle % 360);


                if (this_dot.x > canvas.width + this_dot.radius) {
                    this_dot.x -= canvas.width + this_dot.radius * 2;
                    this_dot.vr = 0;
                } else if (this_dot.x < 0 - this_dot.radius) {
                    this_dot.x += canvas.width + this_dot.radius * 2;
                    this_dot.vr = 0;
                }
                //if (this_dot.y > canvas.height + this_dot.radius) {
                //    this_dot.y -= canvas.height + this_dot.radius * 2;
                if (this_dot.y > canvas.height + this_dot.scale * 2) {
                    this_dot.y = -this_dot.scale * 2;
                    this_dot.vr = 0;

                    //réactualise les autres champs (pour augmenter l'effet aléatoire)

                    this_dot.x = Math.random() * canvas.width;

                    this_dot.scale = Math.floor(10 + (1 + 50 - 10) * Math.random());
                    this_dot.scale2 = 1;// 0.2+Math.random()*1;
                    this_dot.angle = Math.random() * 360;
                }

            }
        }
    }
    function draw() {

        //maj du dessin
        context.clearRect(0, 0, canvas.width, canvas.height);
        for (var i = 0; i < dots.length; i++) {
            var src = img1;
            if (dots[i].src == 1) {
            } else if (dots[i].src == 2) {
                src = img2;
            } else {
                src = img3;
            }
            context.save();
            context.translate(dots[i].x + dots[i].scale / 2, dots[i].y + dots[i].scale / 2);
            context.rotate(dots[i].r);
            context.translate(-dots[i].x - dots[i].scale / 2, -dots[i].y - dots[i].scale / 2);
            //context.drawImage(src, dots[i].x, dots[i].y, dots[i].scale, dots[i].scale * dots[i].scale2);
            var cos = Math.cos(toRadians(dots[i].angle));
            //laisse une epaisseur et fait translater selon deltaY
            var deltaY = 0;
            if (cos > 0 && cos < cos_min) {
                deltaY = dots[i].scale * dots[i].scale2 / 2 * (cos - cos_min);
                cos = cos_min;
            }
            if (cos < 0 && cos > -cos_min) {
                deltaY = dots[i].scale * dots[i].scale2 / 2 * (cos + cos_min);
                cos = -cos_min;
            }
            context.drawImage(src, dots[i].x, dots[i].y + deltaY, dots[i].scale, dots[i].scale * dots[i].scale2 * cos);
            context.restore();
        }
    }

    setInterval(function () {

        //on dessine si le canvas est visible

        if (getScrollY() < (canvas.clientTop + canvas.clientHeight)) {
            update();
            draw();
            //console.log(getScrollY());
        }




    }, 1000 / fps );

});