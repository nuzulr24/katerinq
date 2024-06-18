$(document).ready(function(){
  getCategory()
  getLatestBlog()

  $('#search-blog-form').submit(function(e){
    e.preventDefault()
    SEARCH = $('#search-blog').val()
    console.log("search:", SEARCH)
    $('#blog-list').html(`<p class="placeholder-glow">
      <span class="placeholder col-12"></span>
      <span class="placeholder col-12"></span>
      <span class="placeholder col-12"></span>
      <span class="placeholder col-12"></span>
    </p>`)
    $('#blog-infinity-scroll').html("")

    getBlogList()
  })
})

function getCategory(){
  $.ajax({
    async: true,
    url: `${BLOG_CATEGORY_API_URL}all-and-blog-total`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
    },
    success: function(res) {
      renderCategory(res.data)
    }
  });
}

function getLatestBlog(){
  $.ajax({
    async: true,
    url: `${BLOG_API_URL}?page_number=0&page_size=5&status=PUBLISH`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
    },
    success: function(res) {
      renderLatestBlog(res.data)
    }
  });
}

function renderCategory(categories){
  let categoriesHtml = ``
  categories.forEach(category => {
    let itemHtml = `<li class="nav-item mb-1">
      <a href="${WEB_URL}blog/category/${category.alias}" class="nav-link py-1 px-0">
        ${category.name} <span class="fw-normal opacity-60 ms-1">(${category.blog_total})</span>
      </a>
    </li>`
    categoriesHtml += itemHtml
  });

  $("#blog-category-list").html(categoriesHtml)
}

function renderLatestBlog(blog){
  let blogHtml = ``
  for(const item of blog){
    const link = `${WEB_URL}blog/${item.alias}`
    const item_html = `<li class="border-bottom pb-3 mb-3">
      <h4 class="h6 mb-2">
        <a href="${link}">${sortText(item.title)}</a>
      </h4>
      <div class="d-flex align-items-center text-muted pt-1">
        <div class="fs-xs border-end pe-3 me-3">${formatDateID(item.created_at)}</div>
      </div>
    </li>`
    blogHtml += item_html
  }

  $('#blog-latest').html(blogHtml)
}