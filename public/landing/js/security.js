$(document).ready(function(){
    if(!SESSION){ 
        window.location.href = `${WEB_URL}login?source=${window.location.href}`
    }

    $('#change-password-form').submit(function(e){
        e.preventDefault()
        startLoadingButton('#change-password-button')

        let $form = $('#change-password-form'),
            request = {
                old_password: $form.find("input[name='old_password']").val(),
                new_password: $form.find("input[name='new_password']").val()
            }

        $.ajax({
            async: true,
            url: `${USER_API_URL}change-password`,
            type: 'PUT',
            data: JSON.stringify(request),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            error: function(res) {
              const response = JSON.parse(res.responseText)
              let isRetry = retryRequest(response, window.location.href)
              if(isRetry) $.ajax(this)
              endLoadingButton('#change-password-button', 'Ganti Kata Sandi')
            },
            success: function(res) {
                endLoadingButton('#change-password-button', 'Ganti Kata Sandi')
                $('#change-password-modal').modal('toggle')
                showSuccess("Ubah Kata Sandi", res.message)
            }
        });
    })
})
