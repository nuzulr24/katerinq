let PAGE_NUMBER = 1,
    FILTER_SEARCH = "",
    FILTER_CATEGORY = "",
    FILTER_DA = 0,
    FILTER_PA = 0
    FILTER_PRICE = 0

$(document).ready(function(){
    getCategory()
    getWebsite()
    getWebsitePremium()
    getWebsiteRecommended()
    getWebsiteTopSelling()

    $('#apply-filter-button').click(function(){
        PAGE_NUMBER = 1
        getWebsite()
    })

    $('#reset-filter-button').click(function(){
        PAGE_NUMBER = 1
        FILTER_SEARCH = ""
        FILTER_CATEGORY = ""
        FILTER_DA = 0
        FILTER_PRICE = 0
        FILTER_PA = 0
        $('#filter-search').val("")
        $('#filter-da').val(FILTER_DA)
        $('#filter-pa').val(FILTER_PA)
        $('#filter-kategori').val("").change();
        $('#da-val').html(FILTER_DA)
        $('#pa-val').html(FILTER_DA)
        $('#price-val').html(FILTER_PRICE)

        getWebsite()
    })

    $("body").delegate(".website-pagination-item", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()
    
        let page = $(this).data('page')
        PAGE_NUMBER = page
        getWebsite()
        window.scrollTo(0, 0);
    });

    $("body").delegate(".website-pagination-previous", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()

        PAGE_NUMBER -= 1
        getWebsite()
        window.scrollTo(0, 0);
    })

    $("body").delegate(".website-pagination-next", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()

        PAGE_NUMBER += 1
        getWebsite()
        window.scrollTo(0, 0);
    })

    /* filter */
    $('#filter-da').change(function(){
        FILTER_DA = $(this).val()
        $('#da-val').html(FILTER_DA)
    })
    $('#filter-pa').change(function(){
        FILTER_PA = $(this).val()
        $('#pa-val').html(FILTER_PA)
    })
    $('#filter-price').change(function(){
        FILTER_PRICE = $(this).val()
        $('#price-val').html(formatRupiah(FILTER_PRICE, true))
        console.log(FILTER_PRICE);
    })
    $('#filter-kategori').change(function(){
        FILTER_CATEGORY = $(this).val()
    })
    $('#filter-search').change(function(){
        FILTER_SEARCH = $(this).val()
    })
})

function getCategory(){
    $.ajax({
      async: true,
      url: `${CATEGORY_API_URL}all`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
      },
      success: function(res) {
          renderCategory(res.data)
      }
    });
}

function getWebsite(){
    $('#apply-filter-button').prop("disabled", true)
    $('#reset-filter-button').prop("disabled", true)

    let queryParams = `?page_number=${PAGE_NUMBER-1}&page_size=10&draw=1&status=ACTIVE`
    if(FILTER_SEARCH) queryParams += `&search=${FILTER_SEARCH}`
    if(FILTER_CATEGORY) queryParams += `&category_id=${FILTER_CATEGORY}`
    if(FILTER_DA) queryParams += `&da=${FILTER_DA}`
    if(FILTER_PA) queryParams += `&pa=${FILTER_PA}`
    if(FILTER_PRICE) queryParams += `&max_price=${FILTER_PRICE}`

    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}${queryParams}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          $('#apply-filter-button').prop("disabled", false)
          $('#reset-filter-button').prop("disabled", false)
        },
        success: function(res) {
            $('#apply-filter-button').prop("disabled", false)
            $('#reset-filter-button').prop("disabled", false)

            if(res.data.length > 0){
                $('#website').css("display", "block")
                $('#website-notfound').css("display", "none")
                let totalPage = Math.ceil(res.recordsTotal/10)
                renderWebsite(res.data, 'website')
                renderWebsitePagination(totalPage, "website")
            }else{
                $('#website').css("display", "none")
                $('#website-notfound').css("display", "block")
            }
        }
    });
}

function getWebsitePremium(){
    let queryParams = `?page_number=${PAGE_NUMBER-1}&page_size=10&draw=1`

    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}premium/${queryParams}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            if(res.data.length > 0){
                $('#website').css("display", "block")
                $('#website-notfound').css("display", "none")
                let totalPage = Math.ceil(res.recordsTotal/10)
                renderWebsite(res.data, 'website-premium')
                renderWebsitePagination(totalPage, "website-premium")
            }else{
                $('#website-premium').css("display", "none")
            }
        }
    });
}

function getWebsiteRecommended(){
    let queryParams = `?page_number=0&page_size=10`

    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}recommended${queryParams}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            if(res.data.length > 0){
                $('#website-recommended').css("display", "block")
                $('#website-recommended-notfound').css("display", "none")
                renderWebsite(res.data, 'website-recommended')
            }else{
                $('#website-recommended').css("display", "none")
                $('#website-recommended-notfound').css("display", "block")
            }
        }
    });
}

function getWebsiteTopSelling(){
    let queryParams = `?page_number=0&page_size=10&draw=1`

    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}top-selling${queryParams}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            if(res.data.length > 0){
                $('#website-top-selling').css("display", "block")
                $('#website-top-selling-notfound').css("display", "none")
                renderWebsite(res.data, 'website-top-selling')
            }else{
                $('#website-top-seeling').css("display", "none")
                $('#website-top-selling-notfound').css("display", "block")
            }
        }
    });
}

function renderCategory(category){
    let categoryHtml = `<option value="">-Pilih Kategori-</option>`
    for(const item of category){
      const itemHtml = `<option value="${item.id}">${item.name}</option>`
      categoryHtml += itemHtml
    }
  
    $('#filter-kategori').html(categoryHtml)
}

function renderWebsite(data, type){
    let websiteHtml = ``
    let badge = type == 'website-premium' ? '<i class="fas fa-crown"></i>' : ''

    for(let website of data){
        let price = formatRupiah(website.guest_post_price, true)
        let categoryHtml = ``
        for(const item of website.category){
            const badge = `<span class="badge badge-pill bg-dark">${item.category_name}</span> &nbsp;`
            categoryHtml += badge
        }

        let itemHtml = `<tr>
            <td><a href="${WEB_URL}guestpost/site/${formatWebsiteAlias(website.website_url)}-${website.unique_code}">${website.website_url}</a> ${badge}</td>
            <td>${categoryHtml}</td>
            <td>${website.da}</td>
            <td>${website.pa}</td>
            <td>${website.link_type}</td>
            <td>${price}</td>
            <td class='text-center'>
                <a href='${WEB_URL}guestpost/order/${formatWebsiteAlias(website.website_url)}-${website.unique_code}' class='btn btn-primary btn-sm' title='Pesan'>
                    <i class='bx bx-cart'></i>&nbsp;Pesan
                </a>
            </td>
        </tr>`
        websiteHtml += itemHtml
    }
    $(`#${type}-datatable`).html(websiteHtml)
}

function renderWebsitePagination(totalPage, type){
    if(totalPage > 1){
        let fullPagesHtml = ``
        let previousDisable = PAGE_NUMBER == 1 ? 'disabled' : ''
        let nextDisable = PAGE_NUMBER == totalPage ? 'disabled' : ''
        let isPageLimit = totalPage > 5 ? true : false;

        let previousHtml = `<li class="page-item ${previousDisable}">
            <a class="page-link website-pagination-previous" href="#">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>`
        let nextHtml = `<li class="page-item ${nextDisable}">
            <a class="page-link website-pagination-next" href="#">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>`
        
        let pagesHtml = ``
        for(let i=1; i<=totalPage; i++){
            const pageActive = i == PAGE_NUMBER ? 'active' : ''
            const pageDisabled = i == PAGE_NUMBER ? '' : 'href="#"'
            let pageHtml = ``
            if(isPageLimit){
                if(i == PAGE_NUMBER){
                    pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                        <a class="page-link website-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                    </li>`
                }else if(i == PAGE_NUMBER-1 || i == PAGE_NUMBER+1){
                    pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                        <a class="page-link website-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                    </li>`
                }else if(i == PAGE_NUMBER-10 || i == PAGE_NUMBER+10){
                    pageHtml = `<li class="page-item disabled">
                        <span class="page-link website-pagination-item">...</span>
                    </li>`
                }
            }else{
                pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                    <a class="page-link website-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                </li>`
            }
            pagesHtml += pageHtml
        }
        fullPagesHtml = `${previousHtml}${pagesHtml}${nextHtml}`
        $(`#${type}-pagination`).html(fullPagesHtml)
    }else{
        $(`#${type}-pagination`).html("")
    }
    
}