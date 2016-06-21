<table border=1 align="center">
	<tr>
		<th><?php echo LG_SERVER_NAME?></th><th><?php echo LG_PLAYER_ID?></th><th><?php echo LG_MONEY?></th><th><?php echo LG_GEMSTONE?></th><th><?php echo LG_RECHARGE_TIME?></th><th><?php echo LG_STATUS?></th><th><?php echo LG_ADD_TO_GAME_TIME?></th><th><?php echo LG_TRANSACTION_ID?></th>
	</tr>
	<?php
		foreach($result as $row){
			echo "<tr><td>{$row->server_name}</td><td>{$row->playerid}</td><td>{$row->money}</td><td>{$row->yuanbao}</td><td>{$row->activetime}</td><td>{$row->has_add_to_game}</td><td>{$row->successtime}</td><td>{$row->orderid}</td></tr>";
		}
	?>
</table>