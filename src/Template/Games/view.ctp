<table>
    <tr>
        <th>TURN_ID</th><th>TURN_COUNT</th><th>AI_ID</th><th>ACTION_CODE</th><th>ATK_CARD</th><th>TGT_CARD</th><th>TGT_NUMBER</th><th>CAN_STAY</th><th>IS_STAY</th><th>IS_SUCCESS_ATK</th><th>TURN_STARTED</th><th>TURN_ENDED</th><th>SEC</th>
    </tr>
<?php
    $i = 0;
    foreach($gameTurns->getWellFormedHistories() as $game):
    $i++;
?>
    <?php if($i%2==0):?><tr style="background-color:lightcyan">
    <?php else: ?><tr style="background-color:white"><?php endif;?>
        <td><?php echo $game['TURN_ID']?></td>
        <td><?php echo $game['COUNT']?></td>

        <td><?php echo $game['AI_ID']?></td>
        <td><?php echo $game['ACTION_CODE']?></td>
        <td><?php echo ($game['ATK_CARD']['COLOR'].":".$game['ATK_CARD']['NUMBER'])?></td>
        <td><?php echo ($game['TGT_CARD']['COLOR'].":".$game['TGT_CARD']['NUMBER'])?></td>
        <td><?php echo ($game['TGT_NUMBER'])?></td>
        <td><?php echo ($game['CAN_STAY'])?1:0?></td>
        <td><?php echo ($game['IS_STAY'])?1:0?></td>
        <td><?php echo ($game['IS_SUCCESS_ATK'])?1:0?></td>
        <td><?php echo date('H:i:s',$game['TURN_STARTED'])?></td>
        <td><?php echo date('H:i:s',$game['TURN_ENDED'])?></td>
        <td><?php echo $game['TURN_ENDED'] - $game['TURN_STARTED'] ?></td>



        <!--        <td>--><?php //echo $game['start_ai_id']?><!--</td>-->
<!--        <td>--><?php //echo $game['win_ai']['id']?><!--</td>-->
<!--        <td>--><?php //echo $game['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');?><!--</td>-->
<!--        <td>--><?php //echo $game['current_game_turn']['current_count'];?><!--</td>-->
<!--        <td>--><?php //echo ($game['is_finished']==1)?"Finished":"Playing";?><!--</td>-->
    </tr>

<?php endforeach?>



