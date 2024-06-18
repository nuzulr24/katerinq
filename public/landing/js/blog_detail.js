let PAGE_URL = window.location.pathname.split("/").pop()


$(document).ready(function(){
  console.log("PAGE URL: ", PAGE_URL)
  $('#blog-breadcrumb').hide()
  $('#blog-title').hide()
  $('#search-blog-form').hide()
  getBlogDetail()
})

function getBlogDetail(){
  $.ajax({
    async: true,
    url: `${BLOG_API_URL}by-alias/${PAGE_URL}`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
      renderBlogNotFound()
    },
    success: function(res) {
      const data = res.data
      let blog_category_alias = data.blog_category_name.replace(" ", "-")
      blog_category_alias = blog_category_alias.trim()
      renderBlog(res.data)
      getBlogRelated(blog_category_alias)
    }
  });
}

function getBlogRelated(blog_category_alias){
  let queryParams = `?page_number=0&page_size=5&draw=1&status=PUBLISH&blog_category_alias=${blog_category_alias}`

  $.ajax({
    async: true,
    url: `${BLOG_API_URL}${queryParams}`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
    },
    success: function(res) {
      if(res.data.length > 1){
        $('#blog-related-section').css("display", "block")
        renderBlogRelated(res.data)
      }else{
        $('#blog-related-section').css("display", "none")
      }
    }
  });
}

function renderBlog(data){
  $('#blog-hero').css("background-image", `url(${data.full_img_url})`)
  $('#blog-breadcrumb-title').html(data.title)
  $('#blog-detail-category').html(data.blog_category_name)
  $('#blog-detail-date').html(formatDateID(data.created_at))
  $('#blog-detail-title').html(data.title)
  $('#blog-detail-content').html(data.content)

  $("meta[name='description']").attr("content", data.meta_description);
  $("meta[name='keywords']").attr("content", data.meta_keywords);
}

function renderBlogNotFound(){
  $('#blog-notfound').show()
  $('#blog-image-section').hide()
  $('#blog-nav-section').hide()
  $('#blog-content-section').hide()
}

function renderBlogRelated(blog){
  let blogHtml = ``
  for(const item of blog){
    const link = `${WEB_URL}blog/${item.alias}`
    let plainText = item.content.replace(/<[^>]+>/g, '');
    plainText = plainText.length > 200 ? plainText.slice(0, 200) + "..." : plainText

    const itemHtml = `<div class="swiper-slide h-auto pb-3">
      <article class="card border-0 shadow-sm h-100 mx-2">
        <div class="position-relative">
          <a href="${link}" class="position-absolute top-0 start-0 w-100 h-100" aria-label="Read more"></a>
          <img src="${item.full_img_url}" class="card-img-top" style="height:300px;object-fit: cover;" alt="Image">
        </div>
        <div class="card-body pb-4">
          <div class="mb-3">
            <span class="badge fs-sm bg-primary text-white">${item.blog_category_name}</span><br>
            <span class="fs-sm text-muted">${formatDateID(item.created_at)}</span>
          </div>
          <h3 class="h5 mb-0">
            <a href="${link}">${sortText(plainText, 40)}</a>
          </h3>
        </div>
      </article>
    </div>`
    blogHtml += itemHtml
  }

  $('#blog-related').html(blogHtml)
  const swiper = new Swiper('#blog-related-swiper', {
    "slidesPerView": 1,
    "centeredSlides": false,
    "spaceBetween": 8,
    "loop": false,
    "pagination": {
      "el": ".swiper-pagination",
      "clickable": true
    },
    "breakpoints": {
      "500": {
        "slidesPerView": 2,
        "spaceBetween": 24
      },
      "1000": {
        "slidesPerView": 4,
        "spaceBetween": 24
      },
      "1500": {
        "slidesPerView": 4,
        "spaceBetween": 24
      }
    }
})
}
