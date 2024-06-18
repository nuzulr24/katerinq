let USER_API_URL = API_URL + "user/",
    CONFIG_API_URL = API_URL + "config/",
    SESSION = localStorage.getItem("user-token")

$(document).ready(function(){
    if(SESSION){
      window.location.href = WEB_URL
    }
    getConfig() 

    $('#verification-resend-form').submit(function (e){
        e.preventDefault()
        startLoadingButton("#verification-resend-button")

        let $form = $(this),
            request = {
                email: $form.find( "input[name='email']" ).val()
            }
    
        console.log("REQUEST: ", request)

        $.ajax({
          async: true,
          url: `${USER_API_URL}verification-resend`,
          type: 'POST',
          data: JSON.stringify(request),
          error: function(res) {
            const response = JSON.parse(res.responseText)
            endLoadingButton('#verification-resend-button', 'Daftar')
            showError("Kirim Ulang Verifikasi", response.message)
          },
          success: function(res) {
            endLoadingButton('#verification-resend-button', 'Daftar')
            showSuccess("Kirim Ulang Verifikasi", "Sukses mengirim ulang email verifikasi. Silahkan melakukan konfirmasi melalui email Anda.", WEB_URL)
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
/* Animation */
