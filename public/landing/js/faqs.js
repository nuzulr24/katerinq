
$(document).ready(function(){
    getFaqs()
})

function getFaqs(){
    $.ajax({
        async: true,
        url: `${FAQS_API_URL}`,
        type: 'GET',
        error: function(res) {
          const response = JSON.parse(res.responseText)
        },
        success: function(res) {
            renderFaqs(res.data)
        }
    });
}

function renderFaqs(data){
    let faqHtml = ''
    for(let i=0; i<data.length; i++){
        const isShow = i == 0 ? "show" : ''
        const item_html = `<div class="accordion-item border-0 rounded-3 shadow-sm mb-3">
            <h3 class="accordion-header" id="heading-${i}">
                <button class="accordion-button shadow-none rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${i}" aria-expanded="true" aria-controls="collapse-${i}">
                    ${data[i].question}
                </button>
            </h3>
            <div class="accordion-collapse collapse ${isShow}" id="collapse-${i}" aria-labelledby="heading-${i}" data-bs-parent="#faq-content">
                <div class="accordion-body pt-0">
                    ${data[i].answer}
                </div>
            </div>
        </div>`
        faqHtml += item_html
    }
    $('#faq-content').html(faqHtml)
}