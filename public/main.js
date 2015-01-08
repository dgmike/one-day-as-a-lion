/*jslint browser:true, indent: 2*/
/*global modal*/

(function ($) {
  "use strict";
  var addEntrance, addEntranceAction;

  addEntranceAction = function () {
    // TODO submit form
  };

  addEntrance = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_entrance_form').clone();
    addEntranceForm.removeAttr('id');

    modal.show({
      title: 'Adicionar Entrada',
      content: addEntranceForm,
      cancel: 'Oops! Cancele minha ação',
      ok: 'Adicionar entrada!',
      action: {
        ok: addEntranceAction
      }
    });
  };

  $('#add-entrance').on('click', addEntrance);

}(window.jQuery));
