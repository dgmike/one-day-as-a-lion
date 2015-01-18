/*jslint browser:true, indent: 2*/
/*global modal*/

(function ($) {
  "use strict";
  var addEntrance, addEntranceAction, parseFormValidation;

  parseFormValidation = function () {
    var form = $('#form_add_entrance form'),
      formValidation = form.data('formValidation');

    return {
      form: form,
      formValidation: formValidation
    }
  };

  // event to validate "real value or not"
  $(document)
    .on('change', '#form_add_entrance [name="entrance[add][status]"]', function () {
      var formData = parseFormValidation(),
        form = formData.form,
        formValidation = formData.formValidation,
        happened = (2 == $(this).val());

      formValidation.enableFieldValidators('entrance[add][real]', happened, 'notEmpty');
      formValidation.revalidateField('entrance[add][real]');
    });

  // trigger formValidation
  addEntranceAction = function (event) {
    var url = window.location.pathname,
      formData = parseFormValidation(),
      form = formData.form,
      formValidation = formData.formValidation,
      data = form.serialize();

    event.preventDefault();

    formValidation.validate();
    if (!formValidation.isValid()) {
      return;
    }

    $.post(url, data, function () {
      window.location.reload();
    });
  };

  addEntrance = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_entrance_form').clone();
    addEntranceForm.removeAttr('id').attr('id', 'form_add_entrance');

    // $(addEntranceForm).formValidation();

    modal.show({
      title: 'Adicionar Entrada',
      content: addEntranceForm,
      cancel: 'Oops! Cancele minha ação',
      ok: 'Adicionar entrada!',
      action: {
        ok: addEntranceAction
      },
      modal: {
        onVisible: function () {
          $(this).find('form').formValidation({framework:'semantic'})
        },
        onApprove: function () {
          var formValidation;
          formValidation = $(this).find('form').data('formValidation');
          formValidation.validate();
          return formValidation.isValid();
          debugger
        }
      }
    });
  };

  $('#add-entrance').on('click', addEntrance);

}(window.jQuery));
