/*jslint browser:true, indent: 2*/
/*global modal*/

(function ($) {
  "use strict";
  var addEntrance, addEntranceAction, addOut, parseFormValidation, commit, commitAction, remove;

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
        }
      }
    });
  };

  addOut = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_remove_form').clone();
    addEntranceForm.removeAttr('id').attr('id', 'form_add_remove');

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
        }
      }
    });
  };

  commitAction = function () {
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

  commit = function (event) {
    event.preventDefault();
    var commitForm, rowData;

    rowData = $(event.target).parents('tr').data();

    commitForm = $('#commit_form').clone();
    commitForm.removeAttr('id').attr('id', 'form_commit');
    commitForm.find('input[name="commit[id]"]').val(rowData.id);
    commitForm.find('input[name="commit[day]"]').val(rowData.day);
    commitForm.find('input[name="commit[real]"]').val(Math.abs(rowData.estimated));

    modal.show({
      title: 'Efetuar transação',
      content: commitForm,
      cancel: 'Não quero efetuar!',
      ok: 'Efetue a transação',
      action: {
        ok: commitAction
      },
      modal: {
        onVisible: function () {
          $(this).find('form').formValidation({framework:'semantic'});
        },
        onApprove: function () {
          var formValidation;
          formValidation = $(this).find('form').data('formValidation');
          formValidation.validate();
          return formValidation.isValid();
        }
      }
    });

  };

  remove = function (event) {
    var id, data;
    event.preventDefault();

    if (!confirm('Tem certeza que deseja remover esta entrada?')) {
      return;
    }

    id = $(event.target).parents('tr').data('id');

    data = {
      "_METHOD": "DELETE",
      "id": id
    };

    $.post(window.location.pathname, data, function () {
      window.location.reload();
    });
  };

  $('#add-entrance').on('click', addEntrance);
  $('#add-remove').on('click', addOut);
  $('.commit-button').on('click', commit);
  $('.remove-button').on('click', remove);

}(window.jQuery));
