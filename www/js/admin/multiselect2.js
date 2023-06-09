$(document).ready(function(){
    $('.multiselect2').each((i, el) => {
        new MSFmultiSelect(
            el,
            {
                selectAll: true,
                searchBox: true,
                placeholder: 'Vybrat',
                className: 'form-select',
                width: '100%'
            }
        )
    });
});