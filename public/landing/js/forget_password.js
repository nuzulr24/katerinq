const CONFIG_API_URL = API_URL + "config/",
      USER_API_URL = API_URL + "user/"

$(document).ready(function(){
    getConfig()

    $('#forgetpassword-form').submit(function (e){
        e.preventDefault()
        startLoadingButton("#forgetpassword-button")

        let $form = $(this),
            request = {
                email: $form.find( "input[name='email']" ).val()
            }
    
        console.log("REQUEST: ", request)
        
        $.ajax({
            async: true,
            url: `${USER_API_URL}forget-password`,
            type: 'POST',
            data: JSON.stringify(request),
            error: function(res) {
              const response = JSON.parse(res.responseText)
              endLoadingButton('#forgetpassword-button', 'Kirim email')
              showError("Lupa Kata Sandi", "Gagal melakukan permintaan lupa kata sandi!")
            },
            success: function(res) {
              endLoadingButton('#forgetpassword-button', 'Kirim email')
              showSuccess("Lupa Kata Sandi", "Sukses melakukan permintaan lupa kata sandi. Silahkan melakukan proses selanjutnya melalui email Anda.", WEB_URL)
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
    if(data.web_icon){
      $(".favicon").attr("href", API_URL+data.web_icon);
    }
    if(data.web_logo){
        $('.brand-logo').prop("src", API_URL+data.web_logo)
    }else{
        $('.brand-logo-section').hide()
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

function enableForgetPassword(){
    $('#forgetpassword-button').prop("disabled", false);
}

function disableForgetPassword(){
    $('#forgetpassword-button').prop("disabled", true);
}
/* Animation */