/*jslint browser:true, indent: 2*/
/*global modal*/

(function ($) {
  "use strict";
  var addEntrance, addEntranceAction;

  // trigger formValidation
  addEntranceAction = function (event) {
    event.preventDefault();
    // TODO submit form
  };

  addEntrance = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_entrance_form').clone();
    addEntranceForm.removeAttr('id');

    // $(addEntranceForm).formValidation();

    modal.show({
      title: 'Adicionar Entrada',
      content: addEntranceForm,
      cancel: 'Oops! Cancele minha ação',
      ok: 'Adicionar entrada!',
      action: {
        ok: addEntranceAction
      },
      event: {
        show: function () {
          $(this).find('form').formValidation({framework:'semantic'})
        }
      }
    });
  };

  $('#add-entrance').on('click', addEntrance);

}(window.jQuery));
