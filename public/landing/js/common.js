let SESSION = localStorage.getItem("user-token"),
    REFRESH_SESSION = localStorage.getItem("user-refresh-token"),
    CATEGORY_API_URL = API_URL + "category/",
    BLOG_CATEGORY_API_URL = API_URL + "blog_category/",
    BLOG_API_URL = API_URL + "blog/",
    COMMISSION_API_URL = API_URL + "commission/",
    COMPANY_API_URL = API_URL + "company/",
    CONFIG_API_URL = API_URL + "config/",
    DEPOSIT_API_URL = API_URL + "deposit/",
    FAQS_API_URL = API_URL + "faq/",
    INFO_API_URL = API_URL + "page/",
    USER_API_URL = API_URL + "user/",
    ORDER_API_URL = API_URL + "order/",
    VENDOR_API_URL = API_URL + "vendor/",
    WEBSITE_API_URL = API_URL + "website/",
    NOTIFICATION_API_URL = API_URL + "notification/",
    WITHDRAW_API_URL = API_URL + "withdraw/",
    WIDGET_API_URL = API_URL + "widget/",
    REVIEW_API_URL = API_URL + "review/",
    PROMO_CODE_API_URL = API_URL + `promo_code/`,
    RETRY_COUNT = 0,
    CATEGORY = [],
    CONFIG,
    PROFILE

$(document).ready(function(){

    renderNavbar()
    getConfig()
    getWidget()
    // getInfoHeader()
    getInfoFooter()

    if(SESSION){
      getProfile()
      getNotification()
    }

    $('#summernote').summernote({
        placeholder: 'Tulis konten Anda disini',
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
})

/* API */
function getConfig(){
  $.ajax({
      async: true,
      url: `${CONFIG_API_URL}`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
      },
      success: function(res) {
        CONFIG = res.data
        renderIcon(res.data)
        renderFooter(res.data)
        renderAboutUs(res.data)
      }
  });
}

function getWidget(){
  $.ajax({
      async: true,
      url: `${WIDGET_API_URL}all`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
      },
      success: function(res) {
        renderWidget(res.data)
      }
  });
}

function getInfoFooter(){
  $.ajax({
      async: true,
      url: `${INFO_API_URL}position?position=FOOTER,BOTH`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
      },
      success: function(res) {
          renderFooterMenu(res.data)
      }
  });
}

function getInfoHeader(){
  $.ajax({
      async: true,
      url: `${INFO_API_URL}position?position=HEADER,BOTH`,
      type: 'GET',
      error: function(res) {
        const response = JSON.parse(res.responseText)
      },
      success: function(res) {
          renderHeaderMenu(res.data)
      }
  });
}

function getProfile(){
  $.ajax({
      async: true,
      url: `${USER_API_URL}profile`,
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
        PROFILE = res.data
        renderProfile(res.data)
        try {
          renderProfileForm()
        }catch(e){
          console.log(e)
        }
      }
  });
}

function getCartTotal(){
  $.ajax({
      async: true,
      url: `${CART_API_URL}item-total`,
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
        renderCartTotal(res.data)
      }
  });
}

function getNotification(){
  $.ajax({
      async: true,
      url: `${NOTIFICATION_API_URL}?page_number=0&page_size=5`,
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
        renderNotification(res.data)
      }
  });
}

function readNotification(id){
  $.ajax({
    async: true,
    url: `${NOTIFICATION_API_URL}read/${id}`,
    type: 'PUT',
    beforeSend: function (xhr) {
      xhr.setRequestHeader('Authorization', `Bearer ${SESSION}`);
    },
    error: function(res) {
      const response = JSON.parse(res.responseText)
      let isRetry = retryRequest(response)
      if(isRetry) $.ajax(this)
    },
    success: function(res) {
      console.log("READ NOTiFICATION")
      getNotification()
    }
  });
}
/* API */

/* Render */
function renderWidget(widgets){
  let html_code = ''
  for (const widget of widgets) {
    if(widget.type == 'SCRIPT' || widget.type == 'CSS'){
      $('head').append(widget.code);
    }else{
      html_code += widget.code
    }
  }

  $('#widget-container').html(html_code)
}

function renderProfile(data){
  $('.account-name').html(data.first_name)
  $('.account-email').html(data.email)
  $('.account-balance').html(formatRupiah(data.balance, true))
  // $('#buyer-balance').html(formatRupiah(data.balance, true))
  $('.account-income').html(formatRupiah(data.income, true))
  // $('#seller-income-total').html(formatRupiah(data.income, true))
  // if(parseFloat(data.income) <= 0){
  //   $('#withdraw-create').hide()
  // }
}

function renderCartTotal(data){
  $('#navbar-cart-count').html(data)
}

function renderNotification(data){
  // let notificationHtml = '';
  // if(data.notification.length > 0){
  //   for(let item of data.notification){
  //     const style = parseInt(item.is_read) ? "" : 'style="font-weight:bold"'
  //     const itemHtml = `<li><a href="#" class="notification-detail" data-bs-toggle="modal" data-id="${item.id}" data-content="${item.message}" data-bs-target="#notification-modal" ${style}>${sortText(item.message)}</a></li>`
  //     notificationHtml += itemHtml
  //   }
  //   notificationHtml += `<li><hr class="dropdown-divider"></li>
  //   <li><a class="dropdown-item" href="${WEB_URL}notification">Lihat Semua</a></li>`
  // }else{
  //   notificationHtml = `<li class="text-center"><i>Anda tidak memiliki notifikasi</i></li>`
  // }
  // $('#navbar-notification-list').html(notificationHtml)
  $('#navbar-notification-count').html(data.total)
  if(parseInt(data.total) < 1) $('#navbar-notification-count').hide()
}

function renderAboutUs(data){
  $('#footer-about-us').html(data.web_description)
}

function renderNavbar(){
  console.log("RENDER NAVBAR")

  if(SESSION){
    $('.nav-user').css("display", "block")
    $('.nav-nouser').attr("style", "display: none !important")
    $('#login-button-offcanvas').html(``)
  }else{
    $('.nav-user').attr("style", "display: none !important")
    $('.nav-nouser').css("display", "flex")
    $('#login-button-offcanvas').html(`<a href="${WEB_URL}login" class="btn btn-primary w-100" rel="noopener">
      <i class="bx bx-log-in fs-5 lh-1 me-1"></i>
      &nbsp;Masuk
    </a>`)
  }
}

function renderFooter(data){
  //render contact
  if(data.address){
    $('#footer-address').html(`<p class="fs-sm text-light opacity-70">${data.address}</p>`)
  }else{
    $('#footer-address-section').hide()
  }
  if(data.phone){
    $('#footer-phone').html(data.phone)
  }else{
    $('#footer-phone-section').hide()
  }
  if(data.email){
    $('#footer-email').html(data.email)
  }else{
    $('#footer-email-section').hide()
  }

  if(!data.address && !data.phone && !data.email){
    $('#footer-contact').hide()
  }
  
  //render social media
  let footerSosmed = ``
  if(data.sm_facebook){
    footerSosmed += `<li class="nav-item">
      <a href="${data.sm_facebook}" class="nav-link d-inline-block px-0 pt-1 pb-2">Facebook</a>
    </li>`
  }
  if(data.sm_instagram){
    footerSosmed += `<li class="nav-item">
    <a href="${data.sm_instagram}" class="nav-link d-inline-block px-0 pt-1 pb-2">Instagram</a>
  </li>`
  }
  if(data.sm_twitter){
    footerSosmed += `<li class="nav-item">
    <a href="${data.sm_twitter}" class="nav-link d-inline-block px-0 pt-1 pb-2">Twitter</a>
  </li>`
  }
  if(footerSosmed){
    $('#social-links').html(footerSosmed)
  }else{
    $('#social-links-section').hide()
  }
}

function renderFooterMenu(data){
  let footerMenuHtml = ""
  for(let menu of data){
    let menuHtml = `<li class="nav-item">
      <a href="${WEB_URL}page/${menu.alias}" class="nav-link d-inline-block px-0 pt-1 pb-2">${menu.title}</a>
    </li>`
    footerMenuHtml += menuHtml
  }
  footerMenuHtml += `<li class="nav-item">
    <a href="${WEB_URL}faqs" class="nav-link d-inline-block px-0 pt-1 pb-2">FAQs</a>
  </li>`

  $('#footer-info').html(footerMenuHtml)
}

function renderHeaderMenu(data){
  if(data.length > 0){
    let headerMenuHtml = ""
    for(let item of data){
      const menuHtml = `<li><a href="${WEB_URL}info/${item.alias}">${item.title}</a></li>`
      headerMenuHtml += menuHtml
    }

    $('#navbar-information-list').html(headerMenuHtml)
    $('#header-information').show()
  }
}

function renderIcon(data){
  if(data.web_logo){
    $('.brand-logo').prop("src", API_URL+data.web_logo)
  }else{
    $('.brand-logo-section').hide()
  }
  if(data.web_icon){
    $(".favicon").attr("href", API_URL+data.web_icon);
  }
}

function generateRatingStar(rating){
  let ratingStar = ""
  for(let i=0; i<rating; i++){
      ratingStar += '<i class="fas fa-star" style="color:#ffd27d;"></i>'
  }
  return ratingStar
}
/* Render */

/* Animation */
function startLoadingButton(dom){
  let loading_button = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
  $(dom).html(loading_button)
  $(dom).prop('disabled', true);
}

function endLoadingButton(dom, text){
  $(dom).html(text)
  $(dom).prop('disabled', false);
}

function showError(title, text, confirmLink=null){
  Swal.fire({
      icon: 'error',
      title: title,
      text: text,
      confirmButtonColor: '#6c63ff'
  }).then((result) => {
      if (confirmLink) {
          window.location.href = confirmLink
      }
  }); 
}

function showSuccess(title, text, confirmLink=null){
  Swal.fire({
      icon: 'success',
      title: title,
      text: text,
      confirmButtonColor: '#6c63ff'
  }).then((result) => {
      if (confirmLink) {
          window.location.href = confirmLink
      }
  }); 
}
/* Animation */

/* Session */
function setSession(data){
  console.log("SET NEW SESSION")
  localStorage.setItem("user-token", data.access_token)
  localStorage.setItem("user-refresh-token", data.refresh_token)
  SESSION = data.access_token
  REFRESH_SESSION = data.refresh_token
}

function removeSession(source=null){
  console.log("REMOVE SESSION")
  localStorage.removeItem("user-token");
  localStorage.removeItem("user-refresh-token");
  if(source) window.location.href = `${WEB_URL}login?source=${source}`
  else window.location.href = `${WEB_URL}`
}

function refreshToken(){
  let resp = {}
  $.ajax({
      async: false,
      url: `${USER_API_URL}refresh`,
      type: 'GET',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', `Bearer ${REFRESH_SESSION}`);
      },
      error: function(res) {
        resp = {status: "failed"}
      },
      success: function(res) {
        const response = res.data
        resp = {status: "success", data: response}
      }
  });
  
  return resp
}

function retryRequest(responseError, source){
  if(responseError.code == 401){
    if(RETRY_COUNT < 3){
      let resObj = refreshToken()
      console.log("REFRESH: ", resObj)
      if(resObj.status == 'success'){
        RETRY_COUNT += 1 
        setSession(resObj.data)
        return true
      }else if(resObj.status == 'failed'){
        removeSession(source)
      }
    }else{
      removeSession(source)
    }
  }else{
    showError(responseError.message)
    return false
  }
}
/* Session */


/* Format */
function formatRupiah(angka, prefix){
  var angkaStr = angka.replace(/[^,\d]/g, '').toString(),
      split = angkaStr.split(','),
      sisa = split[0].length % 3,
      rupiah = split[0].substr(0, sisa),
      ribuan = split[0].substr(sisa).match(/\d{3}/gi);

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if(ribuan){
    separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.');
  }

  rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
  return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

function formatLocalDateStr(dateObj){
  const month = dateObj.getMonth()+1;
  const day = String(dateObj.getDate()).padStart(2, '0');
  const year = dateObj.getFullYear();
  const output = day  + '-'+ month  + '-' + year;
  return output
}

function formatLocalDateObj(dateStr){
  let arr = dateStr.split("-")
  return `${arr[2]}-${arr[1]}-${arr[0]}`
}

function formatLocalDatetimeStr(datetime){
  let datetimeArr = datetime.split(" "),
      date = datetimeArr[0],
      time = datetimeArr[1]
  
  let dateArr = date.split("-"),
      year = dateArr[0],
      month = dateArr[1],
      day = dateArr[2]

  return `${day}-${month}-${year} ${time}`
}

function formatDateID(date_str=null){

  let day_arr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  let month_arr = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

  let datetime = date_str ? new Date(date_str) : new Date(),
      day = day_arr[datetime.getDay()],
      date = datetime.getDate(),
      month = month_arr[datetime.getMonth()],
      year = datetime.getFullYear()

  return `${day}, ${date} ${month} ${year}`
}

function sortText(text, size=30){
  return text.length > size ? text.slice(0, size) + ".." : text
}

function getPremiumPrice(subscribe_type){
  if(subscribe_type == 'DAILY'){
    return CONFIG.premium_daily_price;
  }else if(subscribe_type == 'WEEKLY'){
    return CONFIG.premium_weekly_price;
  }else if(subscribe_type == 'MONTHLY'){
    return CONFIG.premium_monthly_price;
  }else if(subscribe_type == '3 MONTH'){
    return CONFIG.premium_3month_price;
  }else if(subscribe_type == '6 MONTH'){
    return CONFIG.premium_6month_price;
  }else if(subscribe_type == 'ANNUAL'){
    return CONFIG.premium_annual_price;
  }else{
    return CONFIG.premium_daily_price;
  }
}

function formatWebsiteAlias(websiteUrl){
  const web_arr = websiteUrl.split("//")
  const web_domain = web_arr.length > 1 ? web_arr[1] : web_arr[0]
  const web_alias = web_domain.replace(/\-/g, "_").replace(/\./g, "-")
  return web_alias
}

function unformatWebsiteAlias(websiteAlias){
  const web_domain = websiteAlias.replace(/\-/g, ".").replace(/\_/g, "-")
  return web_domain
}
/* Format */

/* Common */
function getQueryParams(){
  const urlSearchParams = new URLSearchParams(window.location.search);
  const params = Object.fromEntries(urlSearchParams.entries());
  return params
}

$('#logout-button').click(function(){
  removeSession()
})

$("body").delegate(".notification-detail", "click", function(e) {
  const id = $(this).data('id'),
        content = $(this).data('content')

  $('#notification-content').html(content)

  readNotification(id)
})
/* Common */
