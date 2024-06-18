$(document).ready(function(){
    getTotalUser()
    getTotalWebsite()
    getCompany()
    getBlogList()

    const company_swiper = new Swiper('#company-swiper', {
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
            "slidesPerView": 6,
            "spaceBetween": 24
          }
        }
    })

    const blog_swiper = new Swiper('#blog-swiper', {
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
            "slidesPerView": 3,
            "spaceBetween": 24
          },
          "1500": {
            "slidesPerView": 5,
            "spaceBetween": 24
          }
        }
    })

    const review_swiper = new Swiper('#review-swiper', {
      "spaceBetween": 24,
      "loop": true,
      "pagination": {
        "el": ".swiper-pagination",
        "clickable": true
      },
      "navigation": {
        "prevEl": "#testimonial-prev",
        "nextEl": "#testimonial-next"
      }
    })
})

function getCompany(){
    $.ajax({
        async: true,
        url: `${COMPANY_API_URL}all`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
          renderCompany(res.data)
        }
    });
}

function getTotalUser(){
    $.ajax({
        async: true,
        url: `${USER_API_URL}count`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
          renderTotalUser(res.data)
        }
    });
}

function getTotalWebsite(){
    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}count?status=ACTIVE`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
          renderTotalWebsite(res.data)
        }
    });
}

function getBlogList(){
    let queryParams = `?page_number=0&page_size=5&draw=1&status=PUBLISH`
  
    $.ajax({
      async: true,
      url: `${BLOG_API_URL}${queryParams}`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
        $('#blog-section').css("display", "none")
      },
      success: function(res) {
        if(res.data.length > 0){
          renderBlog(res.data)
        }else{
          $('#blog-section').css("display", "none")
        }
      }
    });
}
  
function renderBlog(blog){
    let blogHtml = ``
    for(const item of blog){
      const link = `${WEB_URL}blog/${item.alias}`
      let plainText = item.content.replace(/<[^>]+>/g, '');
  
      const itemHtml = `<div class="swiper-slide h-auto py-3">
            <article class="card p-md-3 p-2 border-0 shadow-sm card-hover-primary h-100 mx-2">
                <div class="card-body pb-0">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge fs-sm text-nav bg-secondary text-decoration-none position-relative zindex-2">${item.blog_category_name}</span><br>
                    </div>
                    <h5>
                        <a href="${link}" class="stretched-link">${sortText(item.title, 20)}</a>
                    </h5>
                    <p class="mb-0">${sortText(plainText, 70)}</p>
                </div>
            </article>
        </div>`
      blogHtml += itemHtml
    }
  
    $('#blog-list').html(blogHtml)
}

function renderCompany(data){
    let companyHtml = ``
    for(let company of data){
        let itemHtml = `<div class="swiper-slide py-3">
            <div class="card card-body card-hover px-2 mx-2">
                <img src="${company.full_img_url}" class="d-block mx-auto my-2" style="height:100px" alt="${company.name}">
            </div>
        </div>`
        companyHtml += itemHtml
    }
    $('#company-list').html(companyHtml)
}

function renderFAQ(data){
    let faqHtml = ''
    for(let i=0; i<data.length; i++){
        const isShow = i == 0 ? "show" : ''
        const itemHtml = `<li data-aos="fade-up" data-aos-delay="${i+1}00">
            <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" class="collapse" data-bs-target="#faq-list-${i+1}">${data[i].question}<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
            <div id="faq-list-${i+1}" class="collapse ${isShow}" data-bs-parent=".faq-list">
            <p>
                ${data[i].answer}
            </p>
            </div>
        </li>`
        faqHtml += itemHtml
    }
    $('#home-faq').html(faqHtml)
}

function renderTotalUser(total){
    $('#total-user').html(total)
}

function renderTotalWebsite(total){
    $('#total-website').html(total)
}
