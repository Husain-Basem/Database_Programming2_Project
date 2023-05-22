$(()=>{
  'use strict';

  const forms = $('.needs-validation');

  // stop forms from submitting
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false)
  });

  $('#username').on('blur', () =>{
    const e = this;
    $.ajax('<?=BASE_URL.'/user/exists.php'?>', {u: $(e).target.value}).done((exists)=>{
    if (exists == "1") {
      $(e).addClass('is-invalid')
    }
    });
  });

});
