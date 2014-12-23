<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JQuery 判断checkbox是否选中，checkbox全选，获取checkbox选中值</title>
<script type="text/javascript" language="javascript" src="http://code.jquery.com/jquery-1.6.4.min.js" ></script>
<script type="text/javascript">
$(function(){
      /*------------
        全选/全不选
        ------------*/
     $('#cboxchecked').click(function(){
         //判断apple是否被选中
         var bischecked=$('#cboxchecked').is(':checked');
         var fruit=$('input[name="fruit"]');
         bischecked?fruit.attr('checked',true):fruit.attr('checked',false);
         });
         /*-------------
            获取选中值
          -------------*/
        $('#btn_submit').submit(function(){
            $('input[name="fruit"]:checked').each(function(){
                var sfruit=$(this).val();
                alert(sfruit);
                });
                return false;
            });
    })
</script>
</head>
 
<body>
<form action="">
  <input type="checkbox"  id="cboxchecked" />
  <label for="cboxchecked">全选/全不选</label>
  <br />
  <br />
  <input type="checkbox"  id="cboxapple" name="fruit" value="apple" />
  <label for="apple">Apple</label>
  <input type="checkbox"  id="cboxorange" name="fruit" value="orange" />
  <label for="orange">Orange</label>
  <input type="checkbox"  id="cboxbanana" name="fruit" value="banana" />
  <label for="banana">Banana</label>
  <input type="checkbox"  id="cboxgrapes" name="fruit" value="grapes" />
  <label for="grapes">Grapes</label>
  <br />
  <br />
  <input type="submit" id="btn_submit" value="submit" />
</form>
</body>
</html>