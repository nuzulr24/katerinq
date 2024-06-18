let WITHDRAW_ID,
    STATUS = ''

$(document).ready(function(){
  //render datatable
  let withdraw_table = $('#withdraw-datatable').DataTable( {
      processing: true,
      serverSide: true,
      searching: true,
      ordering: false,
      ajax: {
        async: true,
        url: `${WITHDRAW_API_URL}by-seller`,
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
          data: "withdraw_number",
        },
        { 
          data: "bank_code",
        },
        { 
          data: "bank_account_number",
        },
        { 
          data: "bank_account_name",
        },
        { 
          data: "amount",
          render: function (data, type, row, meta) {
            return formatRupiah(data, true)
          }
        },
        // { 
        //   data: "reject_reason",
        //   render: function (data, type, row, meta) {
        //     return data ? sortText(data) : "-"
        //   }
        // },
        // { 
        //   data: "payment_proof_url",
        //   render: function (data, type, row, meta) {
        //     return data ? `<a href="${data}" target="_blank">${data}</a>` : "-"
        //   }
        // },
        { 
          data: "status",
          render: function (data, type, row, meta) {
            let color = 'light'
            if(data == 'REQUESTED') color = 'secondary'
            else if(data == 'REJECTED') color = 'danger'
            else if(data == 'PAID') color = 'success'
            let badge = `<span class="badge badge-pill bg-${color}">${data}</span>`

            return badge
          }
        },
        {
          data: "created_at",
        },
        {
          data: "id",
          className: "dt-body-center",
          render: function (data, type, row, meta) {
              let button = `<button class="btn btn-secondary withdraw-detail-toggle" data-id="${data}" data-bs-toggle="modal" data-bs-target="#withdraw-detail-modal" title="detail">
                <i class='fas fa-search'></i>
              </button>`;

              return button
          }
        },
      ]
  });

  $('#withdraw-status').change(function (){
    STATUS = $(this).val()
    order_table.draw()
  })

  $("body").delegate(".withdraw-detail-toggle", "click", function(e) {
    WITHDRAW_ID = $(this).data('id')
    getWithdrawDetail()
  })

  $('#withdraw-form').submit(function(e){
    e.preventDefault()

    startLoadingButton('#withdraw-button')

    let $form = $('#withdraw-form'),
        request = {
          bank_code: $form.find("input[name='bank_code']").val(),
          bank_account_number: $form.find("input[name='bank_account_number']").val(),
          bank_account_name: $form.find("input[name='bank_account_name']").val(),
          amount: $form.find("input[name='amount']").val(),
        }

    $.ajax({
        async: true,
        url: `${WITHDRAW_API_URL}`,
        type: 'POST',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          endLoadingButton('#withdraw-button', `<i class="fas fa-dollar-sign"></i> Penarikan</button>`)
        },
        success: function(res) {
            showSuccess("Penarikan Dana", res.message)
            endLoadingButton('#withdraw-button', `<i class="fas fa-dollar-sign"></i> Penarikan</button>`)
            withdraw_table.draw()
            getProfile()
            $('#withdraw-modal').modal("toggle")
        }
    });
  })
})

function renderForm(data){
  let $form = $(`#withdraw-detail-form`)
  $form.find( "input[name='user_name']" ).val(data.user_name)
  $form.find( "input[name='withdraw_number']" ).val(data.withdraw_number)
  $form.find( "input[name='bank_code']" ).val(data.bank_code)
  $form.find( "input[name='bank_account_number']" ).val(data.bank_account_number)
  $form.find( "input[name='bank_account_name']" ).val(data.bank_account_name)
  $form.find( "input[name='amount']" ).val(formatRupiah(data.amount, true))
  $form.find( "textarea[name='reject_reason']" ).val(data.reject_reason)
  $form.find( "input[name='payment_proof_url']" ).val(data.payment_proof_url)

  let color = 'light'
  if(data.status == 'REQUESTED') color = 'secondary'
  else if(data.status == 'REJECTED') color = 'danger'
  else if(data.status == 'PAID') color = 'success'
  let badge = `<span class="badge badge-pill bg-${color}">${data.status}</span>`
  $('#status').html(badge)
}


function getWithdrawDetail(){
  $('#withdraw-detail-overlay').show()
  $.ajax({
      async: true,
      url: `${WITHDRAW_API_URL}detail-seller/${WITHDRAW_ID}`,
      type: 'GET',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
      },
      error: function(res) {
        const response = JSON.parse(res.responseText)
        let isRetry = retryRequest(response)
        if(isRetry) $.ajax(this)
        else $('#withdraw-detail-modal').modal('toggle')
      },
      success: function(res) {
        console.log("GET DETAIL: ", res)
        const response = res.data
        renderForm(response)
        $('#withdraw-detail-overlay').hide()
      }
  });
}