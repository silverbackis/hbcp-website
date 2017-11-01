function copyToClipboard(elementId) {
  var aux = document.createElement("input");
  aux.setAttribute("value", document.getElementById(elementId).value);
  document.body.appendChild(aux);
  aux.select();
  document.execCommand("copy");
  document.body.removeChild(aux);

}


$(document).ready(function () {

  $('#dp').click(function () {
    $('.sh').slideToggle();
    $('.dropdown').toggleClass('rotate');
  });

  $('.slid').slideUp();
  $('.read-more').click(function () {
    $(this).prev().slideToggle();
  });

  $('.selectpicker').selectpicker({
    style: 'btn-info',
    size: 4
  });

  $('#datesorter').change(function () {
    var order = $(this).val();
    var sorted = $('.panel-body').sort(function (a, b) {
      var a = new Date($(a).attr('data-id').split('/').join(' '));
      var b = new Date($(b).attr('data-id').split('/').join(' '));
      return order == 'desc' ? b - a : a - b;
    });
    $('.sortedata').html(sorted);
  });
  $('#catnames').on('change', function () {
    $('.recoon').hide();
    var catid = $(this).val();
    var resid = $('#resourceid').val();
    if (catid == '' && resid == '') {
      $('.recoon').show();
    }
    $('.recoon').each(function () {
      var evcat = $(this).attr('cat-id');
      var evres = $(this).attr('res-id');
      if (catid == evcat && resid == '') {
        $(this).css('display', 'block');
      }
      if (catid == evcat && resid == evres) {
        $(this).css('display', 'block');
      }
      if (catid == '' && evres == resid) {
        $(this).css('display', 'block');
      }
    });
  });

  $('#resourceid').on('change', function () {
    $('.recoon').hide();
    var resid = $(this).val();
    var catid = $('#catnames').val();
    if (catid == '' && resid == '') {
      $('.recoon').show();
    }
    $('.recoon').each(function () {
      var evcat = $(this).attr('cat-id');
      var evres = $(this).attr('res-id');
      if (catid == evcat && resid == '') {
        $(this).css('display', 'block');
      }

      if (catid == evcat && resid == evres) {
        $(this).css('display', 'block');
      }
      if (catid == '' && evres == resid) {
        $(this).css('display', 'block');
      }
    });
  });


  var current_page_URL = location.href;
  $(".hbcpmenu a").each(function () {
    if ($(this).attr("href") !== "#") {
      var target_URL = $(this).prop("href");
      if (target_URL == current_page_URL) {
        $('nav a').parents('li, ul').removeClass('active');
        $(this).parent('li').addClass('active');
        return false;
      }
    }
  });


  $('.pagechanger').on('change', function () {
    var option = $("option:selected", this).attr('urlid');
    var current_page_URL = location.href;
    if (current_page_URL != option) {
      window.location.href = option;
    }
  });

  $('body').on('click', '.tab', function () {
    $('.tab').removeAttr('id');
    $(this).attr('id', 'active');
  });

});