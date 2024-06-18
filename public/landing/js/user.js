$(document).ready(function(){
    if(!SESSION){ 
        window.location.href = `${WEB_URL}login?source=${window.location.href}`
    }
});
