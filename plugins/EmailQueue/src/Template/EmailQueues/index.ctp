<div style="float:left;padding-right:20px"><?php echo count($emails)?> emails</div>
<div><?php echo $this->Html->link('Eliminar enviados hace 2 semanas', array('action'=>'remove_sent'), array('confirm'=>'¿Estás seguro que quieres eliminar estos emails?'))?></div>

<br/>
<div>Páginas: <?php echo $this->Paginator->numbers();?></div>
<br/>

<table class='table table-striped table-hover'>
    <thead><th></th><th>Lang</th><th>To</th><th>Subject</th><th>Sent</th><th>Locked</th><th>Send Tries</th><th>Send at</th><th>Template</th><th>Template Vars</th></thead>
    <tbody> 
    <?php foreach ($emails as $e): ?>
        <tr>
            <td>
                <?php if($e['EmailQueue']['sent'] || $e['EmailQueue']['send_tries'] > 3):?>
                    <ul class="list-inline">
                        <li><?php echo $this->Html->link('Eliminar', array('action'=>'remove/'.$e['EmailQueue']['id']), array('escape'=>false, 'confirm'=>'¿Está seguro que quiere eliminar este email?'))?></li>
                        <li><?php echo $this->Html->link('Reenviar', array('action'=>'resend/'.$e['EmailQueue']['id']), array('escape'=>false, 'confirm'=>'¿Está seguro que quiere volver a enviar este email?'))?></li>
                    </ul>
                <?php endif?>
                <?php if($e['EmailQueue']['locked']):?>
                    <ul class="list-inline">
                        <li><?php echo $this->Html->link('<i class="glyphicon glyphicon-ok"></i> Desbloquear', array('action'=>'unlock/'.$e['EmailQueue']['id']), array('escape'=>false, 'confirm'=>'¿Está seguro que quiere desbloquear este email?'))?></li>
                    </ul>
                <?php endif?>
                <?php if($e['EmailQueue']['send_tries'] > 3):?>
                    <ul class="list-inline">
                        <li><?php echo $this->Html->link('<i class="glyphicon glyphicon-refresh"></i> Resetear', array('action'=>'reset/'.$e['EmailQueue']['id']), array('escape'=>false, 'confirm'=>'¿Está seguro que quiere resetear este email?'))?></li>
                    </ul>
                <?php endif?>
            </td>
            <td><?php echo $e['EmailQueue']['lang']?></td>
            <td><?php echo $e['EmailQueue']['to_inbox']?></td>
            <td><?php echo $e['EmailQueue']['subject']?></td>
            <td><?php echo $e['EmailQueue']['sent']?></td>
            <td><?php echo $e['EmailQueue']['locked']?></td>
            <td><?php echo $e['EmailQueue']['send_tries']?></td>
            <td><?php echo $e['EmailQueue']['send_at']?></td>
            <td><?php echo $e['EmailQueue']['template']?></td>
            <td><?php echo json_encode($e['EmailQueue']['template_vars'])?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>