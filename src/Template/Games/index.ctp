
<div class="pagination pagination-large">
    <ul class="pagination">
        <?php
        echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
        echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
        echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
        ?>
    </ul>
</div>


<table>
    <tr>
        <th>Game ID</th><th>Team A</th><th>Team B</th><th>Team A AI</th><th>Team B AI</th><th>Start AI</th><th>Win AI</th><th>Started</th><th>Turn count</th><th>Is Finished</th>
    </tr>

    <?php
    $i = 0;
    foreach($games as $game):
        $i++;
?>
        <?php if($i%2==0):?><tr style="background-color:lightcyan">
        <?php else: ?><tr style="background-color:white"><?php endif;?>
        <td><a href="/Games/view/<?=$game->id?>"><?php echo $game['id']?></a></td>
        <td><?php echo $game['a_group']['name']?></td>
        <td><?php echo $game['b_group']['name']?></td>
        <td><?php echo $game['a_group_ai']['name'].":".$game['a_group_ai']['id']?></td>
        <td><?php echo $game['b_group_ai']['name'].":".$game['b_group_ai']['id']?></td>
        <td><?php echo $game['start_ai_id']?></td>
        <td><?php echo $game['win_ai']['id']?></td>
        <td><?php echo $game['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');?></td>
        <td><?php echo $game['current_game_turn']['current_count'];?></td>
        <td><?php echo ($game['is_finished']==1)?"Finished":"Playing";?></td>
    </tr>

<?php endforeach; ?>
</table>
<div class="pagination pagination-large">
    <ul class="pagination">
        <?php
        echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
        echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
        echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
        ?>
    </ul>
</div>