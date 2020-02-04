$(function() {

  $('input[name=nextMorning]').on('change', function() {
    if ($(this).prop('checked')) {
      $('select[name=period]').prop("disabled", true);
    } else {
      $('select[name=period]').prop("disabled", false);
    }
  });
  $('input[name=nextMorning]').click();

  $(window).on('resize', function() {
    $('body').css('margin-top', ($(window).height() - $('body').outerHeight()) / 2.5);
    $('html').css('background-position', '50% ' + ($(window).height() - $('body').outerHeight()) / 10 + 'px');
  });
  $('body').css('margin-top', ($(window).height() - $('body').outerHeight()) / 2.5);
  $('html').css('background-position', '50% ' + ($(window).height() - $('body').outerHeight()) / 10 + 'px');

  $('body').show();

});