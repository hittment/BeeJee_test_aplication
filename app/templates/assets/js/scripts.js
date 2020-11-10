$('#login-form').submit((e) => {
  e.preventDefault();
  $.post('http://freeapplication.zzz.com.ua/login', $('#login-form').serialize(), (data) => {
    if (data == '') {
      location.href = '../';
    } else {
      alert(data);
    }
    console.log(data);
  });
  return false;
});

$('#add_task').submit((e) => {
  e.preventDefault();
  $.get('../', $('#add_task').serialize(), (data) => {
    if (data == '') {
		$('#status_modal').modal('show');
      $('#status_title').text('Успешно!');
	  $('#status_text').text('Задача добавлена!');
	  $('#status_modal').on('hidden.bs.modal', function (e) {
  		location.reload();
	  });
    } else {
      alert(data);
    }
    console.log(data);
  });
  return false;
});

$('#edit-task').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Button that triggered the modal
  var id = button.data('id');
  var text = button.data('text');
  var status = button.data('status');
  var modal = $(this);
  modal.find('#edit_id').val(id);
  modal.find('#edit_text_input').val(text);
  $('#edit_status').removeAttr('checked');
  try {
    modal.find('#edit_status').attr((status === 1 ? 'checked' : ''), (status === 1 ? 'checked' : ''));
  } catch {

  }
});

$('#edit_approve').click((e) => {
  $('#edit_approve').attr('disabled', 'disabled');
  $('#edit_approve').text('Сохраняю...');

  $.post('../', $('#edit_form').serialize(), (data) => {
    console.log(data);
	  if(data != ''){
		  $('#status_title').text('Ошибка!');
	  $('#status_text').text(data);
		  $('#status_modal').modal('show');
	  }else
		  {
			  $('#edit-task').on('hide.bs.modal', function (e) {
  		$('#status_modal').modal('show');
	  });
	  $('#edit-task').modal('hide');
	  $('#status_title').text('Успешно!');
	  $('#status_text').text('Изменения внесены успешно!');
	  $('#status_modal').on('hidden.bs.modal', function (e) {
  		location.reload();
	  });
		  }
	  
	  
	  
    $('#edit_approve').removeAttr('disabled');
    $('#edit_approve').text('Применить');
  });
});
