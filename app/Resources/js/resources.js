require('jquery-form');

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

  const $categorySelectPicker = $('#category-select');
  const $categorySelectPickerOptions = $('option', $categorySelectPicker);
  const $bsLink = $('.bs-diagram-link');
  $bsLink.on('click', function(e) {
    e.preventDefault();
    const category = $(this).attr('xlink:href').replace(/^#/, '');
    const strLen = category.length
    let values = []
    $categorySelectPickerOptions.each(function(i, op) {
      const opText = op.innerHTML.toLowerCase();
      if (opText.substr(0, strLen) === category) {
        values.push(op.value)
      }
    });
    $categorySelectPicker.selectpicker('val', values);
  });


  let categories = [];
  $bsLink.each(function(i, link) {
    categories.push(link.getAttributeNS('http://www.w3.org/1999/xlink', 'href').replace(/^#/, ''))
  });
  $categorySelectPicker.on('changed.bs.select', function() {
    $bsLink.removeClass('active');
    const newVals = $categorySelectPicker.selectpicker('val');
    let selectedFirstWord = null;
    let allSelectedSameFirstWord = true;
    $categorySelectPickerOptions.each(function(i, op) {
      if(newVals.indexOf(op.value) !== -1) {
        let firstWord = op.innerHTML.toLowerCase().split(' ')[0];
        if (!selectedFirstWord) {
          selectedFirstWord = firstWord
        } else if(selectedFirstWord !== firstWord) {
          allSelectedSameFirstWord = false;
          return false;
        }
      }
    });
    if (allSelectedSameFirstWord) {
      const $activeBsLink = $bsLink.filter('[xlink\\:href="#'+selectedFirstWord+'"]');
      if ($activeBsLink.length === 1) {
        $activeBsLink.addClass('active');
      }
    }
  })
});
