require('bootstrap-sass');
require('bootstrap-select');
import './resources';
import './share';

$(function () {
  $('.member-description')
    .on('show.bs.collapse', (evt) => {
      const $clickedBtn = $('[data-target="#'+evt.currentTarget.id+'"]');
      $clickedBtn.addClass('showing');
    })
    .on('hide.bs.collapse', (evt) => {
      const $clickedBtn = $('[data-target="#'+evt.currentTarget.id+'"]');
      $clickedBtn.removeClass('showing');
    })
  ;
})