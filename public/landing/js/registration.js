let USER_API_URL = API_URL + "user/",
    CONFIG_API_URL = API_URL + "config/",
    SESSION = localStorage.getItem("user-token")

$(document).ready(function(){
    if(SESSION){
      window.location.href = WEB_URL
    }

    getConfig()
    
    $('#registration-form').submit(function (e){
        e.preventDefault()
        startLoadingButton("#registration-button")

        let $form = $(this),
            request = {
              first_name: $form.find( "input[name='first_name']" ).val(),
              last_name: $form.find( "input[name='last_name']" ).val(),
              email: $form.find( "input[name='email']" ).val(),
              phone: $form.find( "input[name='phone']" ).val(),
              password: $form.find( "input[name='password']" ).val(),
              affiliate_code: $form.find( "input[name='affiliate_code']" ).val(),
            }
    
        console.log("REQUEST: ", request)

        $.ajax({
          async: true,
          url: `${USER_API_URL}registration`,
          type: 'POST',
          data: JSON.stringify(request),
          error: function(res) {
            const response = JSON.parse(res.responseText)
            endLoadingButton('#registration-button', 'Daftar')
            showError("Registrasi", "Gagal melakukan registrasi!")
          },
          success: function(res) {
            endLoadingButton('#registration-button', 'Daftar')
            showSuccess("Registrasi", "Sukses melakukan registrasi. Silahkan melakukan konfirmasi melalui email Anda.", WEB_URL)
          }
        });
    })
});

function getConfig(){
  $.ajax({
      async: true,
      url: `${CONFIG_API_URL}`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
      },
      success: function(res) {
        CONFIG = res.data
        renderIcon(res.data)
      }
  });
}

/* Animation */
function startLoadingButton(dom){
  let loading_button = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
  $(dom).html(loading_button)
  $(dom).prop('disabled', true);
}

function endLoadingButton(dom, text){
  $(dom).html(text)
  $(dom).prop('disabled', false);
}

function showError(title, text, confirmLink=null){
  Swal.fire({
      icon: 'error',
      title: title,
      text: text,
      confirmButtonColor: '#6c63ff'
  }).then((result) => {
      if (confirmLink) {
          window.location.href = confirmLink
      }
  }); 
}

function showSuccess(title, text, confirmLink=null){
  Swal.fire({
      icon: 'success',
      title: title,
      text: text,
      confirmButtonColor: '#6c63ff'
  }).then((result) => {
      if (confirmLink) {
          window.location.href = confirmLink
      }
  }); 
}

function enableRegistration(){
  $('#registration-button').prop("disabled", false);
}

function disableRegistration(){
  $('#registration-button').prop("disabled", true);
}
/* Animation */

function renderIcon(data){
  if(data.web_logo){
    $('.brand-logo').prop("src", API_URL+data.web_logo)
  }else{
    $('.brand-logo-section').hide()
  }
  if(data.web_icon){
    $(".favicon").attr("href", API_URL+data.web_icon);
  }
}