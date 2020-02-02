$(function () {

  $('input[name=nextMorning]').on('change', function () {
    if ($(this).prop('checked')) {
      $('select[name=period]').prop("disabled", true);
    } else {
      $('select[name=period]').prop("disabled", false);
    }
  });
  $('input[name=nextMorning]').click();

});
