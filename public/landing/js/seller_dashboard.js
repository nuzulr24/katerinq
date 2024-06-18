$(document).ready(function(){
  getTotalOrder("active")
  getTotalWebsite()
  getSellerIncome()

  $('#seller-order-datatable').DataTable( {
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
        }
    ]
  });
})

function getTotalOrder(status){
  let queryParam = ''
  if(status == 'total'){
    queryParam = "?status=ON WORKING,SUBMITTED,REVISION,COMPLETED"
  }else if(status == 'complete'){
    queryParam = "?status=COMPLETED"
  }else if(status == 'active'){
    queryParam = "?status=ON WORKING,SUBMITTED,REVISION"
  }

  $.ajax({
    async: true,
    url: `${ORDER_API_URL}seller-total${queryParam}`,
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
      renderOrderTotal(res.data, status)
    }
  });
}

function getTotalWebsite(){
  $.ajax({
    async: true,
    url: `${WEBSITE_API_URL}user-count?status=ACTIVE`,
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
      renderWebsiteTotal(res.data)
    }
  });
}

function getSellerIncome(){
  $.ajax({
    async: true,
    url: `${ORDER_API_URL}income`,
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
      renderSellerIncome(res.data)
    }
  });
}


function renderOrderTotal(data, status){
  if(status == 'total'){
    $('#seller-order-total').html(data)
  }else if(status == 'complete'){
    $('#seller-order-complete').html(data)
  }else if(status == 'active'){
    $('#seller-order-active').html(data)
  }
}

function renderWebsiteTotal(data){
  $('#seller-website-total').html(data)
}

function renderSellerIncome(data){
  const total = parseFloat(data.website_income) + parseFloat(data.content_income)
  $('#seller-income-total').html(formatRupiah(total.toString(), true))
}