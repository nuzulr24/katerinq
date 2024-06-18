let CART,
    CART_ITEMS = [],
    CART_ITEM_ID

$(document).ready(function(){
    checkScreenSize()
    getCartDetail()

    $("body").delegate('.item-update-toggle', 'click', function(){
        CART_ITEM_ID = $(this).data("itemid")
        let max = $(this).data("max")
        let qty = $(this).data("qty")
        $('#item-update-qty-max').attr("max", max)
        $('#item-update-qty').val(qty)
    })

    $("body").delegate('.item-delete-toggle', 'click', function(){
        CART_ITEM_ID = $(this).data("itemid")
    })

    $('#item-update-form').submit(function(e){
        e.preventDefault()
        startLoadingButton('#item-update-button')

        let $form = $(this),
            request = {
                id: CART_ITEM_ID,
                quantity: $form.find("input[name='qty']").val()
            }

        $.ajax({
            async: true,
            url: `${CART_API_URL}update-item`,
            type: 'PUT',
            data: JSON.stringify(request),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            error: function(res) {
              const response = JSON.parse(res.responseText)
              let isRetry = retryRequest(response, window.location.href)
              if(isRetry) $.ajax(this)
              endLoadingButton('#item-update-button', 'Ubah')
            },
            success: function(res) {
                location.reload();
            }
        });
    })

    $('#item-delete-button').click(function(){
        startLoadingButton('#item-delete-button')

        $.ajax({
            async: true,
            url: `${CART_API_URL}remove-item/${CART_ITEM_ID}`,
            type: 'DELETE',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            error: function(res) {
              const response = JSON.parse(res.responseText)
              let isRetry = retryRequest(response, window.location.href)
              if(isRetry) $.ajax(this)
              endLoadingButton('#item-delete-button', 'Iya')
            },
            success: function(res) {
                getCartDetail()
            }
        });
    })
})

function checkScreenSize(){
    if(WINDOW_WIDTH < 576){
        $('#cart-items-web').css('display', 'none')
        $('#cart-items-mobile').css('display', 'block')
    }
}

function getCartDetail(){
    $.ajax({
        async: true,
        url: `${CART_API_URL}detail`,
        type: 'GET',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)          
        },
        success: function(res) {
            CART = res.data
            if(CART){
                CART_ITEMS = CART.items
                if(CART_ITEMS.length > 0) renderCartItems()
                else renderEmptyCart()
            }else{
                renderEmptyCart()
            }
        }
    });
}

function renderEmptyCart(){
    $('#no-cart-items').css("display", "block")
    $('#cart-items').css("display", "none")
    $('#checkout-button').addClass("disabled")
}

function renderCartItems(){
    if(WINDOW_WIDTH > 575){
        let itemsHtml = ``
        for(let item of CART_ITEMS){
            let itemImages = item.product_images
            let productVariant = item.product_variant
            let imgUrl = `${WEB_URL}assets/img/default.png`
            if(itemImages.length > 0){
                imgUrl = itemImages[0].full_thumbnail_url    
            }
            let stockQty = productVariant ? productVariant.stock_qty : item.product.stock_qty
            let variant = productVariant ? productVariant.variants : ''
            let price = CURRENCY == 'usd' ? formatDollar(item.price) : formatRupiah(item.price.toString(), true)
            let amount = CURRENCY == 'usd' ? formatDollar(item.amount) : formatRupiah(item.amount.toString(), true)

            let itemHtml = `<tr>
                <th>
                    <img src="${WEB_URL}assets/img/placeholder-image.png" data-src="${imgUrl}" class="img-fluid cart-item-image cart-item-lazyload" alt="${item.product.title}${variant}">
                </th>
                <td>
                    <h1 class="product-title">${item.product.title}</h1>
                    <span>${variant}</span>
                </td>
                <td><span class="product-price" id="I${item.id}" data-id="I${item.id}" data-price="${item.price}">${price}</span></td>
                <td>
                    <span>${item.quantity}</span> <button class="btn btn-link item-update-toggle" data-itemid="${item.id}" data-max="${stockQty}" data-qty="${item.quantity}" data-toggle="modal" data-target="#item-update-modal"><i class="fas fa-pencil-alt"></i></button>
                </td>
                <td><span class="product-price" id="A${item.id}" data-id="A${item.id}" data-price="${item.amount}">${amount}</span></td>
                <td>
                    <button class="btn btn-danger btn-sm item-delete-toggle" data-itemid="${item.id}" data-toggle="modal" data-target="#item-delete-modal"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`
            itemsHtml += itemHtml
        }
        $('#cart-items-web-body').html(itemsHtml)
    }else{
        let itemsHtml = ``
        for(let item of CART_ITEMS){
            let itemImages = item.product_images
            let productVariant = item.product_variant
            let imgUrl = `${WEB_URL}assets/img/default.png`
            if(itemImages.length > 0){
                imgUrl = itemImages[0].full_thumbnail_url    
            }
            let stockQty = productVariant ? productVariant.stock_qty : item.product.stock_qty
            let variant = productVariant ? productVariant.variants : ''
            let amount = CURRENCY == 'usd' ? formatDollar(item.amount) : formatRupiah(item.amount.toString(), true)

            let itemHtml = `<div class="row mb-2">
                <div class="col-4">
                    <img src="${WEB_URL}assets/img/placeholder-image.png" data-src="${imgUrl}" class="img-fluid cart-item-image cart-item-lazyload" alt="${item.product.title}${variant}">
                </div>
                <div class="col-6">
                    <h1 class="product-title">${item.product.title}</h1>
                    <span>${variant}</span>
                    <p>
                        <span>${item.quantity}</span> <button class="btn btn-link item-update-toggle" data-itemid="${item.id}" data-max="${stockQty}" data-qty="${item.quantity}" data-toggle="modal" data-target="#item-update-modal"><i class="fas fa-pencil-alt"></i></button>
                    </p>
                    <span class="product-price" id="A${item.id}" data-id="A${item.id}" data-price="${item.amount}">${amount}</span>
                </div>
                <div class="col-2">
                    <button class="btn btn-danger btn-sm item-delete-toggle" data-itemid="${item.id}" data-toggle="modal" data-target="#item-delete-modal"><i class="fas fa-trash"></i></button>
                </div>
            </div>`
            itemsHtml += itemHtml
        }
        $('#cart-items-mobile').html(itemsHtml)
    }
    let grandTotal = CURRENCY == 'usd' ? formatDollar(CART.total_amount) : formatRupiah(CART.total_amount.toString(), true)
    
    let grandTotalHtml = `<span class="product-price" id="G${CART.id}" data-id="G${CART.id}" data-price="${CART.total_amount.toString()}">${grandTotal}</a>`

    $('#grand-total').html(grandTotalHtml)
    $('.cart-item-lazyload').lazyload()
}
