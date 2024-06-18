let DEPOSIT_ID
$(document).ready(function(){
  //render datatable
  let deposit_table = $('#deposit-datatable').DataTable( {
    processing: true,
    serverSide: true,
    searching: true,
    ordering: false,
    ajax: {
      async: true,
      url: `${DEPOSIT_API_URL}by-user`,
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
        data: "amount",
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
        data: "id",
        render: function (data, type, row, meta) {
          let button = `<button class="btn btn-sm btn-light deposit-detail-toggle" data-id="${data}" data-bs-toggle="modal" data-bs-target="#deposit-detail-modal" title="detail">
            <i class="fas fa-search"></i>
          </button>`;

          return button
        }
      }      
    ]
  });

  $("body").delegate(".deposit-detail-toggle", "click", function() {
    DEPOSIT_ID = $(this).data('id')
    getDetailDeposit()
  })

  $('#deposit').change(function(){
    const val = $(this).val()
    const fee_percentage = CONFIG.deposit_percentage || 0
    const fee = parseFloat(val) * (fee_percentage/100)
    const total = parseFloat(val) + fee
    $('#deposit-subtotal').html(formatRupiah(val.toString(), true))
    $('#deposit-fee').html(formatRupiah(fee.toString(), true))
    $('#deposit-total').html(formatRupiah(total.toString(), true))
  })

  $('.suggest-deposit').click(function(){
    const nominal = $(this).data("nominal")
    $('#deposit').val(nominal).change()
  })

  $('#deposit-form').submit(function(e){
    e.preventDefault()
    startLoadingButton("#deposit-button")

    const request = {
      deposit_amount: $('#deposit').val()
    }

    $.ajax({
      async: true,
      url: `${DEPOSIT_API_URL}`,
      type: 'POST',
      data: JSON.stringify(request),
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
      },
      error: function(res) {
        const response = JSON.parse(res.responseText)
        let isRetry = retryRequest(response)
        if(isRetry) $.ajax(this)
        else endLoadingButton("#deposit-button", "Proses Deposit")

      },
      success: function(res) {
        const response = res.data
        if(response.statusCode != '00'){
          showError("Deposit", "Terjadi Kesalahan!")
          endLoadingButton("#deposit-button", "Proses Deposit")
        }else{
          window.location.href = response.paymentUrl
        }
      }
    });
  })
})

function getDetailDeposit(){  
  $('#deposit-detail-loading').show()

  $.ajax({
    async: true,
    url: `${DEPOSIT_API_URL}by-user-and-id/${DEPOSIT_ID}`,
    type: 'GET',
    beforeSend: function (xhr) {
      xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
    },
    error: function(res) {
      const response = JSON.parse(res.responseText)
      let isRetry = retryRequest(response)
      if(isRetry) $.ajax(this)
      else $('#deposit-detail-modal').modal('toggle')
    },
    success: function(res) {
      const response = res.data
      renderForm(response)
      $('#deposit-detail-loading').hide()
    }
  });
}

function renderForm(data){
  $form = $('#deposit-detail-form')
  $form.find( "input[name='deposit_number']" ).val(data.deposit_number)
  $form.find( "input[name='payment_method']" ).val(data.payment_method)
  $form.find( "input[name='amount']" ).val(formatRupiah(data.amount, true))
  $form.find( "input[name='processing_fee']" ).val(formatRupiah(data.processing_fee, true))
  $form.find( "input[name='total']" ).val(formatRupiah(data.total))
}