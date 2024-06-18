$(document).ready(function(){
  getOrder("total")
  getOrder("complete")
  getOrder("active")

  $('#buyer-order-datatable').DataTable( {
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
      }
    ]
  });
})

function getOrder(status){
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
    url: `${ORDER_API_URL}buyer-total${queryParam}`,
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

function renderOrderTotal(data, status){
  if(status == 'total'){
    $('#buyer-order-total').html(data)
  }else if(status == 'complete'){
    $('#buyer-order-complete').html(data)
  }else if(status == 'active'){
    $('#buyer-order-active').html(data)
  }
}