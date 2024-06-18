let WEBSITE_ID
    IS_CONTENT_INCLUDED = false

$(document).ready(function(){
  if($('#category-option')){
    getCategory()
  }

  //render datatable
  let website_table = $('#website-datatable').DataTable( {
      processing: true,
      serverSide: true,
      searching: true,
      ordering: false,
      ajax: {
        async: true,
        url: `${WEBSITE_API_URL}by-seller`,
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
            data: "website_url",
            className: "dt-body-center",
            render: function (data, type, row, meta) {
              let url = `<a href="${data}" target="_blank">${data}</a>`
              if(parseInt(row.is_premium)){
                url += ' <i class="fas fa-crown"></i>'
              }
              return url
            }
          },
          { 
            data: "category",
            render: function (data, type, row, meta) {
              let categoryHtml = ``
              for(const item of data){
                const badge = `<span class="badge badge-pill bg-dark">${item.category_name}</span> &nbsp;`
                categoryHtml += badge
              }
              return categoryHtml
            }
          },
          { 
            data: "guest_post_price",
            render: function (data, type, row, meta) {
              return formatRupiah(data, true)
            }
          },
          { 
            data: "da",
            render: function (data, type, row, meta) {
              return data ? data : '-'
            }
          },
          { 
            data: "pa",
            render: function (data, type, row, meta) {
              return data ? data : '-'
            }
          },
          { 
            data: "status",
            render: function (data, type, row, meta) {
              let color = "light"
              if(data == 'INACTIVE'){
                color = 'danger'
              }else if(data == 'ONVERIFY'){
                color = 'info'
              }else if(data == 'ACTIVE'){
                color = 'primary'
              }else if(data == 'BANNED'){
                color = 'danger'
              }
              let badge = `<span class="badge badge-pill bg-${color}">${data}</span>`

              return badge
            }
          },
          { 
            data: "id",
            render: function (data, type, row, meta) {
              let button = `<button class='btn btn-secondary website-detail-toggle' data-id="${data}" data-bs-toggle="modal" data-bs-target="#website-detail-modal" title="detail">
                <i class='fas fa-search'></i>
              </button>`
              if(row.status == 'ACTIVE'){
                if(parseInt(row.is_premium)){
                  if(row.subscribe_status != 'UNSUBSCRIBE'){
                    button += `<button class='btn btn-danger website-unsubscribe-premium-toggle' data-id="${data}" data-bs-toggle="modal" data-bs-target="#website-unsubscribe-premium-modal" title="Berhenti langganan website premium">
                      <i class='fas fa-crown'></i>
                    </button>`
                  }
                }else{
                  button += `<button class='btn btn-warning website-premium-toggle' data-id="${data}" data-url="${row.website_url}" data-bs-toggle="modal" data-bs-target="#website-premium-modal" title="Daftarkan website menjadi premium">
                  <i class='fas fa-crown'></i>
                </button>`
                }
              }
              return button
            }
          }
      ]
  });

  $('#is_content_included').change(function(){
    IS_CONTENT_INCLUDED = $(this).is(":checked")
    if( IS_CONTENT_INCLUDED ){
      $('#content_price_field').hide()
      // $('#content_price').prop("required", false)
    }else{
      $('#content_price_field').show()
      // $('#content_price').prop("required", true)
    }
  })

  $('#website-form').submit(function(e){
    e.preventDefault()

    var categorySelected = $('input[name="category[]"]:checked');
    if(categorySelected.length <= 0){
      showError("Tambah Website", "Kategori tidak boleh kosong")
      return
    }
    
    let category = []
    for(const cb of categorySelected){
      category.push({id: $(cb).val()})
    }

    let $form = $('#website-form'),
        request = {
          role: $('#role').find(":selected").val(),
          website_url: $form.find("input[name='website_url']").val(),
          guest_post_price: $form.find("input[name='guest_post_price']").val(),
          delivery_time: $form.find("input[name='delivery_time']").val(),
          word_limit: $form.find("input[name='word_limit']").val(),
          is_content_included: IS_CONTENT_INCLUDED,
          content_price: IS_CONTENT_INCLUDED ? 0 : $form.find("input[name='content_price']").val(),
          guest_post_sample: $form.find("textarea[name='guest_post_sample']").val(),
          website_description: $form.find("textarea[name='website_description']").val(),
          category: category
        }

    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}`,
        type: 'POST',
        data: JSON.stringify(request),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          else showError("Tambah Website", "Gagal mendaftarkan website")
          endLoadingButton('#website-button', 'Submit')
        },
        success: function(res) {
            showSuccess("Tambah Website", res.message, `${WEB_URL}account/seller/website`)
            endLoadingButton('#website-button', 'Submit')
        }
    });

  })

  $('#website-premium-form').submit(function(e){
    e.preventDefault()

    let $form = $('#website-premium-form'),
        request = {
          website_id: WEBSITE_ID,
          subscribe_type: $('#premium_type').find(":selected").val()
        }
    
    let premium_price = $form.find( "input[name='premium_price']" ).val()
    premium_price = parseInt(premium_price.replace(/\./g, "")) 
    let balance = parseInt(PROFILE.balance)

    if(balance >= premium_price){
      $.ajax({
          async: true,
          url: `${WEBSITE_API_URL}premium-subscribe`,
          type: 'POST',
          data: JSON.stringify(request),
          beforeSend: function (xhr) {
              xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
          },
          error: function(res) {
            const response = JSON.parse(res.responseText)
            let isRetry = retryRequest(response, window.location.href)
            if(isRetry) $.ajax(this)
            else showError("Daftar Website Premium", "Gagal mendaftarkan website premium")
            endLoadingButton('#website-premium-button', 'Daftar Premium')
          },
          success: function(res) {
              showSuccess("Daftar Website Premium", res.message, `${WEB_URL}account/seller/website`)
              endLoadingButton('#website-premium-button', 'Daftar Premium')
          }
      });
    }else{
      showError("Saldo Anda tidak cukup", "Gagal mendaftarkan website premium")
    }

  })

  $('#website-unsubscribe-premium-button').click(function(){
    $.ajax({
        async: true,
        url: `${WEBSITE_API_URL}premium-unsubscribe`,
        type: 'POST',
        data: JSON.stringify({website_id: WEBSITE_ID}),
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
        },
        error: function(res) {
          const response = JSON.parse(res.responseText)
          let isRetry = retryRequest(response, window.location.href)
          if(isRetry) $.ajax(this)
          else showError("Berhenti Langganan Website Premium", "Gagal berhenti langganan website premium")
          endLoadingButton('#website-unsubscribe-premium-button', 'IYA')
        },
        success: function(res) {
            showSuccess("Berhenti Langganan Website Premium", "Berhenti langganan premium berhasil! Website tetap premium hingga batas akhir langganan sebelumnya.", `${WEB_URL}account/seller/website`)
            endLoadingButton('#website-unsubscribe-premium-button', 'IYA')
        }
    });

  })

  $("body").delegate(".website-detail-toggle", "click", function(e) {
    WEBSITE_ID = $(this).data('id')
    getDetailWebsite()
  })

  $("body").delegate(".website-premium-toggle", "click", function(e) {
    WEBSITE_ID = $(this).data('id')
    let website_url = $(this).data('url')
    let premium_price = getPremiumPrice('DAILY')
    $('#website_url').val(website_url)
    $('#premium_price').val(formatRupiah(premium_price.toString()))
  })

  $('#premium_type').change(function(){
    let type = $(this).val()
    let premium_price = getPremiumPrice(type)
    $('#premium_price').val(formatRupiah(premium_price.toString()))
  })

  $("body").delegate(".website-unsubscribe-premium-toggle", "click", function(e) {
    WEBSITE_ID = $(this).data('id')
  })

})

function getCategory(){
  $.ajax({
    async: true,
    url: `${CATEGORY_API_URL}all`,
    type: 'GET',
    error: function(res) {
      const response = JSON.parse(res.responseText)
    },
    success: function(res) {
        renderCategory(res.data)
    }
  });
}

function getDetailWebsite(){  
  $('#website-detail-loading').show()

  $.ajax({
    async: true,
    url: `${WEBSITE_API_URL}by-id/${WEBSITE_ID}`,
    type: 'GET',
    beforeSend: function (xhr) {
      xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
    },
    error: function(res) {
      const response = JSON.parse(res.responseText)
      let isRetry = retryRequest(response)
      if(isRetry) $.ajax(this)
      else $('#website-detail-modal').modal('toggle')
    },
    success: function(res) {
      const response = res.data
      renderForm(response)
      $('#website-detail-loading').hide()
    }
  });
}

function renderCategory(category){
  let categoryHtml = ``
  for(const [index, item] of category.entries()){
    const itemHtml = `<div class='form-check form-check-inline col-lg-3'>
      <input class='form-check-input' type='checkbox' name="category[]" id='checkbox-${index}' value='${item.id}'>
      <label class='form-check-label' for='checkbox-${index}'>${item.name}</label>
    </div>`
    categoryHtml += itemHtml
  }

  $('#category-option').html(categoryHtml)
}

function renderForm(data){
  $form = $('#website-detail-form')
  $form.find( "input[name='website_url']" ).val(data.website_url)
  $form.find( "textarea[name='website_description']" ).val(data.website_description)
  $form.find( "input[name='role']" ).val(data.role)
  $form.find( "input[name='link_type']" ).val(data.link_type)
  $form.find( "input[name='guest_post_price']" ).val(formatRupiah(data.guest_post_price, true))
  $form.find( "input[name='delivery_time']" ).val(`${data.delivery_time} Hari`)
  $form.find( "input[name='word_limit']" ).val(`${data.word_limit} Kata`)
  $form.find( "textarea[name='guest_post_sample']" ).val(data.guest_post_sample)
  $form.find( "input[name='da']" ).val(data.da ? data.da : "-")
  $form.find( "input[name='pa']" ).val(data.pa ? data.pa : "-")
  if(parseInt(data.is_content_included)){
    $('#is_content_included').html("IYA")
  }else{
    $('#is_content_included').html("TIDAK")
  }

  if(data.content_price){
    $form.find( "input[name='content_price']" ).val(formatRupiah(data.content_price))
  }else{
    $form.find( "input[name='content_price']" ).val("-")
  }

  let color = "light"
  if(data.status == 'INACTIVE'){
    color = 'warning'
  }else if(data.status == 'ONVERIFY'){
    color = 'info'
  }else if(data.status == 'ACTIVE'){
    color = 'primary'
  }else if(data.status == 'BANNED'){
    color = 'danger'
  }
  let badge = `<span class="badge badge-pill bg-${color}">${data.status}</span>`
  $('#status-website').html(badge)

  $('#premium-website').html(parseInt(data.is_premium) ? 'IYA' : 'TIDAK')
  $('#premium-type-website').html(data.premium_type ? data.premium_type : '-')
}