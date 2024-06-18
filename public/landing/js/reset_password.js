let RESETPASS_REQUEST,
    RESETPASS_CODE = window.location.pathname.split("/").pop(),
    CONFIG_API_URL = API_URL + "config/",
    USER_API_URL = API_URL + "user/"

$(document).ready(function(){
    getConfig()
    getRequestForgetPassword()

    $('#resetpassword-form').submit(function (e){
        e.preventDefault()
        startLoadingButton("#resetpassword-button")

        let $form = $(this),
            request = {
                id_forget_password: RESETPASS_REQUEST.id,
                new_password: $form.find( "input[name='new_password']" ).val(),
                confirmation_password: $form.find( "input[name='confirm_password']" ).val(),
            }
    
        console.log("REQUEST: ", request)
        if(request.new_password != request.confirmation_password){
            showError("Atur Ulang Kata Sandi", "Kata sandi harus sama!")
            endLoadingButton('#resetpassword-button', 'Lupa Kata Sandi')
        }else{
            $.ajax({
                async: true,
                url: `${USER_API_URL}set-new-password`,
                type: 'POST',
                data: JSON.stringify(request),
                error: function(res) {
                    const response = JSON.parse(res.responseText)
                    endLoadingButton('#resetpassword-button', 'Daftar')
                    showError("Atur Ulang Kata Sandi", "Gagal melakukan Atur Ulang kata sandi!")
                },
                success: function(res) {
                    endLoadingButton('#resetpassword-button', 'Lupa Kata Sandi')
                    showSuccess("Atur Ulang Kata Sandi", "Sukses melakukan atur ulang kata sandi. Silahkan masuk menggunakan kata sandi baru Anda.", WEB_URL)
                }
            });
        }
    })
});

function getRequestForgetPassword(){
    $.ajax({
        async: true,
        url: `${USER_API_URL}forget-password/${RESETPASS_CODE}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          $('#notfound-section').show()
          $('#reset-password-section').hide()
        },
        success: function(res) {
            RESETPASS_REQUEST = res.data
            $('#notfound-section').css("display", "none")
            $('#reset-password-section').show()
        }
    });
}

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
    if(data.web_icon){
      $("#favicon").attr("href", API_URL+data.web_icon);
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

function enableResetPassword(){
    $('#resetpassword-button').prop("disabled", false);
}

function disableResetPassword(){
    $('#resetpassword-button').prop("disabled", true);
}
/* Animation */