import Clipboard from 'clipboard/dist/clipboard';

$(function (){
  let revertCopyTimeout;
  const $copyLink = $('#copy-link');
  $copyLink.data('original', $copyLink.html());
  let clipboard = new Clipboard($copyLink[0]);

  clipboard.on('success', function(e) {
    $copyLink.html('Copied').addClass('success');
    if (revertCopyTimeout) {
      clearTimeout(revertCopyTimeout);
    }
    revertCopyTimeout = setTimeout(() => {
      $copyLink.html($copyLink.data('original')).removeClass('success');
    }, 3000)
    e.clearSelection();
  });

  clipboard.on('error', function(e) {
    console.error('Action:', e.action);
    console.error('Trigger:', e.trigger);
  });


  $('#fb-share').on('click', () => {
    evt.preventDefault();
    FB.ui({
      method: 'share',
      display: 'popup',
      href: window.location.href,
    }, function(response){});
  })

  $('#tw-share').on('click', (evt) => {
    evt.preventDefault();
    window.open( 'https://twitter.com/intent/tweet?text=' + encodeURIComponent($('#tw-share').attr('data-tweet')), 'HBCP', 'toolbar=0, status=0, width=626, height=436');
  });
  $('#li-share').on('click', (evt) => {
    evt.preventDefault();
    window.open( 'http://www.linkedin.com/shareArticle?mini=true&url='+encodeURIComponent(window.location.href)+'&title='+encodeURIComponent($('.jumbotron-header').text())+'&source=HBCP', 'sharer', 'toolbar=0, status=0, width=626, height=436');
  });
});
