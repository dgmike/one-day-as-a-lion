(function ($) {
  "use strict";
  var addEntrance, resetModal, removeActions, closeModal, addEntranceAction, addEntranceCancelAction;

  resetModal = function () {
    $('.modal')
      .find('.header, .content, .action .button')
      .html('');
  };

  removeActions = function () {
    $('.modal .negative.button')
      .off('click', addEntranceCancelAction);
    $('.modal .positive.button')
      .off('click', addEntranceAction);
  };

  closeModal = function () {
    removeActions();
    setTimeout(resetModal, 500);
  }

  addEntranceCancelAction = function () {
    closeModal();
  };

  addEntranceAction = function () {
    // TODO submit form
    closeModal();
  };

  addEntrance = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_entrance_form').clone();
    addEntranceForm.removeAttr('id');

    $('.modal .header').text('Adicionar Entrada');

    $('.modal .content').html(addEntranceForm);

    $('.modal .actions .negative.button')
      .text('Oops! Cancele minha ação')
      .on('click', addEntranceCancelAction);

    $('.modal .actions .positive.button')
      .text('Adicionar entrada!')
      .on('click', addEntranceAction);

    $('.modal')
      .modal('setting', 'closable', false)
      .modal('show');
  };

  $('#add-entrance').on('click', addEntrance);

}(window.jQuery));
