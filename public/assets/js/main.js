(function($, bootbox, Mustache, location, i18n) {
  'use strict';

  var action;
  var maskMoney;
  var render;

  $.fn.bootstrapTable.defaults.locale = 'pt_BR';
  $.fn.bootstrapTable.defaults.striped = true;
  $.fn.bootstrapTable.defaults.search = true;
  $.fn.bootstrapTable.defaults.showHeader = true;
  $.fn.bootstrapTable.defaults.showToggle = true;
  $.fn.bootstrapTable.defaults.sortable = true;

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

  maskMoney = function() {
    $('input.money').each(function() {
      $(this)
      .attr('type', 'text')
      .maskMoney({'allowZero': true, 'thousands': '.', 'decimal': ','})
      .maskMoney('mask');
    });
  };

  render = function(templateName, data) {
    var template = $('#template-' + templateName).html();

    data = data || {};

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

  action = function(options) {
    var defaultOptions;

    defaultOptions = Object.create(null);

    defaultOptions.filter = (data) => data,
    defaultOptions.template = '';
    defaultOptions.title = '';
    defaultOptions.wrapper = '';

    options = Object.assign(Object.create(null), defaultOptions, options);

    return function(event) {
      var form;
      var formData;
      var fv;

      event.preventDefault();

      formData = $(this).parents('tr').data();
      bootbox.dialog({
        'title': i18n._(options.title),
        'message': render(options.template, formData),
        'onEscape': true,
        'buttons': {
          'cancel': {'label': i18n._('cancel'), 'callback': $.noop},
          'ok': {
            'label': i18n._('ok'),
            'callback': function() {
              var data;

              if (!$('form').data('formValidation').validate().isValid()) {
                return false;
              }

              data = options.filter(
                form.serializeWrap(options.wrapper),
                formData
              );
              $.post(location.pathname, data, () => location.reload());
            }
          }
        }
      });

      maskMoney();
      form = $('.bootbox form');
      fv = form.formValidation({
        'framework': 'bootstrap',
        'locale': 'pt_BR',
        'fields': {
          'day': {
            'validators': {
              'notEmpty': {'enabled': true},
              'beetween': {'min': 1, 'max': 31}
            }
          },
          'description': {
            'validators': {
              'notEmpty': {'enabled': true}
            }
          },
          'estimated': {
            'validators': {
              'notEmpty': {'enabled': true},
              'numeric': {
                'thousandsseparator': '.',
                'decimalseparator': ','
              }//,
              // 'greaterthan': {
              //   'value': 0,
              //   'inclusive': false
              // }
            }
          }
        }
      });
    };
  };

  $(document).on(
    'click',
    '.commit-button:not(.disabled)',
    action({
      'filter': (data, formData) => {
        data._METHOD = 'put';
        data.commit.id = formData.id;
        data.commit.status = 2;
        data.commit.type = 0 > formData.estimated ? 'remove' : 'add';
        return data;
      },
      'template': 'check_dialog',
      'title': 'commit-transaction',
      'wrapper': 'commit',
    })
  );

  $(document).on(
    'click',
    '.edit-button',
    action({
      'filter': (data, formData) => {
        data._METHOD = 'patch';
        data.entrance.edit.id = formData.id;
        data.entrance.edit.type = 0 > formData.estimated ? 'remove' : 'add';
        return data;
      },
      'template': 'edit_dialog',
      'title': 'edit',
      'wrapper': 'entrance.edit',
    })
  );

  $(document).on(
    'click',
    '.remove-button',
    action({
      'filter': (data, formData) => {
        data._METHOD = 'delete';
        data.id = formData.id;
        return data;
      },
      'template': 'remove_dialog',
      'title': 'remove',
      'wrapper': '',
    })
  );

  $(document).on(
    'click',
    '#add-entrance',
    action({
      'filter': (data) => data,
      'template': 'edit_dialog',
      'title': 'add-entrance',
      'wrapper': 'entrance.add',
    })
  );

  $(document).on(
    'click',
    '#add-out',
    action({
      'filter': (data) => data,
      'template': 'edit_dialog',
      'title': 'add-out',
      'wrapper': 'entrance.remove',
    })
  );
}(window.jQuery, window.bootbox, window.Mustache, window.location, window.i18n));
