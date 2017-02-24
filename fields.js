$(document).ready(function() {
  var SPMaskBehavior = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
  },
  spOptions = {
    onKeyPress: function(val, e, field, options) {
      field.mask(SPMaskBehavior.apply({}, arguments), options);
    }
  };

  $('.number').mask('#');
  $('.cpf').mask('000.000.000-00');
  $('.telefone').mask(SPMaskBehavior, spOptions);
});

function fill_fields() {
  var path      = '../plugins/fortbrasil/ajax/get_fields.php';
  var id_conta  = $('#id_conta_field').val();

  $.ajax({
    type: 'GET',
    dataType: 'json',
    url: path,
    data: 'id_conta=' + id_conta,

    success: function(data) {
      var nome      = (data['nome']) ? data['nome'] : '';
      var cpf       = (data['cpf']) ? data['cpf'] : '';
      var produto   = (data['produto']) ? data['produto'] : '';
      var telefone  = (data['telefone']) ? data['telefone'] : '';
      var email     = (data['email']) ? data['email'] : '';

      $('#nome_field').val(nome);
      $('#cpf_field').val(cpf);
      $('#produto_field').val(produto);
      $('#telefone_field').val(telefone);
      $('#email_field').val(email);
    }
  });
}
