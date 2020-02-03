$(function () {

  $('input[name=nextMorning]').on('change', function () {
    if ($(this).prop('checked')) {
      $('select[name=period]').prop("disabled", true);
    } else {
      $('select[name=period]').prop("disabled", false);
    }
  });
  $('input[name=nextMorning]').click();

  $(window).on('resize', function(){
    $('body').css('margin-top', ($(window).height() - $('body').outerHeight()) / 3);
  });
  $('body').css('margin-top', ($(window).height() - $('body').outerHeight()) / 3);
  $('body').show();

});
