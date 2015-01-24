/*jslint browser:true, indent: 2*/
/*global modal*/

(function ($) {
  "use strict";
  var addEntrance, addOut, parseFormValidation, commit, commitAction, remove, showModal;

  parseFormValidation = function () {
    var form = $('.modal .form'),
      formValidation = form.data('formValidation');

    return {
      form: form,
      formValidation: formValidation
    }
  };

  // event to validate "real value or not"
  $(document)
    .on('change', '.modal [name$="[status]"]', function () {
      var formData = parseFormValidation(),
        form = formData.form,
        formValidation = formData.formValidation,
        happened = (2 == $(this).val()),
        addRemove = !!this.name.match(/\[add\]/) ? 'add' : 'remove';

      formValidation.enableFieldValidators('entrance[' + addRemove + '][real]', happened, 'notEmpty');
      formValidation.revalidateField('entrance[' + addRemove + '][real]');
    });

  // trigger formValidation
  commitAction = function (event) {
    var url = window.location.pathname,
      formData = parseFormValidation(),
      form = formData.form,
      formValidation = formData.formValidation,
      data = form.find(':input').serialize();

    event.preventDefault();

    formValidation.validate();
    if (!formValidation.isValid()) {
      return;
    }

    $.post(url, data, function () {
      window.location.reload();
    });
  };

  showModal = function (options) {
    modal.show({
      title: options.title,
      content: options.form,
      cancel: i18n._('cancel'),
      ok: i18n._('ok'),
      action: { ok: commitAction },
      modal: {
        onVisible: function () {
          $(this).find('.form').formValidation({framework:'semantic'});
          $('.modal [name$="[status]"]').trigger('change');
        },
        onApprove: function () {
          var formValidation;
          formValidation = $(this).find('.form').data('formValidation');
          formValidation.validate();
          return formValidation.isValid();
        }
      }
    });
  };

  addEntrance = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_entrance_form').clone();
    addEntranceForm.removeAttr('id').attr('id', 'form_add_entrance');

    showModal({
      title: i18n._('add-entrance'),
      form: addEntranceForm
    });
  };

  addOut = function (event) {
    event.preventDefault();

    var addEntranceForm;

    addEntranceForm = $('#add_remove_form').clone();
    addEntranceForm.removeAttr('id').attr('id', 'form_add_remove');

    showModal({
      title: i18n._('add-out'),
      form: addEntranceForm
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

    showModal({
      title: i18n._('commit-transaction'),
      form: commitForm
    });
  };

  remove = function (event) {
    var id, data;
    event.preventDefault();

    if (!confirm(i18n._('confirm-remove-transaction'))) {
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
