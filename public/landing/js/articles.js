let ARTICLE_URL = window.location.pathname.split("/").pop()

$(document).ready(function(){
    getArticle()
})

function getArticle(){
    $.ajax({
        async: true,
        url: `${PAGE_API_URL}by-url/${ARTICLE_URL}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          $('#article-title').html('404')
          $('#articles').html('<p align="center">Artikel tidak ditemukan!</p>')
        },
        success: function(res) {
            renderArticle(res.data)
        }
    });
}

function renderArticle(data){
    $('#article-title').html(data.title)
    $('#articles').html(data.content)
}