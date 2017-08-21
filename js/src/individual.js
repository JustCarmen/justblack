// Scripts for the individual page
$('.jc-global-individual').each(function() {
  var html = $('h2').html();
  var parts = html.split(',');
  $('h2').html(parts[0]);
  $('.wt-main-container .card:first .card-header').each(function() {
    $(this)
      .addClass('d-flex flex-column flex-lg-row justify-content-lg-between')
      .wrapInner('<div class="jc-individual-name">');
    if (strip_tags(parts[1]) !== '–') {
      $(this).append('<div class="jc-individual-age">' + parts[1]);
    }
  });
});
