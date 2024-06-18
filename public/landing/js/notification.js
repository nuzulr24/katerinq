$(document).ready(function(){
  getAllNotification()
})

function getAllNotification(){
  $.ajax({
    async: true,
    url: `${NOTIFICATION_API_URL}all`,
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
      console.log("NOTIF: ", res.data)
      renderNotificationAll(res.data)
      readAllNotification()
    }
  });
}

function readAllNotification(){
  $.ajax({
    async: true,
    url: `${NOTIFICATION_API_URL}read-all`,
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
      // getNotification()
    }
  });
}

function renderNotificationAll(notifications){
  console.log("JUMLAH: ", notifications.length)
  if(notifications.length > 0){
    let notificationHtml = ``
    for(const item of notifications){
      let color = parseInt(item.is_read) ? "secondary" : "primary"
      const itemHtml = `<div class="alert alert-${color}" role="alert">
        ${item.message}
      </div>`
      notificationHtml += itemHtml
    }

    $('#notification-list').html(notificationHtml)
    $('#notification-list').show()
    $('#no-notification').hide()
  }else{
    $('#notification-list').hide()
    $('#no-notification').show()
  }
}