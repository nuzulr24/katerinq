let PRODUCT,
    PRODUCT_ID,
    PRODUCT_URL = window.location.pathname.split("/").pop(),
    PRODUCT_IMAGES = [],
    PRODUCT_VARIANTS = []

$(document).ready(function(){
    getProductDetail()

    $('#product-variant').change(function(){
        let variantStr = $(this).val()
        let selectedVariant = PRODUCT_VARIANTS.find(variant => variant.variants == variantStr)
        if(selectedVariant.stock_qty > 0){
            $('#product-qty').val(1)
            $('#product-qty').attr("max", selectedVariant.stock_qty)
            $('#product-qty').attr("disabled", false)
            $('#addtocart-button').attr("disabled", false)
            $('#nostock').css("display", "none")
        }else{
            $('#product-qty').val(0)
            $('#product-qty').attr("max", 0)
            $('#product-qty').attr("disabled", true)
            $('#addtocart-button').attr("disabled", true)
            $('#nostock').css("display", "block")
        }
    })

    $('#addtocart-form').submit(function(e){
        e.preventDefault()
        startLoadingButton('#addtocart-button')

        let $form = $(this),
            request = {
                product_id: PRODUCT_ID,
                quantity: $('#product-qty').val()
            }
        if(parseInt(PRODUCT.has_variant)) request.product_variant_id = $('#product-variant').find(":selected").val();
        console.log("qty: ",$form.find("input[name='qty']"))

        $.ajax({
            async: true,
            url: `${CART_API_URL}add-item`,
            type: 'POST',
            data: JSON.stringify(request),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            error: function(res) {
              const response = JSON.parse(res.responseText)
              let isRetry = retryRequest(response, window.location.href)
              if(isRetry) $.ajax(this)
              endLoadingButton('#addtocart-button', 'Tambah ke keranjang')
            },
            success: function(res) {
                // CART = res.data
                endLoadingButton('#addtocart-button', 'Tambah ke keranjang')
                showSuccess("Produk", "Produk berhasil ditambahkan ke keranjang")
            }
        });
    })
});

function getProductDetail(){
    $.ajax({
        async: true,
        url: `${PRODUCT_API_URL}by-title?title=${convertUrl(PRODUCT_URL)}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            PRODUCT = res.data
            PRODUCT_ID = res.data.id
            PRODUCT_IMAGES = res.data.images
            PRODUCT_VARIANTS = res.data.variants

            renderImages()
            renderProductDetail()
            renderProductLink()
        }
    });
}

function renderImages(){
    if(PRODUCT_IMAGES.length > 0){
        let mainSlidersHtml = ``
        let thumbnailSlidersHtml = ``
        for(let image of PRODUCT_IMAGES){
            let mainSliderHtml = `<li class="splide__slide">
                <img src="${WEB_URL}assets/img/placeholder-image.png" data-src="${image.full_img_url}" class="img-fluid product-detail-lazyload" alt="${PRODUCT.title}">
            </li>`
            let thumbnailSliderHtml = `<li class="splide__slide">
                <img src="${image.full_thumbnail_url}" class="img-fluid product-detail-lazyload" alt="${PRODUCT.title}">
            </li>`
            mainSlidersHtml += mainSliderHtml
            thumbnailSlidersHtml += thumbnailSliderHtml
        }
        $('#product-main-slider').html(mainSlidersHtml)
        $('#product-thumbnail-slider').html(thumbnailSlidersHtml)

        let thumbnailSlider = new Splide( '.thumbnail-slider', {
            gap       : 10,
            rewind    : true,
            pagination: false,
            breakpoints: {
                600: {
                    fixedWidth : 60,
                    fixedHeight: 44,
                },
            },
            isNavigation: true,
            fixedWidth : 100,
            fixedHeight : 100,
            cover      : true,
        } );

        let mainSlider = new Splide('.main-slider', {
            type      : 'fade',
            rewind    : true,
            pagination: false,
            arrows    : false,
            
        })

        mainSlider.sync( thumbnailSlider );
        mainSlider.mount();
        thumbnailSlider.mount();

        $('.product-detail-lazyload').lazyload()
    }else{
        $('.main-slider').css("display", "none")
        $('.thumbnail-slider').css("display", "none")
        $('#no-image').css("display", "block")
    }
}

function renderProductDetail(){
    let hasVariant = parseInt(PRODUCT.has_variant)
    let price = CURRENCY == 'usd' ? formatDollar(PRODUCT.price) : formatRupiah(PRODUCT.price, true)

    $('#product-header').html(`<h1 id="product-title">${PRODUCT.title}</h1>
    <h2 id="product-price" data-price="${PRODUCT.price}">${price}</h2>`)

    $('#product-description').html(PRODUCT.description)

    if(hasVariant){
        let variantsHtml = ``,
            index = 0,
            indexWithStock = null
        for(let variant of PRODUCT_VARIANTS){
            if(!indexWithStock) indexWithStock = variant.stock_qty > 0 ? index : null

            let variantHtml = `<option value="${variant.id}">${variant.variants.toUpperCase()}</option>`
            
            if(indexWithStock && variant.id == PRODUCT_VARIANTS[indexWithStock].id){
                variantHtml = `<option value="${variant.id}" selected>${variant.variants.toUpperCase()}</option>`
            }
            
            variantsHtml += variantHtml
            index++
        }
        $('#product-variant').html(variantsHtml)
        if(indexWithStock){
            $('#product-qty').attr("max", PRODUCT_VARIANTS[indexWithStock].stock_qty)
        }else{
            $('#product-qty').val(0)
            $('#product-qty').attr("max", 0)
            $('#product-qty').attr("disabled", true)
            $('#addtocart-button').attr("disabled", true)
            $('#nostock').css("display", "block")
        }
    }else{
        $('#variant-section').css("display", "none")
        $('#product-qty').attr("max", PRODUCT.stock_qty)
    }
}

function renderProductLink(){
    $('#social_facebook_link').attr("href", `https://facebook.com/sharer/sharer.php?u=${window.location.href}`)
    $('#social_twitter_link').attr("href", `https://twitter.com/intent/tweet?text=${window.location.href}`)
    $('#social_whatsapp_link').attr("href", `https://wa.me?text=${encodeURIComponent(window.location.href)}`)
}