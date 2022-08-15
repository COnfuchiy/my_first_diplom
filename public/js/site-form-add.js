function pageSelectSwitcher(inputValue){
    if (inputValue ==='manual'){
        $('#sitemap-url').parent().parent().hide();
        $('#manual-input-text-area').parent().parent().show();
    }
    if (inputValue==='sitemap'){
        $('#sitemap-url').parent().parent().show();
        $('#manual-input-text-area').parent().parent().hide();
    }
}

function viewCheckedInput(checkValue, inputClass){
    if (checkValue) {
        $(inputClass).parent().show();
    }
    else {
        $(inputClass).parent().hide();
    }
}
$(()=>{

    let pageSelect = $('input[name=page_select_type]');
    let psiMobileCheck = $('#seo-psi-mobile-check');
    let psiDesktopCheck = $('#seo-psi-desktop-check');
    let sslCheck = $('#ssl-check');
    pageSelectSwitcher($('input[name=page_select_type]:checked').val());
    viewCheckedInput(psiMobileCheck[0].checked,'#seo-psi-mobile-min-value');
    viewCheckedInput(psiDesktopCheck[0].checked,'#seo-psi-desktop-min-value');
    viewCheckedInput(sslCheck[0].checked,'#ssl-notify-num-days');


    pageSelect.on('change', function (e){
        pageSelectSwitcher(e.target.value);
    });

    psiMobileCheck.on('change', function (e){
        viewCheckedInput(e.target.checked,'#seo-psi-mobile-min-value');
    });
    psiDesktopCheck.on('change', function (e){
        viewCheckedInput(e.target.checked,'#seo-psi-desktop-min-value');
    });
    sslCheck.on('change', function (e){
        viewCheckedInput(e.target.checked,'#ssl-notify-num-days');
    });

    $('#domain').on('paste', function (e){
        try{
            let clipboardData = e.clipboardData || window.clipboardData || e.originalEvent.clipboardData;
            let data = clipboardData.getData('text/plain');
            console.log(data);

            let url = new URL(data);
            this.value = url.href.replace(url.protocol+'//','');
            let protocolContainer = $('.domain-protocol');
            if (protocolContainer.html()!== url.protocol ){
                protocolContainer.html(url.protocol+'//');
            }
            e.preventDefault();
        } catch (e) {
        }
    }).on('change', function (e){
        try{
            let url = new URL(e.target.value);
            this.value = url.href.replace(url.protocol+'//','');
            let protocolContainer = $('.domain-protocol');
            if (protocolContainer.html()!== url.protocol ){
                protocolContainer.html(url.protocol+'//');
            }
        } catch (e) {
        }
    });

    $('#domain').trigger("change");

    // $('.site-form').on('submit', function (e) {
    //    e.preventDefault();
    //    console.log($(this).serialize());
    //    return false;
    // });
});
