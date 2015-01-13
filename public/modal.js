/*jslint browser:true, indent: 2*/

(function ($) {
  "use strict";
  var Modal, wrapButtonAction;

  // private
  wrapButtonAction = function (event) {
    var action, data;

    data = event.data;

    if (data.actions.ok) {
      data.modalElement.find('.actions .positive.button').off('click', data.actions.ok);
    }
    if (data.actions.cancel) {
      data.modalElement.find('.actions .negative.button').off('click', data.actions.cancel);
    }

    action = data.actions[data.action];
    action.apply(this, [].slice.call(arguments));
  };


  // public
  Modal = function () {
    this.modalElement = $('.modal');

    this.defaultOptions = {
      'title': '',
      'content': '',
      'ok': '',
      'cancel': '',
      'action': {
        'ok': false,
        'cancel': false
      },
      'modal': {
        'closable': false
      }
    };
  };

  Modal.prototype.show = function (options) {
    var modalElement;

    modalElement = this.modalElement;

    if (!options) {
      options = {};
    }
    options = $.extend(true, {}, this.defaultOptions, options);

    modalElement.find('.header').text(options.title);
    modalElement.find('.content').html(options.content);

    if (false === options.ok) {
      modalElement.find('.actions .positive.button').text('').hide();
    } else {
      modalElement.find('.actions .positive.button').text(options.ok);
    }

    if (false === options.cancel) {
      modalElement.find('.actions .negative.button').text('').hide();
    } else {
      modalElement.find('.actions .negative.button').text(options.cancel);
    }

    if (options.action.ok) {
      modalElement.find('.actions .positive.button')
        .on('click', {
          modalElement: modalElement,
          actions: options.action,
          action: 'ok'
        }, wrapButtonAction);
    }

    if (options.action.cancel) {
      modalElement.find('.actions .negative.button')
        .on('click', {
          modalElement: modalElement,
          actions: options.action,
          action: 'cancel'
        }, wrapButtonAction);
    }

    $.each(options.modal, function (key, value) {
      modalElement.modal('setting', key, value);
    });

    modalElement.modal('show');
  };

  window.modal = new Modal();

}(window.jQuery));