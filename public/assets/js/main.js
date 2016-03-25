(function($, bootbox, Mustache) {
  'use strict';

  var render = function(templateName, data) {
    var template = $('#template-' + templateName).html();

    data.selected = function() {
      return function(text, render) {
        var textData = render(text).split(',');

        if (textData[0] == textData[1]) {
          return 'selected="selected"';
        }
      };
    };
    return Mustache.render(template, data);
  };

  $(document).on('click', '.commit-button:not(.disabled)', function(event) {
    event.preventDefault();

    bootbox.dialog({
      'title': i18n._('commit-transaction'),
      'message': render('check_dialog', $(this).parents('tr').data()),
      'onEscape': true,
      'buttons': {
        'Cancelar': $.noop,
        'Confirmar': function() {}
      }
    });
  });

  $(document).on('click', '.edit-button', function(event) {
    event.preventDefault();

    bootbox.dialog({
      'title': i18n._('edit'),
      'message': render('edit_dialog', $(this).parents('tr').data()),
      'onEscape': true,
      'buttons': {
        'Cancelar': $.noop,
        'Salvar': function() {},
      }
    });
  });

  $(document).on('click', '.remove-button');
}(window.jQuery, window.bootbox, window.Mustache));
