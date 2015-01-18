/*jslint browser:true, indent: 2*/
/*global modal*/

(function ($) {
  "use strict";
  var addEntrance, addEntranceAction, addOut, parseFormValidation;

  parseFormValidation = function () {
    var form = $('.modal form'),
      formValidation = form.data('formValidation');

    return {
      form: form,
      formValidation: formValidation
    }
  };

  // event to validate "real value or not"
  $(document)
    .on('change', '.modal [name="entrance[add][status]"]', function () {
      var formData = parseFormValidation(),
        form = formData.form,
        formValidation = formData.formValidation,
        happened = (2 == $(this).val());

      formValidation.enableFieldValidators('entrance[add][real]', happened, 'notEmpty');
      formValidation.revalidateField('entrance[add][real]');
    })
    .on('change', '.modal [name="entrance[remove][status]"]', function () {
      var formData = parseFormValidation(),
        form = formData.form,
        formValidation = formData.formValidation,
        happened = (2 == $(this).val());

      formValidation.enableFieldValidators('entrance[remove][real]', happened, 'notEmpty');
      formValidation.revalidateField('entrance[remove][real]');
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
          $(this).find('form').formValidation({framework:'semantic'});
          $('#form_add_entrance [name="entrance[add][status]"]').trigger('change');
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

  addOut = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_remove_form').clone();
    addEntranceForm.removeAttr('id').attr('id', 'form_add_remove');

    // $(addEntranceForm).formValidation();

    modal.show({
      title: 'Adicionar Saída',
      content: addEntranceForm,
      cancel: 'Oops! Não quero adicionar saída',
      ok: 'Adicionar saída!',
      action: {
        ok: addEntranceAction
      },
      modal: {
        onVisible: function () {
          $(this).find('form').formValidation({framework:'semantic'});
          $('#form_add_remove [name="entrance[remove][status]"]').trigger('change');
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
  $('#add-remove').on('click', addOut);

}(window.jQuery));
