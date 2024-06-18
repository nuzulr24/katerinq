let PARAMS = getQueryParams(),
    USER_API_URL = API_URL + "user/",
    CONFIG_API_URL = API_URL + "config/",
    SESSION = localStorage.getItem("user-token")

$(document).ready(function(){
    if(SESSION){ 
        window.location.href = WEB_URL
    }

    getConfig()

    $('#login-form').submit(function (e){
        e.preventDefault()
        startLoadingButton("#login-button")

        let $form = $(this),
            request = {
                email: $form.find( "input[name='email']" ).val(),
                password: $form.find( "input[name='password']" ).val()
            }

        console.log("REQUEST: ", request)
        $.ajax({
            async: true,
            url: `${USER_API_URL}login`,
            type: 'POST',
            data: JSON.stringify(request),
            error: function(res) {
                const response = JSON.parse(res.responseText)
                endLoadingButton('#login-button', 'Masuk')
                showError("Masuk", "Email atau kata sandi salah!")
            },
            success: function(res) {
                endLoadingButton('#login-button', 'Masuk')
                setSession(res.data)
                let redirectUrl = PARAMS && PARAMS.source ? PARAMS.source : `${WEB_URL}account/dashboard`
                window.location.href = redirectUrl
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

function enableLogin(){
    $('#login-button').prop("disabled", false);
}

function disableLogin(){
    $('#login-button').prop("disabled", true);
}
/* Animation */

/* Session */
function setSession(data){
    console.log("SET NEW SESSION")
    localStorage.setItem("user-token", data.access_token)
    localStorage.setItem("user-refresh-token", data.refresh_token)
}
/* Session */

/* Common */
function getQueryParams(){
    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    return params
}
/* Common */

function renderIcon(data){
    console.log("web icon: ", data.web_icon)
    if(data.web_icon){
        $(".favicon").attr("href", API_URL+data.web_icon);
    }
    if(data.web_logo){
        $('#login-logo').attr("src", API_URL+data.web_logo);
    }
}