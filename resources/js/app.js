import $, { easing } from 'jquery';
window.$ = window.jQuery = $;
import { injectSpeedInsights } from '@vercel/speed-insights';
import anime from 'animejs';

injectSpeedInsights();

$(document).ready(function() {
   var isPlayed = false;

    function quickAnimation() {
      anime({
        targets: '.quick-icon',
        translateX: [
          { value: -100, duration: 1000, easing: 'easeInOutSine' },
          { value: 50, duration: 2000},
          { value: 75, duration: 750},
          { value: 100, duration: 750},
          { value: 0, duration: 1000, easing:'easeInOutSine'}
        ],
        rotate: { 
          duration: 50,
          easing: 'easeInOutSine'
        },
        delay: function(el, i, l){
          return (i * 100) + 1000;
        }
      });
    }

    anime({
      complete: function() {
        quickAnimation();
      }
    })
    
    $('.quick-icon').on({
      'mouseover': function() {
        anime({
          targets: '.quick-icon',
          translateX: [
            { value: 0, duration: 500, easing: 'easeInOutSine' },
            { value: 20, duration: 500, easing: 'easeInOutSine' },
            { value: -20, duration: 500, easing: 'easeInOutSine' },
          ],
          rotate: {
            value: '+=5',
            duration: 200,
            easing: 'easeInOutSine'
          },
          direction: 'alternate'
        });
      },
      'mouseout': function() {
        anime.pause('.quick-icon');
      }
    });
    
      function turnOnScreen() {
        anime({
          targets: '.secure-icon path',
          fill: {
            value: '#3498db',
            duration: 500,
            easing: 'easeInOutQuad'
          }
        });
      };
    
      function turnOffScreen() {
        anime({
          targets: '.secure-icon path',
          fill: {
            value: '#FFFFFF',
            duration: 500,
            easing: 'easeInOutQuad'
          }
        });
      };
    
      anime({
        targets: '.secure-icon',
        rotate: {
          value: 360,
          duration: 1000,
          easing: 'easeInOutQuad'
        },
        complete: function() {
          turnOnScreen();
          setTimeout(function() {
            turnOffScreen();
          }, 3000);
        }
      });
    
      $('.secure-icon').on({
        'mouseover': function() {
          turnOnScreen();
        },
        'mouseout': function(){
          turnOffScreen();
        }
      });

      anime({
        targets: '.document-icon',
        scale: [1, 1.2, 1],
        direction: 'alternate',
        loop: 5,
        easing: 'easeInOutQuad',
        duration: 500
      });

});

import 'bootstrap'; 