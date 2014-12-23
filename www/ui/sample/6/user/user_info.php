<?php
	include_once('./_common.php');

?>
		<link rel="stylesheet" href="/css/uniform.css" />

<table class="table table-bordered table-striped with-check">
		<thead>
			<tr>
				<th><input type="checkbox" id="title-table-checkbox" name="title-table-checkbox" /></th>
				<th>아이디</th>
				<th>설명</th>
				<th>이메일</th>
				<th>상태</th>
			</tr>
		</thead>
		<tbody>
		
		<?
			$user = $AnySql->getUser($userID,$group_name);

			foreach($user as $list){
		?>
			<tr>
				<td><input type="checkbox" id="event-user-list" name="userlist[]" value="<?=$list[US_ID]?>"/></td>
				<td><?=$list[US_ID]?></td>
				<td><?=$list[US_INTRO]?$list[US_INTRO]:"-"?></td>
				<td><?=$list[US_EMAIL]?></td>
				<td><?=$list[US_STATUS]?></td>
			</tr>
			<?
			}
			?>
		</tbody>
</table>
