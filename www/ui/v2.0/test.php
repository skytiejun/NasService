
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Bootstrap Admin Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="css/plugins/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>



			<div class="row">
				<div class="col-lg-12" style="padding-top:15px">
						        <table class="table table-hover">  
								  <thead>  
									<tr>  
									  <th>编号 ID</th>  
									  <th>描述 DESC</th>  
									  <th>状态 STATUS</th>  
									  <th>动作 ACTION</th>  
									</tr>  
								  </thead>  
								  <tbody>  
									<tr>  
									  <td>1</td>  
									  <td>RPi.PCF8574.IO0</td>  
									  <td>ON</td>  
									  <td><button type="button" class="btn btn-primary btn-xs">Toggle</button></td>  
									</tr>  
									<tr>  
									  <td>2</td>  
									  <td>RPi.PCF8574.IO1</td>  
									  <td>OFF</td>  
									  <td><button type="button" class="btn btn-primary btn-xs">Toggle</button>  </td>  
									</tr>  
								  </tbody>  
								</table>
					</div>

				</div>



			<!--/내용-->

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

<script>  
$(document).ready(function(){  
  $('.btn').on("click",function(){  
    // 获得 tr元素  
    var trobj = $(this).parent().parent();  
    // tr元素中包含 dev_id属性  
    var dev_id = trobj.attr('dev_id');  
    console.log( dev_id );  
    // 访问该tr元素的所有子td元素  
    var tdobj = $(trobj).children("td");  
     
    var status_obj = $(tdobj).eq(2);  
    var status_str = status_obj.text();  
    console.log(status_str);  
  
    if( status_str == "on"){  
      status_obj.text("off");  
      sendLedControl( dev_id , "off" );  
    }else{  
      status_obj.text("on");  
      sendLedControl( dev_id , "on" );  
    }  
  });  
});  

function sendLedControl( dev_id , cur_status ){  
  $.ajax({  
    url: '/api/leds/' + dev_id, // /api/leds/1  
    async: true,  
    dataType: 'json',  
    type: 'PUT',  
    data: JSON.stringify({status:cur_status}),  
  
    success: function(data , textStatus){  
      console.log("success");  
    },  
     
    error: function(jqXHR , textStatus , errorThrown){  
      console.log("error");  
    },  
     
  });  
}  
</script>  
</body>

</html>


