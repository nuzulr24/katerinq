$(document).ready(function(){
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
})