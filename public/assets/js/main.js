(function ($, bootbox, Mustache) {
  "use strict";

  var render = function (templateName, data) {
    var template = $('#template-' + templateName).html();
    data.selected = function () {
      return function (text, render) {
        var textData, key, value, currentValue;

        textData = render(text).split(',');
        key = textData[0];
        value = textData[1];
        currentValue = textData[2];

        if (value == currentValue) {
          return 'selected="selected"';
        }
      };
    };
    return Mustache.render(template, data);
  };

  $(document).on('click', '.edit-button', function (event) {
    event.preventDefault();

    bootbox.dialog({
      'title': 'Editar registro',
      'message': render('edit_dialog', $(this).parents('tr').data()),
      'buttons': {
        'Cancelar': function () {},
        'Salvar': function () {},
      }
    });
  });
}(window.jQuery, window.bootbox, window.Mustache));