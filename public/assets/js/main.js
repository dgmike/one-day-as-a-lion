(function($, bootbox, Mustache) {
  'use strict';

  $.fn.serializeWrap = function(wrapper) {
    var zeroObj = () => Object.create(null);
    var value;
    var result = {};

    value = $('form')
    .serializeArray()
    .reduce((c, n) => { c[n.name] = n.value; return c; }, zeroObj());

    wrapper
    .split('.')
    .reduce(
      (prev, cur, idx, arr) => {
        if (idx === arr.length - 1) {
          return (prev[cur] = value);
        }
        return $.isPlainObject(prev[cur]) ? prev[cur] : (prev[cur] = zeroObj());
      }, result
    );

    return result;
  };

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
    var formData = $(this).parents('tr').data();
    event.preventDefault();

    bootbox.dialog({
      'title': i18n._('commit-transaction'),
      'message': render('check_dialog', formData),
      'onEscape': true,
      'buttons': {
        'Cancelar': $.noop,
        'Confirmar': function() {
          var form = $('.bootbox form');
          var data = form.serializeWrap('commit');

          data._METHOD = 'put';
          data.commit.id = formData.id;
          data.commit.type = 0 > formData.estimated ? 'remove' : 'add';
          data.commit.status = 2;

          $.post(window.location.pathname, data);
        }
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

  $(document).on('click', '.remove-button', $.noop);

  $(document).on('click', '#add-entrance', $.noop);

  $(document).on('click', '#add-out', $.noop);

}(window.jQuery, window.bootbox, window.Mustache));
