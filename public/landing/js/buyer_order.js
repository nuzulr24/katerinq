let ORDER_ID,
    STATUS = ''

$(document).ready(function(){
  //render datatable
  let order_table = $('#buyer-order-datatable').DataTable( {
      processing: true,
      serverSide: true,
      searching: true,
      ordering: false,
      ajax: {
        async: true,
        url: `${ORDER_API_URL}by-buyer`,
        type: "GET",
        dataType: "json",
        crossDomain: true,
        beforeSend: function (xhr) {
          xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        data: function ( d ) {
          let newObj = {}
          let start = d.start
          let size = d.length
          newObj.page_number = d.start > 0 ? (start/size) : 0;
          newObj.page_size = size
          newObj.search = d.search.value
          newObj.draw = d.draw
          if(STATUS) newObj.status = STATUS
          d = newObj
          console.log("D itu:", d)
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
          data: "seller_fullname",
        },
        {
          data: "website_url",
        },
        { 
          data: "total",
          render: function (data, type, row, meta) {
            if(row.promo_code){
              return formatRupiah((parseInt(data) - parseInt(row.promo_amount)).toString(), true)
            }else{
              return formatRupiah(data.toString(), true)
            }
          }
        },
        { 
          data: "created_at",
          render: function (data, type, row, meta) {
            return formatLocalDatetimeStr(data)
          }
        },
        { 
          data: "status",
          render: function (data, type, row, meta) {
            let color = "light",
                status = data
            if(data == 'REQUESTED'){
              color = 'secondary'
            }else if(data == 'ON WORKING'){
              color = 'success'
            }else if(data == 'CANCELLED' || data == 'REQUEST CANCEL' || data == 'REJECTED'){
              color = 'danger'
            }else if(data == 'SUBMITTED'){
              color = 'info'
            }else if(data == 'REVISION'){
              color = 'info'
            }else if(data == 'COMPLETED'){
              color = 'primary'
            }
            let badge = `<span class="badge badge-pill bg-${color}">${status}</span>`

            return badge
          }
        },
        {
          data: "id",
          render: function (data, type, row, meta) {
            let button = `<button class="btn btn-sm btn-light order-detail-toggle" data-id="${data}" data-bs-toggle="modal" data-bs-target="#order-detail-modal" title="detail">
              <i class="fas fa-search"></i>
            </button>`;

            if(row.status == 'SUBMITTED'){
              button += `<button class="btn btn-sm btn-primary order-result-toggle" data-id="${data}" data-url="${row.result_url}" data-message="${row.last_seller_message}" data-bs-toggle="modal" data-bs-target="#order-result-modal" title="Lihat Hasil Pekerjaan">
                <i class="fas fa-tv"></i>
              </button>`;
            }
            if(row.status == 'REVISION' || row.status == 'ON WORKING'){
              button += `<button class="btn btn-sm btn-danger order-request-cancel-toggle" data-id="${data}" data-bs-toggle="modal" data-bs-target="#order-request-cancel-modal" title="Minta Batalkan Pekerjaan">
                <i class="fas fa-times"></i>
              </button>`
            }

            return button
          }
        }
    ]
  });

  $("body").delegate(".order-detail-toggle", "click", function() {
    ORDER_ID = $(this).data('id')
    console.log("SELECT ORDER ID:", ORDER_ID)
    getDetailOrder()
  })

  $("body").delegate(".order-result-toggle", "click", function() {
    const url = $(this).data('url')
    const message = $(this).data('message')
    $('#url').val(url)
    $('#message').val(message)
    ORDER_ID = $(this).data('id')
  })

  $("body").delegate(".order-request-cancel-toggle", "click", function() {
    const id = $(this).data('id')
    if(id) ORDER_ID = id
  })

  $('#order-revision-form').submit(function(e){
    e.preventDefault()

    startLoadingButton('#order-revision-button')

    let $form = $('#order-revision-form'),
        request = {
          id: ORDER_ID,
          comment: $form.find("textarea[name='comment']").val(),
        }

    $.ajax({
        async: true,
        url: `${ORDER_API_URL}revision`,
        type: 'PUT',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#order-revision-button', `Submit`)
        },
        success: function(res) {
            showSuccess("Revisi Pekerjaan", res.message)
            endLoadingButton('#order-revision-button', `Submit`)
            $('#order-revision-modal').modal("toggle")
            $('#order-result-modal').modal("toggle")
            order_table.draw()
        }
    });
  })

  $('#order-complete-form').submit(function(e){
    e.preventDefault()

    const rating =  $('#rating').val()
    if(!rating){
      showError("Rating", "Anda harus memasukan rating!")
      return
    }

    startLoadingButton('#order-complete-button')

    let $form = $('#order-complete-form'),
        request = {
          id: ORDER_ID,
          comment: $form.find("textarea[name='comment']").val(),
          review: $form.find("textarea[name='review']").val(),
          rating: rating
        }

    console.log("REQUEST: ", request)

    $.ajax({
        async: true,
        url: `${ORDER_API_URL}complete`,
        type: 'PUT',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#order-complete-button', `Submit`)
        },
        success: function(res) {
            showSuccess("Terima Hasil Pekerjaan", res.message)
            endLoadingButton('#order-complete-button', `Submit`)
            $('#order-result-modal').modal("toggle")
            $('#order-complete-modal').modal("toggle")
            order_table.draw()
        }
    });
  })

  $('#order-request-cancel-form').submit(function(e){
    e.preventDefault()

    startLoadingButton('#order-request-cancel-button')

    let $form = $('#order-request-cancel-form'),
        request = {
          id: ORDER_ID,
          comment: $form.find("textarea[name='comment']").val(),
        }

    $.ajax({
        async: true,
        url: `${ORDER_API_URL}request-cancel`,
        type: 'PUT',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#order-request-cancel-button', `Submit`)
        },
        success: function(res) {
            showSuccess("Minta Pembatalan Pemesanan", res.message)
            endLoadingButton('#order-request-cancel-button', `Submit`)
            $('#order-result-modal').modal("toggle")
            $('#order-request-cancel-modal').modal("toggle")
            order_table.draw()
        }
    });
  })
})

function getDetailOrder(){  
  $('#order-detail-loading').show()

  $.ajax({
    async: true,
    url: `${ORDER_API_URL}by-buyer-detail/${ORDER_ID}`,
    type: 'GET',
    beforeSend: function (xhr) {
      xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
    },
    error: function(res) {
      const response = JSON.parse(res.responseText)
      let isRetry = retryRequest(response)
      if(isRetry) $.ajax(this)
      else $('#order-detail-modal').modal('toggle')
    },
    success: function(res) {
      const response = res.data
      renderForm(response)
      $('#order-detail-loading').hide()
    }
  });
}

function renderForm(data){
  const totalAfterPromo = data.promo_code ? parseInt(data.total)-parseInt(data.promo_amount) : parseInt(data.total)
  $form = $('#order-detail-form')
  $form.find( "input[name='website_url']" ).val(data.website_url)
  $form.find( "input[name='guestpost_price']" ).val(formatRupiah(data.guestpost_price, true))
  $form.find( "input[name='invoice_number']" ).val(data.invoice_number)
  $form.find( "textarea[name='special_requirement']" ).val(data.special_requirement)
  $form.find( "textarea[name='cancel_reason']" ).val(data.cancel_reason)
  $form.find( "input[name='result_url']" ).val(data.result_url)
  $form.find( "input[name='seller_fullname']" ).val(data.seller_fullname)
  $form.find( "input[name='total']" ).val(formatRupiah(totalAfterPromo.toString(), true))
  $form.find( "input[name='promo_amount']" ).val(data.promo_amount ? formatRupiah(data.promo_amount.toString(), true) : "-")
  $form.find( "input[name='promo_code']" ).val(data.promo_code ? data.promo_code : "-")

  //is content included
  if(parseInt(data.is_content_included)){
    const contentPrice = data.content_price ? formatRupiah(data.content_price.toString(), true) : '-'

    $form.find( "input[name='content_price']" ).val(contentPrice)
    $form.find( "input[name='anchor_text']" ).val(data.anchor_text)
    $form.find( "input[name='webpage_url']" ).val(data.webpage_url)
    $('#is_content_included').html("<b>IYA</b>")
    $('.content-included').show()
    $('.content-nonincluded').hide()
  }else{
    $('#is_content_included').html("<b>TIDAK</b>")
    $(`#summernote`).summernote("code", data.guest_post_content)
    $("#summernote").summernote("disable");
    $('.content-included').hide()
    $('.content-nonincluded').show()
  }

  //attachment
  if(data.attached_file){
    const full_url = `${API_URL}/${data.attached_file}`
    $('#order-attachment').html(`<a href="${full_url}" target="_blank"><i class="fas fa-file"></i> File</a>`)
  }else{
    $('#order-attachment').html("-")
  }

  if(data.status == 'CANCELLED'){
    $('#cancel_reason').show()
  }else{
    $('#cancel_reason').hide()
  }

  if(data.status == 'COMPLETED'){
    $('#result_url').show()
  }else{
    $('#result_url').hide()
  }

  //status
  let color = 'light'
  if(data.status == 'REQUESTED') color = 'secondary'
  if(data.status == 'ON WORKING') color = 'success'
  if(data.status == 'COMPLETED') color = 'primary'
  if(data.status == 'SUBMITTED') color = 'info'
  if(data.status == 'REVISION') color = 'warning'
  if(data.status == 'CANCELLED') color = 'danger'
  if(data.status == 'REJECTED') color = 'danger'
  if(data.status == 'FAILED') color = 'warning'
  let badge = `<span class="badge badge-pill bg-${color}">${data.status}</span>`
  $('#order-status').html(badge)
}
