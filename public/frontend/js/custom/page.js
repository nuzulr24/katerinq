$(document).ready(()=>{$("button[type=submit]").attr("type","button"),$("button").click(()=>{$(".indicator-label").addClass("d-none"),$("button").prop("disabled",!0),$(".indicator-progress").addClass("d-block"),setTimeout(()=>{$("button").prop("disabled",!1),$(".indicator-progress").removeClass("d-block"),$(".indicator-label").removeClass("d-none"),$("form").submit()},2e3)})});