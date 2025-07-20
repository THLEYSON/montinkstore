$(function () {
  $('#cepForm').on('submit', function (e) {
    e.preventDefault();
    const cep = $('#cep').val().replace(/\D/g, '');
    const $result = $('#cepResult');

    if (cep.length !== 8) {
      $result.removeClass('d-none alert-success').addClass('alert-danger').text('Invalid CEP format.');
      setTimeout(() => $result.addClass('d-none'), 4000);
      return;
    }

    $.get(`https://viacep.com.br/ws/${cep}/json/`)
      .done(function (data) {
        if (data.erro) {
          $result.removeClass('d-none alert-success').addClass('alert-danger').text('CEP not found.');
        } else {
          $result.removeClass('d-none alert-danger').addClass('alert-success')
            .text(`📍 Address found: ${data.logradouro}, ${data.bairro} - ${data.localidade}/${data.uf}`);
        }
        setTimeout(() => $result.addClass('d-none'), 4000);
      })
      .fail(function () {
        $result.removeClass('d-none alert-success').addClass('alert-danger').text('Error fetching CEP.');
        setTimeout(() => $result.addClass('d-none'), 4000);
      });
  });
});
