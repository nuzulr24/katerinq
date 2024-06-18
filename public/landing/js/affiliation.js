$(document).ready(function(){
  //render datatable
  let affiliation_table = $('#affiliation-datatable').DataTable( {
    processing: true,
    serverSide: true,
    searching: true,
    ordering: false,
    ajax: {
      async: true,
      url: `${USER_API_URL}affiliate`,
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
        data: "first_name",
        render: function (data, type, row, meta) {
          return `${data} ${row.last_name}`
        }
      },
      { 
        data: "email"
      },
      { 
        data: "created_at",
        render: function (data, type, row, meta) {
          return formatLocalDatetimeStr(data)
        }
      }  
    ]
  });
})