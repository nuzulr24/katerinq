let WEBSITE_ALIAS = decodeURIComponent(window.location.pathname.split("/").pop()),
    WEBSITE

$(document).ready(function(){
    getWebsiteDetail()
});

function getWebsiteDetail(){
    let website_alias_and_code = unformatWebsiteAlias(WEBSITE_ALIAS)
    let arr = website_alias_and_code.split(".")
    let unique_code = arr.pop()
    let web_url = arr.join(".")
    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}by-url-and-code?url=${web_url}&code=${unique_code}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            WEBSITE = res.data
            renderWebsiteDetail(res.data)
            getReviewByWebsite(WEBSITE.id)
        }
    });
}

function getReviewByWebsite(){
    $.ajax({
        async: true,
        url: `${REVIEW_API_URL}website/${WEBSITE.id}?page_number=0&page_size=10`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            renderReview(res.data)
        }
    });
}

function renderWebsiteDetail(website){
    const description = website.website_description || "-"
    const contentPrice = website.content_price ? formatRupiah(website.content_price) : "-"
    const userLink = `${website.user.first_name}-${website.user.last_name}-${website.user.unique_code}`
    $('#website-seller').html(`<a style="text-decoration: none" class="text-dark" href="${WEB_URL}users/${encodeURIComponent(userLink)}">${website.user.first_name} ${website.user.last_name}</a>`)
    $('#website-url').attr("href", `${website.website_url}`)
    $('#website-price').html(formatRupiah(website.guest_post_price, true))
    $('#website-content_price').html(formatRupiah(contentPrice, true))
    $('#website-delivery_time').html(website.delivery_time)
    $('#website-word_limit').html(website.word_limit)
    $('#website-da').html(website.da)
    $('#website-pa').html(website.pa)
    $('#website-description').html(description)
    $('#website-guest_post_sample').html(website.guest_post_sample)
    $('#website-order').prop("href", `${WEB_URL}guestpost/order/${formatWebsiteAlias(website.website_url)}-${website.unique_code}`)

    let categoryHtml = ``
    for(const item of website.category){
        const badge = `<span class="badge badge-pill bg-dark">${item.category_name}</span> &nbsp;`
        categoryHtml += badge
    }
    $('#website-category').html(categoryHtml)
}

function renderReview(reviews){
    if(reviews.length > 0){
        let reviews_html = ``
        for(let review of reviews){
            // let date = new Date(review.created_at)
            let ratingStar = generateRatingStar(review.rating)

            let review_html = `<div class="swiper-slide h-auto pt-4">
                <figure class="d-flex flex-column h-100 px-2 px-sm-0 mb-0">
                <div class="card h-100 position-relative border-0 shadow-sm pt-4">
                    <span class="btn btn-icon btn-primary shadow-primary pe-none position-absolute top-0 start-0 translate-middle-y ms-4">
                        <i class="bx bxs-quote-left"></i>
                    </span>
                    <blockquote class="card-body pb-3 mb-0">
                    <p class="mb-0">
                        ${review.review}
                    </p>
                    </blockquote>
                    <div class="card-footer border-0 text-nowrap pt-0">
                        ${ratingStar}
                    </div>
                </div>
                <figcaption class="d-flex align-items-center ps-4 pt-4">
                    <img src="${WEB_URL}assets/img/default-avatar.png" width="48" class="rounded-circle" alt="Fannie Summers">
                    <div class="ps-3">
                        <h6 class="fs-sm fw-semibold mb-0">${review.buyer_name}</h6>
                    </div>
                </figcaption>
                </figure>
            </div>`
            reviews_html += review_html
        }

        $('#review-swiper-wrapper').html(reviews_html)

        const swiper = new Swiper('#review-swiper', {
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
        
    }else{
        $('#no-review').css("display", "block")
        $('#review-list').css("display", "none")
    }
}