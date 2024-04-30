// Copyright© Aron Särkioja & W3Schools.com to Mercantec, Inc. 2024. All rights reserved.
var canvas = document.getElementById("canvas"),
  ctx = canvas.getContext('2d');
var body = document.getElementById("body");

drawing = true;
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

var stars = [], 
  FPS = 60, 
  numStars = canvas.width / 15,
  mouse = {
    x: 0,
    y: 0
  };  

function initialize() {
  if (drawing) {
    createStars();
    body.addEventListener('mousemove', onMouseMove);
    tick();
  }
}


function createStars() {
  for (var i = 0; i < numStars; i++) {
    stars.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      radius: Math.random() * 1 + 1,
      vx: Math.floor(Math.random() * 50) - 25,
      vy: Math.floor(Math.random() * 50) - 25
    });
  }
}


function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.globalCompositeOperation = "lighter";

  for (var i = 0; i < stars.length; i++) {
    var s = stars[i];

    ctx.fillStyle = "grey";
    ctx.beginPath();
    ctx.arc(s.x, s.y, s.radius, 0, 2 * Math.PI);
    ctx.fill();
    ctx.fillStyle = 'black';
    ctx.stroke();
  }

  ctx.beginPath();
  for (var i = 0; i < stars.length; i++) {
    var starI = stars[i];
    ctx.moveTo(starI.x, starI.y);
    if (distance(mouse, starI) < 150) ctx.lineTo(mouse.x, mouse.y);
    for (var j = 0; j < stars.length; j++) {
      var starII = stars[j];
      if (distance(starI, starII) < 150) {
        
        ctx.lineTo(starII.x, starII.y);
      }
    }
  }
  ctx.lineWidth = 0.05;
  ctx.strokeStyle = 'grey';
  ctx.stroke();
}


function update() {
  for (var i = 0; i < stars.length; i++) {
    var s = stars[i];

    s.x += s.vx / FPS;
    s.y += s.vy / FPS;

    if (s.x < 0 || s.x > canvas.width) s.vx = -s.vx;
    if (s.y < 0 || s.y > canvas.height) s.vy = -s.vy;
  }
}


function distance(point1, point2) {
  var xs = point2.x - point1.x;
  xs *= xs;

  var ys = point2.y - point1.y;
  ys *= ys;

  return Math.sqrt(xs + ys);
}


function onMouseMove(e) {
  mouse.x = e.clientX;
  mouse.y = e.clientY;
}



function tick() {
    draw();
    update();
    requestAnimationFrame(tick);
}


initialize();


$(document).ready(function () {
  $(window).on("resize", function () {
    body.removeEventListener("mousemove", null);
    drawing = false;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    drawing = true;
  });
})
