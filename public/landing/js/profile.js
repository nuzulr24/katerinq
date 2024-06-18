$(document).ready(function(){
    if(!SESSION){ 
        window.location.href = `${WEB_URL}login?source=${window.location.href}`
    }

    // renderProfileForm()

    $('#profile-form').submit(function(e){
        e.preventDefault()
        startLoadingButton('#profile-button')

        let $form = $('#profile-form'),
            request = {
                first_name: $form.find("input[name='first_name']").val(),
                last_name: $form.find("input[name='last_name']").val(),
                phone: $form.find("input[name='phone']").val()
            }

        $.ajax({
            async: true,
            url: `${USER_API_URL}`,
            type: 'PUT',
            data: JSON.stringify(request),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            error: function(res) {
              const response = JSON.parse(res.responseText)
              let isRetry = retryRequest(response, window.location.href)
              if(isRetry) $.ajax(this)
              endLoadingButton('#profile-button', 'Ubah Profil')
            },
            success: function(res) {
                showSuccess("Ubah Profil", res.message)
                endLoadingButton('#profile-button', 'Ubah Profil')
            }
        });
    })

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

function renderProfileForm(){
    let $form = $('#profile-form')
    $form.find("input[name='first_name']").val(PROFILE.first_name)
    $form.find("input[name='last_name']").val(PROFILE.last_name)
    $form.find("input[name='email']").val(PROFILE.email)
    $form.find("input[name='phone']").val(PROFILE.phone)
    $form.find("input[name='unique_code']").val(PROFILE.unique_code)
}