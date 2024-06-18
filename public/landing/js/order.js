let ORDER_ALIAS = decodeURIComponent(window.location.pathname.split("/").pop()),
    CONTENT_PRICE = 0,
    GUESTPOST_PRICE = 0,
    FEE_ADMIN = 0,
    TOTAL_PRICE = 0,
    IS_CONTENT_INCLUDED = false,
    BALANCE = 0,
    PROMO_CODE = null,
    WEBSITE

$(document).ready(function(){
    if(!SESSION){ 
        window.location.href = `${WEB_URL}login?source=${window.location.href}`
    }

    $('.content-included').hide()
    $('#warning-balance').hide()

    getBuyerProfile()
    getWebsiteDetail()

    $('#is_content_included').change(function(){
        IS_CONTENT_INCLUDED = $(this).is(":checked")
        TOTAL_PRICE = IS_CONTENT_INCLUDED ? parseInt(CONTENT_PRICE) + parseInt(GUESTPOST_PRICE) : parseInt(GUESTPOST_PRICE)
        renderForm()
    })

    $('#promo-code-form').submit(function(e){
        e.preventDefault()
        startLoadingButton('#promo-code-button')

        let $form = $('#promo-code-form'),
            promo_code = $form.find("input[name='promo_code']").val()

        $.ajax({
            async: true,
            url: `${PROMO_CODE_API_URL}by-code/${promo_code}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
            },
            error: function(res) {
              const response = JSON.parse(res.responseText)
              endLoadingButton('#promo-code-button', 'Gunakan')
              $('#promo-code-response').html(`<div class="alert alert-danger" role="alert">
                Kode promo tidak ditemukan
              </div>`)
            },
            success: function(res) {
                const response = res.data
                PROMO_CODE = response
                $('#promo-code-response').html(`<div class="alert alert-success" role="alert">
                    Kode promo berhasil ditambahkan
                </div>`)
                endLoadingButton('#promo-code-button', 'Digunakan')
                $('#promo-code-button').prop("disabled", true)
                $('#website-discount').html(formatRupiah(response.nominal, true))
                $('#website-total_price').html(formatRupiah((TOTAL_PRICE - parseInt(response.nominal)).toString(), true))
            }
        });
    })

    $('#order-button').click(function(e){
        e.preventDefault()

        if($("#order-form").valid()){
            let $form = $('#order-form'),
            guest_post_content = $('#summernote').summernote('code'),
            anchor_text = $form.find("input[name='anchor_text']").val(),
            webpage_url = $form.find("input[name='webpage_url']").val(),
            special_requirement = $form.find("textarea[name='special_requirement']").val(),
            attachment = $('#attached_file').prop('files')[0];

            let request = {
                website_id: WEBSITE.id,
                is_content_included: IS_CONTENT_INCLUDED,
                special_requirement: special_requirement
            }

            if(PROMO_CODE){
                request.promo_code = PROMO_CODE.code
            }
        
            if(IS_CONTENT_INCLUDED){
                request = {
                    ...request,
                    anchor_text: anchor_text,
                    webpage_url: webpage_url
                }
            }else{
                let attached_file = uploadFile(attachment)
                request = {
                    ...request,
                    guest_post_content: guest_post_content,
                    attached_file: attached_file
                }
            }

            checkoutOrder(request)
        }
    })
})

function checkoutOrder(request){
    startLoadingButton('#order-button')

    $.ajax({
        async: true,
        url: `${ORDER_API_URL}`,
        type: 'POST',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          else showError("Pesanan", "Gagal melakukan pesanan")
          endLoadingButton('#order-button', 'Submit')
        },
        success: function(res) {
            const response = res.data
            showSuccess("Pesanan", "Berhasil melakukan pesanan", `${WEB_URL}account/buyer/order`)
        }
    });
}

function getWebsiteDetail(){
    let website_alias_and_code = unformatWebsiteAlias(ORDER_ALIAS)
    let arr = website_alias_and_code.split(".")
    let unique_code = arr.pop()
    let web_url = arr.join(".")
    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}by-url-and-code?url=${web_url}&code=${unique_code}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
          $("#promo-code-form :input").prop("disabled", true);
          $("#order-form :input").prop("disabled", true);
          $("#order-button").prop("disabled", true);
        },
        success: function(res) {
            renderWebsiteDetail(res.data)
            WEBSITE = res.data
        }
    });
}

function getBuyerProfile(){
    $.ajax({
        async: false,
        url: `${USER_API_URL}profile`,
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
          BALANCE = res.data.balance
        }
    });
}

function renderForm(){
    if(IS_CONTENT_INCLUDED){
        const totalAfterPromo = PROMO_CODE ? TOTAL_PRICE-parseInt(PROMO_CODE.nominal) : TOTAL_PRICE
        $('.content-included').show()
        $('.content-included-field').prop("required", true)
        $('.content-nonincluded').hide()
        $('.content-nonincluded-field').prop("required", false)
        $('#website-content_price').html(formatRupiah(CONTENT_PRICE, true))
        $('#website-total_price').html(formatRupiah(total.toString(), true))

        renderBalanceSufficient(totalAfterPromo, BALANCE)
    }else{
        const totalAfterPromo = PROMO_CODE ? parseInt(GUESTPOST_PRICE)-parseInt(PROMO_CODE.nominal) : parseInt(GUESTPOST_PRICE)
        $('.content-nonincluded').show()
        $('.content-nonincluded-field').prop("required", true)
        $('.content-included').hide()
        $('.content-included-field').prop("required", false)
        $('#website-content_price').html("-")
        $('#website-total_price').html(formatRupiah(total.toString(), true))

        renderBalanceSufficient(totalAfterPromo, BALANCE)
    }
}

function renderBalanceSufficient(totalPrice, balance){
    if(parseInt(totalPrice) > parseInt(balance)){
        $('#warning-balance').show()
        $('#order-button').prop("disabled", true)
    }else{
        $('#warning-balance').hide()
        $('#order-button').prop("disabled", false)
    }
}

function renderWebsiteDetail(website){
    CONTENT_PRICE = website.content_price
    GUESTPOST_PRICE = website.guest_post_price
    $('#website-url').html(`<a href="${website.website_url}" target="_blank">${website.website_url}</a>`)
    $('.website-price').html(formatRupiah(website.guest_post_price, true))
    $('#website-total_price').html(formatRupiah(website.guest_post_price, true))
    $('#website-word_limit').html(website.word_limit)
    $('#website-da').html(website.da)
    $('#website-pa').html(website.pa)
    
    const fee_percentage = CONFIG.platform_percentage || 0
    // const fee = totalAfterPromo * (fee_percentage/100)
    // const total = totalAfterPromo + fee
    // $('#fee_platform').html(fee)
    
    console.log(fee_percentage)

    if(!website.is_content_included && !website.content_price){
        $('#website-switch-content').hide()
        TOTAL_PRICE = GUESTPOST_PRICE
    }else{
        $('#website-switch-content').show()
        // TOTAL_PRICE = parseInt(CONTENT_PRICE) + parseInt(GUESTPOST_PRICE)
        TOTAL_PRICE = GUESTPOST_PRICE
    }
    renderBalanceSufficient(TOTAL_PRICE, BALANCE)
}

function uploadFile(file){
    let response = null
    let formData = new FormData();
    formData.append('file', file);

    $.ajax({
        async: false,
        url: `${ORDER_API_URL}upload-file`,
        type: 'POST',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        data: formData,
        processData: false, 
        contentType: false, 
        error: function(res) {
        },
        success: function(res) {
            response = res.data
        }
    });

    return response
}