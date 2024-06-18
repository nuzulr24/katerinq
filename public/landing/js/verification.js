let VERIFICATION_CODE = window.location.pathname.split("/").pop(),
    USER_API_URL = API_URL + "user/",
    SESSION = localStorage.getItem("user-token")
$(document).ready(function(){
    getVerification()
});

function getVerification(){
    $.ajax({
        async: true,
        url: `${USER_API_URL}verification/${VERIFICATION_CODE}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          $('#verification-section').html("Kode verifikasi yang Anda masukan tidak valid!")
          $('#back-to-website').hide()
        },
        success: function(res) {
            $('#verification-section').html(`Selamat!<br>Verifikasi akun Anda berhasil.`)
        }
    });
}