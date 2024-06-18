let OVERSEAS_SHIPPING = false,
    COUNTRY = [],
    PROVINCE = [],
    CITY = [],
    SUBDISTRICT = [],
    CART_ITEMS = [],
    COURIER = [],
    ORIGIN = 455,
    TOTAL_WEIGHT = 0,
    TOTAL_AMOUNT = 0

$(document).ready(function(){
    stepper = new Stepper($('.bs-stepper')[0])
    getCartDetail()
    getCountry()
    getProvince()

    $('#is_overseas_hipping').change(function() {
        if(this.checked) {
            OVERSEAS_SHIPPING = true
            $('#country-option').css("display", "block")
            $('#province-option').css("display", "none")
            $('#city-option').css("display", "none")
            $('#subdistrict-option').css("display", "none")
            $('#city').prop("disabled", true);
            $('#subdistrict').prop("disabled", true);
            renderCountry()
        }else{
            OVERSEAS_SHIPPING = false
            $('#country-option').css("display", "none")
            $('#province-option').css("display", "block")
            $('#city-option').css("display", "block")
            $('#subdistrict-option').css("display", "block")
            renderProvince()
        }
    })
    //watch province option
    $('#province').change(function() {
        $('#city').prop("disabled", true);
        $('#subdistrict').prop("disabled", true);
        $('#city').prop('selectedIndex',0);
        $('#subdistrict').prop('selectedIndex',0);
        const selectedProvince = $('#province').find(":selected").val()
        console.log(selectedProvince)
        
        if (parseInt(selectedProvince) > 0) {
            getCity(selectedProvince)
        } else {
            $('#city').html(`<option value="0">--Pilih Kota--</option>`);
            $('#city').prop("disabled", true);
        }
    })
    //watch city option
    $('#city').change(function() {
        $('#subdistrict').prop("disabled", true);
        $('#subdistrict').prop('selectedIndex',0);
        const selectedCity = $('#city').find(":selected").val()
        console.log(selectedCity)

        if (parseInt(selectedCity) > 0) {
            getSubdistrict(selectedCity)
        } else {
            $('#subdistrict').html(`<option value="0">--Pilih Kecamatan--</option>`);
            $('#subdistrict').prop("disabled", true);
        }
    })
    //watch county option
    $('#subdistrict').change(function() {
        const selectedSubdistrict = $('#subdistrict').find(":selected").val()
        console.log(selectedSubdistrict)
    })

    $('#address-button').click(function(){
        submitAddressForm()
    })
    $('#ship-button').click(function(){
        submitShippingForm()
    })
    $('.address-update-button').click(function(){
        stepper.to(1)
        $('#address-button').css("display", "block")
        $('#ship-button').css("display", "none")
        $('#pay-button').css("display", "none")
    })
    $('.shipping-update-button').click(function(){
        stepper.to(2)
        $('#address-button').css("display", "none")
        $('#ship-button').css("display", "block")
        $('#pay-button').css("display", "none")
    })
    $('#pay-button').click(function(){
        checkoutCart()
    })
})

function submitAddressForm(){
    let $form = $('#address-form'),
        receiverName = $form.find( "input[name='receiver_name']" ).val(),
        fullAddress = $form.find( "textarea[name='full_address']" ).val(),
        postalCode = $form.find( "input[name='postal_code']" ).val(),
        phone = $form.find( "input[name='phone']" ).val(),
        country = $('#country-option').find(":selected").val(),
        province = $('#province-option').find(":selected").val(),
        city = $('#city-option').find(":selected").val(),
        subdistrict = $('#subdistrict-option').find(":selected").val()

    let fields = [receiverName, fullAddress, postalCode, phone]
    if(OVERSEAS_SHIPPING) fields.push(country)
    else fields.push(province, city, subdistrict)
    if(checkForm(fields)){
        stepper.next()
        renderAddressBox()
        getCourier()
        $('#ship-button').css("display", "block")
        $('#address-button').css("display", "none")
    }else{
        showError("Alamat", "Form Alamat Harus diisi")
    }
}

function submitShippingForm(){
    let $form = $('#shipping-form'),
        shippingService = $form.find('input[name="shipping_method"]:checked').val();

    console.log("SHIPPING SERVICE: ", shippingService)

    let fields = [shippingService]
    if(checkForm(fields)){
        stepper.next()
        renderShippingBox()
        $('#ship-button').css("display", "none")
        $('#pay-button').css("display", "block")
    }else{
        showError("Metode Pengiriman", "Anda harus memilih metode pengiriman")
    }
}

function checkoutCart(){
    startLoadingButton('#pay-button')

    let $addressForm = $('#address-form'),
        $shippingForm = $('#shipping-form'),
        selectedCourier = $shippingForm.find('input[name="shipping_method"]:checked').val();
        arr = selectedCourier.split("|"),
        service = arr[0],
        serviceType = arr[1],
        etd = arr[2],
        cost = arr[3],
        grandTotal = parseInt(cost) + parseInt(TOTAL_AMOUNT),
        request = {
            receiver_name: $addressForm.find("input[name='receiver_name']").val(),
            service: service,
            service_type: serviceType,
            service_price: cost,
            full_address: $addressForm.find("textarea[name='full_address']").val(),
            postal_code: $addressForm.find("input[name='postal_code']").val(),
            phone: $addressForm.find("input[name='phone']").val()
        }
    
    if(OVERSEAS_SHIPPING){
        request.country = $("#country").find(":selected").text()
        request.country_id = $("#country").find(":selected").val()
    }else{
        request.province = $("#province").find(":selected").text()
        request.province_id = $("#province").find(":selected").val()
        request.city = $("#city").find(":selected").text()
        request.city_id = $("#city").find(":selected").val()
        request.subdistrict = $("#subdistrict").find(":selected").text()
        request.subdistrict_id = $("#subdistrict").find(":selected").val()
    }

    $.ajax({
        async: true,
        url: `${CART_API_URL}checkout`,
        type: 'POST',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#pay-button', 'Bayar')
        },
        success: function(res) {
            let data = res.data
            endLoadingButton('#pay-button', 'Bayar')
            openMidtrans(data)
        }
    });
}

function checkForm(fields){
    let isValid = true
    for(let field of fields){
        if(!field){
            isValid = false
            break
        }
    }

    return isValid
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
                if(CART_ITEMS.length > 0){
                    TOTAL_AMOUNT = CART.total_amount
                    renderCartItems()
                    getTotalWeight()
                }else{
                    window.location.href = `${WEB_URL}carts`
                }
            }else{
                window.location.href = `${WEB_URL}carts`
            }
        }
    });
}

function getCountry(){
    $.ajax({
        async: true,
        url: `${VENDOR_API_URL}international-destination`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)          
        },
        success: function(res) {
            COUNTRY = res.data.rajaongkir.results
        }
    });
}

function getProvince(){
    $.ajax({
        async: true,
        url: `${VENDOR_API_URL}province`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)          
        },
        success: function(res) {
            PROVINCE = res.data.rajaongkir.results
            renderProvince()
        }
    });
}

function getCity(province) {
    $.ajax({
      async: true,
      url: `${VENDOR_API_URL}city?province_id=${province}`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
        let isRetry = retryRequest(response, window.location.href)
        if(isRetry) $.ajax(this) 
      },
      success: function(res) {
        CITY = res.data.rajaongkir.results
        renderCity()
      }
    })
}
  
function getSubdistrict(city) {
    $.ajax({
      async: true,
      url: `${VENDOR_API_URL}subdistrict?city_id=${city}`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
        let isRetry = retryRequest(response, window.location.href)
        if(isRetry) $.ajax(this) 
      },
      success: function(res) {
        SUBDISTRICT = res.data.rajaongkir.results
        renderSubdistrict()
      }
    })
}
  
function getCourier() {
    let origin = ORIGIN,
        destination = OVERSEAS_SHIPPING ? $('#country').find(":selected").val() : $('#subdistrict').find(":selected").val(),
        weight = TOTAL_WEIGHT
    
    if(OVERSEAS_SHIPPING){
        $.ajax({
            async: true,
            url: `${VENDOR_API_URL}international-shipping-cost?origin=${origin}&destination=${destination}&weight=${weight}&courier=pos`,
            type: 'GET',
            error: function(res) {
              const response = JSON.parse(res.responseText)
              let isRetry = retryRequest(response, window.location.href)
              if(isRetry) $.ajax(this) 
            },
            success: function(res) {
              COURIER = res.data.rajaongkir.results
              renderCourier()
            }
        })
    }else{
        $.ajax({
            async: true,
            url: `${VENDOR_API_URL}shipping-cost?origin=${origin}&origin_type=subdistrict&destination=${destination}&destination_type=subdistrict&weight=${weight}`,
            type: 'GET',
            error: function(res) {
              const response = JSON.parse(res.responseText)
              let isRetry = retryRequest(response, window.location.href)
              if(isRetry) $.ajax(this) 
            },
            success: function(res) {
              COURIER = res.data.rajaongkir.results
              renderCourier()
            }
        })
    }

    
}

function renderCartItems(){
    console.log("RENDER ITEMS")
    let itemsHtml = `<h2 style="font-size:20px;">Keranjang</h2>`
    for(let item of CART_ITEMS){
        let itemImages = item.product_images
        let productVariant = item.product_variant
        let imgUrl = `${WEB_URL}assets/img/default.png`
        if(itemImages.length > 0){
            imgUrl = itemImages[0].full_thumbnail_url    
        }
        let variant = productVariant ? productVariant.variants : ''
        let price = CURRENCY == 'usd' ? formatDollar(item.price) : formatRupiah(item.price, true)

        let itemHtml = `<div class="row mb-2">
            <div class="col-4">
                <img src="${WEB_URL}assets/img/placeholder-image.png" data-src="${imgUrl}" class="img-fluid cart-item-image item-lazyload" alt="${item.product.title} ${variant}">
            </div>
            <div class="col-8">
                <h1 class="product-title">${item.product.title}</h1>
                <span>${variant}</span>
                <p class="product-price mb-0" id="I${item.id}" data-id="I${item.id}" data-price="${item.price}">${price}</p>
                <span>Jumlah: ${item.quantity}</span>
            </div>
        </div>`
        itemsHtml += itemHtml
    }
    console.log(itemsHtml)
    let totalAmount = CURRENCY == 'usd' ? formatDollar(TOTAL_AMOUNT) : formatRupiah(TOTAL_AMOUNT, true)
    let totalAmountHtml = `<span class="product-price" id="subtotal" data-id="subtotal" data-price="${TOTAL_AMOUNT}">${totalAmount}</span>`
    $('.carts-item').html(itemsHtml)
    $('#total-amount').html(totalAmountHtml)
    $('.item-lazyload').lazyload()
}

function renderCountry(){
    let listCountryHtml = `<option value="">--Pilih Negara--</option>`
    for (const item of COUNTRY) {
        const countryHtml = `<option value="${item.country_id}">${item.country_name}</option>`
        listCountryHtml += countryHtml
    }
    $('#country').html(listCountryHtml)
    $('#country').prop("disabled", false);
}

function renderProvince(){
    let listProvinceHtml = `<option value="">--Pilih Provinsi--</option>`
    for (const item of PROVINCE) {
        const provinceHtml = `<option value="${item.province_id}">${item.province}</option>`
        listProvinceHtml += provinceHtml
    }
    $('#province').html(listProvinceHtml)
    $('#province').prop("disabled", false);
}

function renderCity(){
    let listCityHtml = `<option value="">--Pilih Kota--</option>`
    for (const item of CITY) {
        const cityHtml = `<option value="${item.city_id}">${item.type} ${item.city_name}</option>`
        listCityHtml += cityHtml
    }
    $('#city').html(listCityHtml)
    $('#city').prop("disabled", false);
}

function renderSubdistrict(){
    let listSubdistrictHtml = `<option value="">--Pilih Kecamatan--</option>`
    for (const item of SUBDISTRICT) {
      const subdistrictHtml = `<option value="${item.subdistrict_id}">${item.subdistrict_name}</option>`
      listSubdistrictHtml += subdistrictHtml
    }
    $('#subdistrict').html(listSubdistrictHtml)
    $('#subdistrict').prop("disabled", false);
}

function renderCourier(){
    let listCourierHtml = ''
    let index = 0
    for(const item of COURIER){
        const courierCode = item.code
        const courierOption = item.costs
        if(courierOption.length > 0){
            for(const courier of courierOption){
                const cost = Array.isArray(courier.cost) ? courier.cost[0].value.toString() : courier.cost.toString()
                let etd = Array.isArray(courier.cost) ? courier.cost[0].etd : courier.etd
                if(!etd.toLowerCase().includes("hari")) etd = `${etd} Hari`
                let costCurr = CURRENCY == 'usd' ? formatDollar(cost) : formatRupiah(cost, true)

                const courierHtml = `<div class="row mb-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-2 d-flex align-items-center">
                                        <input type="radio" name="shipping_method" value="${courierCode}|${courier.service}|${etd}|${cost}" required>
                                    </div>
                                    <div class="col-6">
                                        <span id="shipping-service">${courierCode.toUpperCase()}-${courier.service}</span><br>
                                        <span id="shipping-estimation">Estimasi pengiriman ${etd}</span>
                                    </div>
                                    <div class="col-4 d-flex align-items-center">
                                        <span id="${index}${courier.service}" class="product-price" data-id="${index}${courier.service}" data-price="${cost}">${costCurr}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
                listCourierHtml += courierHtml
            }
        }
        index++
    }
    $('#shipping-form').html(listCourierHtml)
}

function renderAddressBox(){
    let $form = $('#address-form'),
        receiver = $form.find("input[name='receiver_name']").val(),
        fullAddress = $form.find("textarea[name='full_address']").val(),
        country = $("#country").find(":selected").text(),
        province = $("#province").find(":selected").text(),
        city = $("#city").find(":selected").text(),
        subdistrict = $("#subdistrict").find(":selected").text(),
        postalCode = $form.find("input[name='postal_code']").val(),
        phone = $form.find("input[name='phone']").val()

    $('.receiver-name').html(receiver)
    $('.full-address').html(fullAddress)
    $('.address').html(OVERSEAS_SHIPPING ? country : `${province}, ${city}, ${subdistrict}`)
    $('.postal-code').html(postalCode)
    $('.phone').html(phone)
}

function renderShippingBox(){
    let $form = $('#shipping-form'),
        selectedCourier = $form.find('input[name="shipping_method"]:checked').val();
        arr = selectedCourier.split("|"),
        service = arr[0],
        serviceType = arr[1],
        etd = arr[2],
        cost = arr[3],
        grandTotal = parseInt(cost) + parseInt(TOTAL_AMOUNT)
    
    console.log("COURIER: ", arr)
    let shippingCost = CURRENCY == 'usd' ? formatDollar(cost.toString()) : formatRupiah(cost.toString(), true)

    let shippingBoxHtml = `<span id="shipping-service">${service.toUpperCase()} ${serviceType}</span>
    <span id="shipping-estimation">Estimasi pengiriman ${etd}</span> - <span id="shipping-price" class="product-price" data-id="shipping-price" data-price="${cost}">${shippingCost}</span>`

    let costCurr = CURRENCY == 'usd' ? formatDollar(cost.toString()) : formatRupiah(cost.toString(), true)
    let costHtml = `<span class="product-price" id="cost" data-id="cost" data-price="${cost}">${costCurr}</span>`

    let grandTotalCurr = CURRENCY == 'usd' ? formatDollar(grandTotal.toString()) : formatRupiah(grandTotal.toString(), true)
    let grandTotalHtml = `<span class="product-price" id="grand-total-curr" data-id="grand-total-curr" data-price="${grandTotal}">${grandTotalCurr}</span>`

    $('.shipping-box').html(shippingBoxHtml)
    $('.shipping-price').html(costHtml)
    $('#grand-total').html(grandTotalHtml)
}

function getTotalWeight(){
    for(const item of CART_ITEMS){
        let product = item.product
        let weightAmount = parseInt(item.quantity) * parseInt(product.weight)
        TOTAL_WEIGHT += weightAmount
    }
    console.log("TOTAL WEIGHT: ", TOTAL_WEIGHT)
}

function openMidtrans(data){
    snap.pay(data.midtrans_token)
}