let PARAMS = getQueryParams(),
    PAGE_NUMBER = 1

$(document).ready(function(){
    getProducts()
    renderSearchBadge()

    $('#product-category-filter').change(function(){
        if($(this).val() && $(this).val() != ""){
            if("search" in PARAMS) window.location.href = `products?search=${PARAMS.search}&category=${$(this).val()}`
            else window.location.href = `products?category=${$(this).val()}`
        }else{
            if("search" in PARAMS) window.location.href = `products?search=${PARAMS.search}`
            else window.location.href = `products`
        }
    })

    $("body").delegate("#search-remove-button", "click", function(){
        if("category" in PARAMS) window.location.href = `products?category=${PARAMS.category}`
        else window.location.href = `products`
    })

    $("body").delegate(".product-pagination-item", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()
    
        let page = $(this).data('page')
        PAGE_NUMBER = page
        console.log('page:',PAGE_NUMBER)
        getProducts()
        window.scrollTo(0, 0);
    });

    $("body").delegate(".product-pagination-previous", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()

        PAGE_NUMBER -= 1
        console.log('page:',PAGE_NUMBER)
        getProducts()
        window.scrollTo(0, 0);
    })

    $("body").delegate(".product-pagination-next", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()

        PAGE_NUMBER += 1
        console.log('page:',PAGE_NUMBER)
        getProducts()
        window.scrollTo(0, 0);
    })
})

function getProducts(){
    let queryParams = `?page_number=${PAGE_NUMBER-1}&page_size=12&draw=1`
    if("search" in PARAMS) queryParams += `&search=${PARAMS.search}`
    if("category" in PARAMS) queryParams += `&category_id=${PARAMS.category}`
    if("new_arrival" in PARAMS) queryParams += `&is_new_arrival=${PARAMS.new_arrival}`

    $.ajax({
        async: true,
        url: `${PRODUCT_API_URL}${queryParams}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            if(res.data.length > 0){
                $('#products').css("display", "flex")
                $('#products-notfound').css("display", "none")
                let totalPage = Math.ceil(res.recordsTotal/12)
                renderProducts(res.data)
                renderProductPagination(totalPage)
            }else{
                $('#products').css("display", "none")
                $('#products-notfound').css("display", "block")
            }
        }
    });
}

function renderProducts(data){
    let productsHtml = ``,
        col = 'col-12'
        if(data.length >= 4){
            col = 'col-lg-3 col-md-4 col-sm-6 col-6'
        } else if(data.length == 3){
            col = 'col-lg-4 col-md-4 col-sm-6 col-6'
        } else if(data.length == 2){
            col = 'col-6'
        } else {
            col = 'col-12'
        }

    for(let product of data){
        let imgUrl = product.full_thumbnail_url ? product.full_thumbnail_url : `${WEB_URL}assets/img/default.png`
        let price = CURRENCY == 'usd' ? formatDollar(product.price) : formatRupiah(product.price, true)

        let productHtml = `<div class="${col} my-2">
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    <a href="products/${formatUrl(product.title)}">
                        <img src="${WEB_URL}assets/img/placeholder-image.png" data-src="${imgUrl}" class="img-fluid product-image product-lazyload" alt="${product.title}">
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="product-title">${product.title}</h1>
                    <span class="product-price" id="P${product.id}" data-id="P${product.id}" data-price="${product.price}">${price}</span>
                </div>
            </div>
        </div>`
        productsHtml += productHtml
    }
    $('#products').html(productsHtml)
    $('.product-lazyload').lazyload()
}

function renderSearchBadge(){
    if("search" in PARAMS){
        let searchBagdeHtml = `
        <span>Search: </span>
        <span class="tag badge badge-warning py-1">
            ${PARAMS.search}
            <a id="search-remove-button"><i class="remove fas fa-times"></i></a> 
        </span>`
        $('#search-badge').html(searchBagdeHtml)
    }
}

function renderProductPagination(totalPage){
    if(totalPage > 1){
        let fullPagesHtml = ``
        let previousDisable = PAGE_NUMBER == 1 ? 'disabled' : ''
        let nextDisable = PAGE_NUMBER == totalPage ? 'disabled' : ''
        let isPageLimit = totalPage > 5 ? true : false;

        let previousHtml = `<li class="page-item ${previousDisable}">
            <a class="page-link product-pagination-previous" href="#">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>`
        let nextHtml = `<li class="page-item ${nextDisable}">
            <a class="page-link product-pagination-next" href="#">
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
                        <a class="page-link product-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                    </li>`
                }else if(i == PAGE_NUMBER-1 || i == PAGE_NUMBER+1){
                    pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                        <a class="page-link product-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                    </li>`
                }else if(i == PAGE_NUMBER-2 || i == PAGE_NUMBER+2){
                    pageHtml = `<li class="page-item disabled">
                        <span class="page-link product-pagination-item">...</span>
                    </li>`
                }
            }else{
                pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                    <a class="page-link product-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                </li>`
            }
            pagesHtml += pageHtml
        }
        fullPagesHtml = `${previousHtml}${pagesHtml}${nextHtml}`
        $('#product-pagination').html(fullPagesHtml)
    }
    
}