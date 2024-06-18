let PAGE_NUMBER = 1,
    PAGE_URL = window.location.pathname.split("/").pop()
    SEARCH = ""

$(document).ready(function(){
  console.log("PAGE URL: ", PAGE_URL)
  // if(PAGE_URL != 'blog' && PAGE_URL != 'blog/'){
  //   $('#blog-title').html(`<h2>${PAGE_URL} | Blog</h2>`)
  // }

  getBlogList()

  $("body").delegate("#infinity-scroll-button", "click", function(e){
    console.log("READ MORE..")
    PAGE_NUMBER += 1
    startLoadingButton("#infinity-scroll-button")
    getBlogList()
  })

})

function getBlogList(){
  let queryParams = `?page_number=${PAGE_NUMBER-1}&page_size=5&draw=1&status=PUBLISH`
  if(PAGE_URL != 'blog' && PAGE_URL != 'blog/'){
    queryParams += `&blog_category_alias=${PAGE_URL}`
  }
  if(SEARCH){
    queryParams += `&search=${SEARCH}`
  }

  $.ajax({
    async: true,
    url: `${BLOG_API_URL}${queryParams}`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
      $('#blog-list').css("display", "none")
      $('#blog-notfound').css("display", "block")
    },
    success: function(res) {
      $('#blog-list').html("")
      if(res.data.length > 0){
        $('#blog-list').css("display", "block")
        $('#blog-notfound').css("display", "none")
        let totalPage = Math.ceil(res.recordsTotal/5)
        renderBlog(res.data)
        renderInfinityScroll(totalPage)
      }else{
        $('#blog-list').css("display", "none")
        $('#blog-notfound').css("display", "block")
      }
    }
  });
}

function renderBlog(blog){
  let blogHtml = ``
  for(const item of blog){
    const link = `${WEB_URL}blog/${item.alias}`
    let plainText = item.content.replace(/<[^>]+>/g, '');

    const itemHtml = `<article class="card border-0 bg-transparent me-xl-5 mb-4">
      <div class="row g-0">
        <div class="col-sm-5 position-relative bg-position-center bg-repeat-0 bg-size-cover rounded-3" style="background-image: url('${item.full_img_url}'); min-height: 15rem;">
          <a href="${link}" class="position-absolute top-0 start-0 w-100 h-100" aria-label="Read more"></a>
        </div>
        <div class="col-sm-7">
          <div class="card-body px-0 pt-sm-0 ps-sm-4 pb-0 pb-sm-4">
            <span class="badge fs-sm text-white bg-info shadow-info text-decoration-none mb-3">${item.blog_category_name}</span>
            <h3 class="h4">
              <a href="${link}">${sortText(item.title)}</a>
            </h3>
            <p class="mb-4">${sortText(plainText, 200)}</p>
            <div class="d-flex align-items-center text-muted">
              <div class="fs-sm pe-3 me-3">${formatDateID(item.created_at)}</div>
            </div>
          </div>
        </div>
      </div>
    </article>
    <div class="pb-2 pb-lg-3"></div>`
    blogHtml += itemHtml
  }

  $('#blog-list').append(blogHtml)
}

function renderInfinityScroll(totalPage){
  if(totalPage > PAGE_NUMBER){
    $('#blog-infinity-scroll').html(`<button class="btn btn-lg btn-outline-primary w-100 mt-4" id="infinity-scroll-button">
      <i class="bx bx-down-arrow-alt fs-xl me-2"></i> Tampilkan lebih
    </button>`)
    endLoadingButton("#infinity-scroll-button", `<i class="bx bx-down-arrow-alt fs-xl me-2"></i> Tampilkan lebih`)
  }else{
    $('#blog-infinity-scroll').html("")
  }
  
}
