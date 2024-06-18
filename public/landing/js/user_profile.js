let USER_ALIAS  = decodeURIComponent(window.location.pathname.split("/").pop()),
    UNIQUE_CODE = USER_ALIAS.split("-").pop(),
    PAGE_NUMBER = 1

$(document).ready(function(){
  getUserPorifle()
  getUserReview()

  //render datatable
  let website_table = $('#website-datatable').DataTable( {
    processing: true,
    serverSide: true,
    searching: true,
    ordering: false,
    ajax: {
      async: true,
      url: `${WEBSITE_API_URL}by-seller-code`,
      type: "GET",
      dataType: "json",
      crossDomain: true,
      data: function ( d ) {
        let newObj = {}
        let start = d.start
        let size = d.length
        newObj.page_number = d.start > 0 ? (start/size) : 0;
        newObj.page_size = size
        newObj.search = d.search.value
        newObj.draw = d.draw
        newObj.code = UNIQUE_CODE
        d = newObj
        return d
      },
      error: function(res) {
        const response = JSON.parse(res.responseText)
        let isRetry = retryRequest(response)
        if(isRetry) $.ajax(this)
      }
    },
    columns: [
        {
          data: "website_url",
          className: "dt-body-center",
          render: function (data, type, row, meta) {
            let url = `<a href="${data}" target="_blank">${data}</a>`
            if(parseInt(row.is_premium)){
              url += ' <i class="fas fa-crown"></i>'
            }
            return url
          }
        },
        { 
          data: "category",
          render: function (data, type, row, meta) {
            let categoryHtml = ``
            for(const item of data){
              const badge = `<span class="badge badge-pill bg-dark">${item.category_name}</span> &nbsp;`
              categoryHtml += badge
            }
            return categoryHtml
          }
        },
        { 
          data: "guest_post_price",
          render: function (data, type, row, meta) {
            return formatRupiah(data, true)
          }
        },
        { 
          data: "da",
          render: function (data, type, row, meta) {
            return data ? data : '-'
          }
        },
        { 
          data: "pa",
          render: function (data, type, row, meta) {
            return data ? data : '-'
          }
        },
        { 
          data: "status",
          render: function (data, type, row, meta) {
            let color = "light"
            if(data == 'INACTIVE'){
              color = 'danger'
            }else if(data == 'ONVERIFY'){
              color = 'info'
            }else if(data == 'ACTIVE'){
              color = 'primary'
            }else if(data == 'BANNED'){
              color = 'danger'
            }
            let badge = `<span class="badge badge-pill bg-${color}">${data}</span>`

            return badge
          }
        },
        { 
          data: "id",
          render: function (data, type, row, meta) {
            let button = `<a href='${WEB_URL}guestpost/order/${formatWebsiteAlias(row.website_url)}-${row.unique_code}' class='btn btn-primary btn-sm' title='Pesan'>
              <i class='bx bx-cart'></i>&nbsp;Pesan
            </a>`

            return button
          }
        }
    ]
  });

  $("body").delegate(".review-pagination-item", "click", function(e){
    e.preventDefault()
    e.stopImmediatePropagation()

    let page    = $(this).data('page')
    PAGE_NUMBER = page
    console.log('page:',PAGE_NUMBER)
    getUserReview()
    window.scrollTo(0, 0);
  });

  $("body").delegate(".review-pagination-previous", "click", function(e){
    e.preventDefault()
    e.stopImmediatePropagation()

    PAGE_NUMBER -= 1
    console.log('page:',PAGE_NUMBER)
    getUserReview()
    window.scrollTo(0, 0);
  })

  $("body").delegate(".review-pagination-next", "click", function(e){
    e.preventDefault()
    e.stopImmediatePropagation()

    PAGE_NUMBER += 1
    console.log('page:',PAGE_NUMBER)
    getUserReview()
    window.scrollTo(0, 0);
  })
})

function getUserReview(){
  $.ajax({
    async: true,
    url: `${REVIEW_API_URL}seller?code=${UNIQUE_CODE}&page_number=${PAGE_NUMBER-1}&page_size=5`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
      let isRetry = retryRequest(response)
      if(isRetry) $.ajax(this)
    },
    success: function(res) {
      if(res.data.length > 0){
        let totalPage = Math.ceil(res.recordsTotal/5)
        renderUserReview(res.data)
        renderUserReviewPagination(totalPage)
      }else{
        $('#user-review').hide()
        $('#user-review-notfound').show()
      }
    }
  });
}

function getUserPorifle(){
  $.ajax({
    async: true,
    url: `${USER_API_URL}by-unique-code/${UNIQUE_CODE}`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
      let isRetry = retryRequest(response)
      if(isRetry) $.ajax(this)
    },
    success: function(res) {
      renderUserProfile(res.data)
    }
  });
}

function renderUserReview(reviews){
  let reviews_html = ``
  reviews.forEach(item => {
    let item_html = `<figure class="d-flex flex-column px-2 px-sm-0 mb-5">
      <div class="card border-0 shadow-sm pt-4">
        <span class="btn btn-icon btn-primary shadow-primary pe-none position-absolute top-0 start-0 translate-middle-y ms-4">
          <i class="bx bxs-quote-left"></i>
        </span>
        <div class="row">
          <div class="col-8">
            <div class="row card-body pb-3 pt-3">
              <div class="col-2">
                <img src="${WEB_URL}assets/img/default-avatar.png" style="width:100px" class="rounded-circle" alt="Fannie Summers">
              </div>
              <div class="col-10">
                <div>
                  ${generateRatingStar(item.rating)}
                </div>
                <h6 class="fs-sm fw-semibold mb-0">${item.buyer_name}</h6>
                <p class="mb-0">
                  ${item.review}
                </p>
              </div>
            </div>
          </div>
          <div class="col-4 text-end fw-bold px-4" style="font-size:12px">
            ${formatDateID(item.created_at)}
          </div>
        </div>
      </div>
    </figure>`
    reviews_html += item_html
  })

  $('#user-review').html(reviews_html)
}

function renderUserProfile(data){
  $('#user-fullname').html(`${data.first_name} ${data.last_name}`)
  $('#user-email').html(data.email)
  $('#user-phone').html(data.phone)
  $('#total-website').html(data.total_website)
  $('#total-order').html(data.order_total)
  $('#total-order-complete').html(data.order_total_complete)
  $('#total-order-ongoing').html(data.order_total_ongoing)
  $('#total-order-cancel').html(data.order_total_cancel)
  $('#user-rating').html(`<input id="user-rating-display" name="user-rating-display" value="${data.rating.average_rating}" class="rating-loading">`)
  $('#user-rating-display').rating({displayOnly: true, step: 0.01});
}

function renderUserReviewPagination(totalPage){
  if(totalPage > 1){
      let fullPagesHtml   = ``
      let previousDisable = PAGE_NUMBER == 1 ? 'disabled' : ''
      let nextDisable     = PAGE_NUMBER == totalPage ? 'disabled' : ''
      let isPageLimit     = totalPage > 5 ? true : false;

      let previousHtml  = `<li class="page-item ${previousDisable}">
          <a class="page-link review-pagination-previous" href="#">
              <i class="fas fa-chevron-left"></i>
          </a>
      </li>`
      let nextHtml      = `<li class="page-item ${nextDisable}">
          <a class="page-link review-pagination-next" href="#">
              <i class="fas fa-chevron-right"></i>
          </a>
      </li>`
      
      let pagesHtml = ``
      for(let i=1; i<=totalPage; i++){
          const pageActive    = i == PAGE_NUMBER ? 'active' : ''
          const pageDisabled  = i == PAGE_NUMBER ? '' : 'href="#"'

          let pageHtml = ``
          if(isPageLimit){
              if(i == PAGE_NUMBER){
                  pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                      <a class="page-link review-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                  </li>`
              }else if(i == PAGE_NUMBER-1 || i == PAGE_NUMBER+1){
                  pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                      <a class="page-link review-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                  </li>`
              }else if(i == PAGE_NUMBER-10 || i == PAGE_NUMBER+10){
                  pageHtml = `<li class="page-item disabled">
                      <span class="page-link review-pagination-item">...</span>
                  </li>`
              }
          }else{
              pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                  <a class="page-link review-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
              </li>`
          }
          pagesHtml += pageHtml
      }
      fullPagesHtml = `${previousHtml}${pagesHtml}${nextHtml}`
      $(`#review-pagination`).html(fullPagesHtml)
  }else{
    $(`#review-pagination`).html("")
  }
  
}