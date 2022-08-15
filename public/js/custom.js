$(()=>{
    let body = $('body');

    body.on('click','.button-site-delete', function (e){
        let siteContainer = $(this).parentsUntil('.border-b').last();
        let siteId = siteContainer.data('siteId');
        $.ajax({
            url:'sites/'+siteId,
            method:'delete',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:(response)=>{
                if (response){
                    siteContainer.remove();
                }
            },
            error:(e)=>{
                console.log(e);
            }
        });
    });
    $('.toggle-site-active').on('change', function (e){
        let siteId = $(this).parentsUntil('.border-b').last().data('siteId');
        $.ajax({
            url:'/sites/activity/'+siteId,
            method:'post',
            data:{'isActive':+this.checked},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:(response)=>{
                if (response){
                    console.log(response);
                }
            },
            error:(e)=>{
                console.log(e);
            }
        });
    });

});
