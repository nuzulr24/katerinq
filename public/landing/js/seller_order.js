let ORDER_ID,
    STATUS = ''

$(document).ready(function(){
  //render datatable
  let order_table = $('#seller-order-datatable').DataTable( {
      processing: true,
      serverSide: true,
      searching: true,
      ordering: false,
      ajax: {
        async: true,
        url: `${ORDER_API_URL}by-seller`,
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
            data: "buyer_fullname",
          },
          {
            data: "website_url",
          },
          { 
            data: "total",
            render: function (data, type, row, meta) {
              return formatRupiah(data.toString(), true)
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

              if(row.status == 'ON WORKING' || row.status == 'REVISION'){
                button += `<button class="btn btn-sm btn-primary order-submit-toggle" data-id="${data}" data-bs-toggle="modal" data-bs-target="#order-submit-modal" title="Submit Pekerjaan Anda">
                  <i class="fas fa-edit"></i>
                </button>`;
              }else if(row.status == 'REQUESTED'){
                button += `<button class="btn btn-sm btn-primary order-accept-toggle" data-id="${data}" data-bs-toggle="modal" data-bs-target="#order-accept-modal" title="Terima Pekerjaan">
                  <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-sm btn-danger order-reject-toggle" data-id="${data}" data-bs-toggle="modal" data-bs-target="#order-reject-modal" title="Tolak Pekerjaan">
                  <i class="fas fa-times"></i>
                </button>`;
              }

              return button
            }
          }
          
      ]
  });

  $('#order-status').change(function (){
    STATUS = $(this).val()
    order_table.draw()
  })

  //button action click
  $("body").delegate(".order-detail-toggle", "click", function(e) {
    ORDER_ID = $(this).data('id')
    getOrderDetail()
  })

  $("body").delegate(".order-submit-toggle", "click", function(e) {
    ORDER_ID = $(this).data('id')
  })

  $("body").delegate(".order-reject-toggle", "click", function(e) {
    ORDER_ID = $(this).data('id')
  })

  $("body").delegate(".order-accept-toggle", "click", function(e) {
    ORDER_ID = $(this).data('id')
  })

  $('#order-submit-form').submit(function(e){
    e.preventDefault()

    startLoadingButton('#order-submit-button')

    let $form = $('#order-submit-form'),
        request = {
          id: ORDER_ID,
          url: $form.find("input[name='url']").val(),
          comment: $form.find("textarea[name='comment']").val(),
        }

    $.ajax({
        async: true,
        url: `${ORDER_API_URL}delivery`,
        type: 'PUT',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#order-submit-button', `Submit`)
        },
        success: function(res) {
            showSuccess("Submit Pekerjaan", res.message)
            endLoadingButton('#order-submit-button', `Submit`)
            $('#order-submit-modal').modal("toggle")
            order_table.draw()
        }
    });
  })

  $('#order-reject-form').submit(function(e){
    e.preventDefault()

    startLoadingButton('#order-reject-button')

    let $form = $('#order-reject-form'),
        request = {
          id: ORDER_ID,
          reject_reason: $form.find("textarea[name='reject_reason']").val(),
        }

    $.ajax({
        async: true,
        url: `${ORDER_API_URL}reject`,
        type: 'PUT',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#order-reject-button', `Submit`)
        },
        success: function(res) {
            showSuccess("Tolak Pekerjaan", res.message)
            endLoadingButton('#order-reject-button', `Submit`)
            $('#order-reject-modal').modal("toggle")
            order_table.draw()
        }
    });
  })

  $('#order-accept-button').click(function(){
    startLoadingButton('#order-accept-button')

    let request = {
          id: ORDER_ID
        }

    $.ajax({
        async: true,
        url: `${ORDER_API_URL}accept`,
        type: 'PUT',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#order-accept-button', `Submit`)
        },
        success: function(res) {
            showSuccess("Terima Pekerjaan", res.message)
            endLoadingButton('#order-accept-button', `Submit`)
            $('#order-accept-modal').modal("toggle")
            order_table.draw()
        }
    });
  })
})

function renderForm(data){
  $form = $('#order-detail-form')
  $form.find( "input[name='website_url']" ).val(data.website_url)
  $form.find( "input[name='guestpost_price']" ).val(formatRupiah(data.guestpost_price, true))
  $form.find( "input[name='invoice_number']" ).val(data.invoice_number)
  $form.find( "textarea[name='special_requirement']" ).val(data.special_requirement)
  $form.find( "textarea[name='cancel_reason']" ).val(data.cancel_reason)
  $form.find( "input[name='result_url']" ).val(data.result_url)
  $form.find( "input[name='buyer_fullname']" ).val(data.buyer_fullname)
  $form.find( "input[name='total']" ).val(formatRupiah(data.total, true))
  $form.find( "input[name='platform_fee']" ).val(formatRupiah(data.platform_fee, true))

  //is content included
  if(parseInt(data.is_content_included)){
    const contentPrice = data.content_price ? formatRupiah(data.content_price.toString(), true) : '-'

    $form.find( "input[name='content_price']" ).val(contentPrice)
    $form.find( "input[name='anchor_text']" ).val(data.anchor_text)
    $form.find( "input[name='webpage_url']" ).val(data.webpage_url)
    $('#is_order_content_included').html("<b>IYA</b>")
    $('.content-included').show()
    $('.content-nonincluded').hide()
  }else{
    $('#is_order_content_included').html("<b>TIDAK</b>")
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
  if(data.status == 'SUBMITTED') color = 'info'
  if(data.status == 'COMPLETED') color = 'primary'
  if(data.status == 'REVISION') color = 'warning'
  if(data.status == 'CANCELLED') color = 'danger'
  if(data.status == 'FAILED') color = 'warning'
  let badge = `<span class="badge badge-pill bg-${color}">${data.status}</span>`
  $('#order-status').html(badge)
}


function getOrderDetail(){
  $.ajax({
      async: true,
      url: `${ORDER_API_URL}by-seller-detail/${ORDER_ID}`,
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
        console.log("GET DETAIL: ", res)
        const response = res.data
        renderForm(response)
      }
  });
}