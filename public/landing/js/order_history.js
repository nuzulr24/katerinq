let PAGE_NUMBER = 1,
    ORDER_HISTORY = []

$(document).ready(function(){
    $('#orderhistory-tab').addClass("active");

    getOrderHistory()

    $("body").delegate(".order-pagination-item", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()
    
        let page = $(this).data('page')
        PAGE_NUMBER = page
        console.log('page:',PAGE_NUMBER)
        getOrderHistory()
        window.scrollTo(0, 0);
    });

    $("body").delegate(".order-pagination-previous", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()

        PAGE_NUMBER -= 1
        console.log('page:',PAGE_NUMBER)
        getOrderHistory()
        window.scrollTo(0, 0);
    })

    $("body").delegate(".order-pagination-next", "click", function(e){
        e.preventDefault()
        e.stopImmediatePropagation()

        PAGE_NUMBER += 1
        console.log('page:',PAGE_NUMBER)
        getOrderHistory()
        window.scrollTo(0, 0);
    })

    $("body").delegate(".detail-order-button", "click", function(){
        let id = $(this).data("id")
        $.ajax({
            async: true,
            url: `${ORDER_API_URL}by-id/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            error: function(res) {
                const response = JSON.parse(res.responseText)
                console.log(`RESPONSE ORDER:`,response)
            },
            success: function(res) {
              const response = res.data
              console.log(`RESPONSE ORDER:`,response)
              renderOrderDetail(response)
            }
        })
    })

    $("body").delegate(".track-button", "click", function(){
        let courier = $(this).data("courier")
        let awb = $(this).data("airway")
        trackSalesOrder(courier, awb)
    })
})

function getOrderHistory(){
    $.ajax({
        async: true,
        url: `${ORDER_API_URL}?page_number=${PAGE_NUMBER-1}&page_size=5&draw=1`,
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
            console.log(res.data)
            if(res.data.length > 0){
                $('#order-notfound').css("display", "none")
                let totalPage = Math.ceil(res.recordsTotal/5)
                ORDER_HISTORY = res.data
                renderOrderHistory()
                renderOrderHistoryPagination(totalPage)
            }else{
                $('#order-history').css("display", "none")
                $('#order-notfound').css("display", "block")
            }
        }
    });
}

function trackSalesOrder(courier, awb){
    $('#timeline').html(`<div class="d-flex justify-content-center">
        <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
        </div>
    </div>`)
    $.ajax({
        async: true,
        url: `${VENDOR_API_URL}airwaybill-track?waybill=${awb}&courier=${courier}`,
        type: 'GET',
        beforeSend: function (xhr) {
          xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response)
          if(isRetry) $.ajax(this)
        },
        success: function(res) {
          // console.log("TRACK")
          let status = res.data.rajaongkir.status.code
          if(status == 200){
            let timeline = res.data.rajaongkir.result.manifest
            renderTimeline(timeline)
          }else{
            $('#timeline').html(`<p align="center" class="font-weight-bold">Invalid Air Waybill Number!</p>`)
          }
  
        //   $('#loading-page').hide()
        //   $('#timeline-container').css("display", "block")
        }
    });
}

function midtransPay(midtrans_token){
    console.log("PAY..")
    let res = snap.pay(midtrans_token)
    console.log(res)
}

function renderOrderHistory(){
    let itemsHtml = ``
    for(let order of ORDER_HISTORY){
        let button = `<button class="btn btn-light detail-order-button" data-toggle="modal" data-target="#detail_order_modal" data-id="${order.id}"><i class="fas fa-search"></i> Detail</button>&nbsp;`
        let grandTotal = CURRENCY == 'usd' ? formatDollar(order.grand_total) : formatRupiah(order.grand_total, true)

        if(order.status == 'PENDING'){
            button += `<button class="btn btn-primary" onclick="midtransPay('${order.midtrans_token}')"><i class="fas fa-wallet"></i> Bayar</button>&nbsp;`
        }else if(order.status == 'ON DELIVERY' || order.status == 'COMPLETED'){
            button += `<button class="btn btn-success track-button" data-toggle="modal" data-target="#track_airwaybill_modal" data-courier="${order.service}" data-airway="${order.airway_bill}"><i class="fas fa-map-marker-alt"></i> Lacak</button>`
        }

        let itemHtml = `<li class="list-group-item mb-2">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-12 col-12">
                    <span style="font-size:20px; font-weight:bold;">${order.invoice_number}</span><br>
                    <span class="font-weight-bold product-price" id="A${order.id}" data-id="A${order.id}" data-price="${order.grand_total}">${grandTotal}</span><br>
                    <span>${formatDatetime(order.created_at)}</span>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-12 col-12 text-right">
                    <p><span class="badge badge-${getBadge(order.status)}">${order.status}</span></p>
                    ${button}
                </div>
            </div>
        </li>`
        itemsHtml += itemHtml
    }

    $('#order-history-section').html(itemsHtml)
}

function getBadge(status){
    let color = 'light'
    if(status == 'CANCELLED') color = 'danger'
    else if(status == 'PENDING') color = 'secondary'
    else if(status == 'PAID') color = 'success'
    else if(status == 'ON DELIVERY') color = 'info'
    else if(status == 'COMPLETED') color = 'primary'
    return color
}

function renderOrderHistoryPagination(totalPage){
    if(totalPage > 1){
        let fullPagesHtml = ``
        let previousDisable = PAGE_NUMBER == 1 ? 'disabled' : ''
        let nextDisable = PAGE_NUMBER == totalPage ? 'disabled' : ''
        let isPageLimit = totalPage > 5 ? true : false;

        let previousHtml = `<li class="page-item ${previousDisable}">
            <a class="page-link order-pagination-previous" href="#">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>`
        let nextHtml = `<li class="page-item ${nextDisable}">
            <a class="page-link order-pagination-next" href="#">
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
                        <a class="page-link order-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                    </li>`
                }else if(i == PAGE_NUMBER-1 || i == PAGE_NUMBER+1){
                    pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                        <a class="page-link order-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                    </li>`
                }else if(i == PAGE_NUMBER-2 || i == PAGE_NUMBER+2){
                    pageHtml = `<li class="page-item disabled">
                        <span class="page-link order-pagination-item">...</span>
                    </li>`
                }
            }else{
                pageHtml = `<li class="page-item ${pageActive} ${pageDisabled}">
                    <a class="page-link order-pagination-item" data-page="${i}" ${pageDisabled}>${i}</a>
                </li>`
            }
            pagesHtml += pageHtml
        }
        fullPagesHtml = `${previousHtml}${pagesHtml}${nextHtml}`
        $('#order-pagination').html(fullPagesHtml)
    }
    
}

function renderOrderDetail(order){
    let orderItems = order.items
    let awb = order.airway_bill ? order.airway_bill : '-'
    let grandTotal = parseInt(order.grand_total) + parseInt(order.service_price)
    let grandTotalCurr = CURRENCY == 'usd' ? formatDollar(grandTotal) : formatRupiah(grandTotal.toString(), true)
    let servicePrice = CURRENCY == 'usd' ? formatDollar(order.service_price) : formatRupiah(order.service_price, true)
    let subTotal = CURRENCY == 'usd' ? formatDollar(order.grand_total) : formatRupiah(order.grand_total, true)

    let itemsHtml = ``
    for(const item of orderItems){
      let product = item.product
      let variant = item.product_variant
      let image = item.product_images.length > 0 ? item.product_images[0] : null
      let imgUrl = image ? image.full_thumbnail_url : `${WEB_URL}assets/img/default.png`
      let price = CURRENCY == 'usd' ? formatDollar(item.price) : formatRupiah(item.price.toString(), true)

      let itemHtml = `<div class="row" style="font-size:13px">
          <div class="col-5 d-flex justify-content-center">
            <img src="${imgUrl}" class="cart-item-image " style="height:150px;">
          </div>
          <div class="col-7">
            <p id="cart-item-title" style="font-weight:bold">${product.title}</p>
            <p id="I${item.id}" style="font-weight:bold" class="product-price" data-id="I${item.id}" data-price="${item.price}">${price}</p>
            <p id="cart-item-variant">
              Variant: ${parseInt(variant) ? variant.variants : '-'} <br> 
              Jumlah: ${item.quantity ? item.quantity : '-'} <br>
              Berat: ${product.weight ? `${product.weight} gram` : '-' } &nbsp;
            </p>
          </div>
        </div>`
        itemsHtml += itemHtml
    }

    if(order.country){
        let addressHtml = `<span id="order-address">${order.full_address}</span><br>
         <span id="order-country">${order.country}</span> <span id="order-postal-code">(${order.postal_code})</span><br>
        <span id="order-receiver">${order.receiver_name}</span>, <span id="order-phone">${order.phone}</span>`
        $('#address-detail').html(addressHtml)
    }else{
        let addressHtml = `<span id="order-address">${order.full_address}</span><br>
        <span id="order-county">Kec. ${order.subdistrict}</span>, <span id="order-city">${order.city}</span>, <span id="order-province">${order.province}</span> <span id="order-postal-code">(${order.postal_code})</span><br>
        <span id="order-receiver">${order.receiver_name}</span>, <span id="order-phone">${order.phone}</span>`
        $('#address-detail').html(addressHtml)
    }
    
    $('#order-status').html(order.status)
    $('#order-number').html(order.invoice_number)
    $('#order-airwaybill-number').html(awb)
    $('#order-courier').html(`${order.service.toUpperCase()} - ${order.service_type}`)
    $('#order-sub-total').html(subTotal)
    $('#order-sub-total').data("price", order.grand_total)
    $('#order-courier-price').html(servicePrice)
    $('#order-courier-price').data("price", order.service_price)
    $('#order-grand-total').html(grandTotalCurr)
    $('#order-grand-total').data("price", grandTotal)
    $('#order-items').html(itemsHtml)
}
  
function renderTimeline(data){
    let timeline = data.reduce(function (r, a) {
        r[a.manifest_date] = r[a.manifest_date] || [];
        r[a.manifest_date].push(a);
        return r;
    }, Object.create(null));
    console.log("TIMELINE: ", timeline)
  
    let timelineHtml = ``
    for(const key in timeline){
      let timelineItems = timeline[key]
      let timelineItemsHtml = `<div class="time-label">
        <span class="bg-yellow">${formatDate(key)}</span>
      </div>`
  
      for(const item of timelineItems){
        timelineItemHtml = `<div>
          <i class="fas fa-truck bg-info"></i>
          <div class="timeline-item">
            <span class="time"><i class="fas fa-clock"></i> ${item.manifest_time}</span>
            <h3 class="timeline-header"><span class="font-weight-bold">${item.manifest_code}</span> - ${item.city_name}</h3>
            <div class="timeline-body">
              ${item.manifest_description}
            </div>
          </div>
        </div>`
  
        timelineItemsHtml += timelineItemHtml
      }
  
      timelineHtml += timelineItemsHtml
    }
    timelineHtml += `<div>
      <i class="fas fa-clock bg-gray"></i>
    </div>`
  
    $('#timeline').html(timelineHtml)
}
