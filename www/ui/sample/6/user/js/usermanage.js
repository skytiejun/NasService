function showGroup(){
	var group =$("#group option:selected").val();
		selectgroup(group);
}

function save(type){

	var falg = 0; 
	$("input[name='userlist[]']:checkbox").each(function () { 
		if ($(this).attr("checked")) { 
		falg += 1; 
	}
	}) 
	if (falg > 0){
		if(type == "u"){
			document.getElementById("useredit").action = "useredit.html";
			document.getElementById("useredit").submit();
		}else if(type == "d"){

				//document.getElementById("useredit").action = "user_manage.php";
				//document.getElementById("useredit").submit();
			
				msgBox("선택한 사용자를 삭제하시겠습니까?","사용자 삭제");
			
		}
	}else{
		if(type == "u"){
			colsBox("사용자를 선택하세요","사용자 수정");
		}else if(type == "d"){
			colsBox("사용자를 선택하세요","사용자 삭제");
		}
		
	}
}
function msgBox(msg,title){
		  var tmpl = [
			'<div class="modal hide fade" tabindex="-1">',
			  '<div class="modal-header">',
				'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>',
				'<h3>'+title+'</h3>', 
			  '</div>',
			  '<div class="modal-body">',
				'<p>'+msg+'</p>',
			  '</div>',
			  '<div class="modal-footer">',
				'<a href="#" data-dismiss="modal" class="btn">취소</a>',
				'<a href="#" class="btn btn-primary" onclick=setURL("user_manage.php") >확인</a>',
			  '</div>',
			'</div>'
		  ].join('');
		  
		  $(tmpl).modal();
}
function colsBox(msg,title){
		  var tmpl = [
			'<div class="modal hide fade" tabindex="-1">',
			  '<div class="modal-header">',
				'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>',
				'<h3>'+title+'</h3>', 
			  '</div>',
			  '<div class="modal-body">',
				'<p>'+msg+'</p>',
			  '</div>',
			  '<div class="modal-footer">',
				'<a href="#" data-dismiss="modal" class="btn">닫기</a>',
			  '</div>',
			'</div>'
		  ].join('');
		  
		  $(tmpl).modal();
}

function setURL(url){

	document.getElementById("useredit").action = url;
	document.getElementById("useredit").submit();
}