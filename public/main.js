/*jslint browser:true, indent: 2*/
/*global modal*/

(function ($) {
  "use strict";
  var addEntrance, addOut, parseFormValidation, commit, commitAction, remove,
    showModal, actionImport, edit, editAction;

  actionImport = function (event) {
    var data, target = $(event.target).closest('a').attr('href');
    event.preventDefault();
    data = {
      // @TODO add from
      to: window.location.pathname.replace(/[^\d-]/g, '')
    };

    modal.show({
      title: i18n._('import'),
      content: i18n._('confirm-import'),
      cancel: i18n._('cancel'),
      ok: i18n._('ok'),
      action: {
        ok: function () {
          $.post(target, data, function () {
            window.location.reload();
          });
        }
      }
    });
  };

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
    commitForm.find('input[name="commit[real]"]').val(Math.abs(rowData.real ? rowData.real : rowData.estimated));

    showModal({
      title: i18n._('commit-transaction'),
      form: commitForm
    });
  };

  remove = function (event) {
    var id, data;
    event.preventDefault();

    id = $(event.target).parents('tr').data('id');

    data = {
      "_METHOD": "DELETE",
      "id": id
    };

    modal.show({
      title: i18n._('remove'),
      content: i18n._('confirm-remove-transaction'),
      cancel: i18n._('cancel'),
      ok: i18n._('ok'),
      action: {
        ok: function () {
          $.post(window.location.pathname, data, function () {
            window.location.reload();
          });
        }
      }
    });
  };

  edit = function (event) {
    var $target = $(event.target),
      data = $target.parents('tr').data(),
      id = data.id,
      editForm = $('#edit_form').clone(),
      rnd = Math.round(Math.random() * 100000),
      getInput;

    event.preventDefault();

    editForm.removeAttr('id');
    editForm.find('label').each(function (key, element) {
      var $element = $(element),
        inputId = $(element).attr('for') + '_' + rnd;

      $(element)
        .attr('for', inputId)
        .parents('.field')
        .find(':input')
        .attr('id', inputId);
    });

    getInput = function (name) {
      return editForm.find('[name=entrance\\[edit\\]\\[' + name + '\\]]');
    };

    getInput('id').val(id);
    getInput('type').val(data.estimated > 0 ? 'entrance' : 'remove');
    getInput('day').val(data.day);
    getInput('description').val(data.description);
    getInput('estimated').val(Math.abs(data.estimated));
    getInput('real').val(Math.abs(data.real));
    getInput('status').val(data.status);

    showModal({
      title: 'edit',
      form: editForm
    })

    return;
  };

  $('.action-import').on('click', actionImport);
  $('#add-entrance').on('click', addEntrance);
  $('#add-remove').on('click', addOut);
  $('.commit-button').on('click', commit);
  $('.remove-button').on('click', remove);
  $('.edit-button').on('click', edit);

}(window.jQuery));
