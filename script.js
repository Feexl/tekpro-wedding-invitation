const hamburger = document.querySelector('.hamburger');
const nav = document.querySelector('.nav');

hamburger.addEventListener('click', () => {
  nav.classList.toggle('show');
})


const slides = document.querySelectorAll('.slide');
let currentIndex = 0;

function showSlide() {
  slides[currentIndex].classList.remove('active');
  currentIndex++;
  if (currentIndex === slides.length) {
    currentIndex = 0;
  }

  slides[currentIndex].classList.add('active');
}
setInterval(showSlide, 3000);


// Membuka WA

function openWhatsapp() {
  const phoneNumber = '6285272048989';
  const message = 'Halo, saya ingin menanyakan tentang undangan digital';
  const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`
  window.open(url, '_blank');
}





















// // script.js - small interactive bits: countdown, simple smooth scrolling
// (function(){
//   // Countdown to end of day (local time) for demo
//   function pad(n){return n<10?('0'+n):n}
//   function updateCountdown(){
//     var now = new Date();
//     // end of day
//     var end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23,59,59);
//     var diff = Math.max(0, end - now);
//     var s = Math.floor(diff/1000);
//     var days = Math.floor(s/(24*3600));
//     s %= 24*3600;
//     var hours = Math.floor(s/3600);
//     s %= 3600;
//     var minutes = Math.floor(s/60);
//     var seconds = s%60;
//     document.getElementById('days').textContent = pad(days);
//     document.getElementById('hours').textContent = pad(hours);
//     document.getElementById('minutes').textContent = pad(minutes);
//     document.getElementById('seconds').textContent = pad(seconds);
//   }
//   setInterval(updateCountdown, 1000);
//   updateCountdown();

//   // Smooth scrolling for local anchors
//   document.querySelectorAll('a[href^="#"]').forEach(function(a){
//     a.addEventListener('click', function(e){
//       var tgt = document.querySelector(this.getAttribute('href'));
//       if(tgt){ e.preventDefault(); tgt.scrollIntoView({behavior:'smooth', block:'start'}); }
//     });
//   });
// })();
