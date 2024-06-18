let PAGE_URL = window.location.pathname.split("/").pop()

$(document).ready(function(){
    getInfoDetail()
})

function getInfoDetail(){
    $.ajax({
        async: true,
        url: `${INFO_API_URL}by-alias/${PAGE_URL}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          $('#info-title').html('404')
          $('#info-text').html('<p align="center">Artikel tidak ditemukan!</p>')
        },
        success: function(res) {
            renderArticle(res.data)
        }
    });
}

function renderArticle(data){
    $('#info-title').html(data.title)
    $('#info-text').html(data.content)
}