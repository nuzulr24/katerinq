$(document).ready(function(){
    $('#changepassword-tab').addClass("active");

    $("#change-password-form").submit(function(e){
        e.preventDefault()
        startLoadingButton("#change-password-button")
      
        let $form = $( this ),
            request = {
              old_password: $form.find( "input[name='old_password']" ).val(),
              confirm_password: $form.find( "input[name='confirm_password']" ).val(),
              new_password: $form.find( "input[name='new_password']" ).val()
            }

        if(request.confirm_password != request.new_password){
            endLoadingButton('#change-password-button', 'Ganti Kata Sandi')
            showError("Kata sandi baru dan konfirmasi harus sama!")
            return false
        }
        
        $.ajax({
            async: true,
            url: `${USER_API_URL}change-password`,
            type: 'PUT',
            beforeSend: function (xhr) {
              xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            data: JSON.stringify(request),
            error: function(res) {
              const response = JSON.parse(res.responseText)
              endLoadingButton('#change-password-button', 'Ganti Kata Sandi')
              let is_retry = retryRequest(response)
              if(is_retry) $.ajax(this)
            },
            success: function(res) {
              showSuccess("Ubah Kata Sandi", res.message)
              endLoadingButton('#change-password-button', 'Ganti Kata Sandi')
            }
        });
    })
})

