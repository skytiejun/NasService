var selectgroup = function(group_name) {
	//alert(group_name);
    $.ajax({
		
        type: 'POST',
			
        url: 'user_info.php',
        data: {
            //'s_date': encodeURIComponent($('#s_date').val()),
			'group_name': group_name
        },
        cache: false,
        async: false,
        success: function(result) {
			
            var msg = $('#group_list');
			
			msg.html(result).css('color', 'red');

           // $('#mb_email_enabled').val(result);
        }
    });
}