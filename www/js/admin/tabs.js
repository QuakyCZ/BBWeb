$(document).ready(function () {
    $('a[role="tab"]').on('click', function () {
        window.location.hash = $(this).attr('data-bs-target');
    });

    let hash = window.location.hash;
    if (hash !== '') {
        $('a[data-bs-target="' + hash + '"]').tab('show');
    }
});