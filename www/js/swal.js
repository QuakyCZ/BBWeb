$(document).ready(function() {

    $('[data-toggle="sweetalert"]').click(function() {

        let options = {
            title: $(this).data('title'),
            text: $(this).data('text'),
            icon: $(this).data('scope'),
        }

        if ($(this).data('type') === 'confirm')
        {
            options.showCloseButton =  true;
            options.showDenyButton = true;
            options.confirmButtonText = 'Ano';
            options.denyButtonText = 'Ne';

            Swal.fire(options).then(result => {
                if (result.isConfirmed)
                {
                    let link = $(this).data('link');
                    switch ($(this).data('action'))
                    {
                        case 'link':
                            window.location.href = link;
                            break;
                        case 'ajax':
                            $.nette.ajax(link);
                            break;
                    }
                }
            })
        } else {
            Swal.fire(options);
        }
    });

});