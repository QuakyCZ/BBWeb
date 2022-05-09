$(".ajax").bind("click", function() { 
    $(this).button("loading");});

$(".ajax").bind("ajaxComplete", function() { 
    $(this).button("reset");});