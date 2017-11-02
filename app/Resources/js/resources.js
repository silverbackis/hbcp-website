require('jquery-form')

$(function(){
  const $deptSelect = $('#department-select');
  $deptSelect.on('change', () => {
    window.location.href = $deptSelect.val();
  })

  const $filterSelects = $('.filter-row .selectpicker');
  const $filterForm = $('#filter-form');
  const $resourcesList = $('#resources-list')
  const $filterCount = $('#filter-count')
  $filterForm.ajaxForm({
    beforeSubmit () {
      $resourcesList.addClass('loading')
    },
    success (data) {
      $resourcesList
        .html(data.html)
        .removeClass('loading')
    }
  });

  $filterSelects.on('change.bs.select', (evt) => {
    $filterForm.submit();

    let count = 0;
    $.each($filterSelects.filter('.filter'), function () {
      count += $('option:selected', this).length
    });
    $filterCount.html(count);
  })
});
