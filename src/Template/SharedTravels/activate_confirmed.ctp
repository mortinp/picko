<?php use App\Model\Entity\SharedTravel;?>

<?php
$modalityCode = $request['SharedTravel']['modality_code'];
$modality = SharedTravel::$modalities[$modalityCode];
?>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-1">
            <p class="lead"><?php echo __d('shared_travels', 'Muchas gracias {0}', $request['SharedTravel']['name_id'])?>!</p> 
            <p class="lead">
                <?php echo __d('shared_travels', 'Buenas noticias')?>! 
                <?php echo __d('shared_travels', 'Su solicitud fue confirmada automáticamente porque {0}.', $confirmed_reason)?>
            </p> 
            <p><?php echo __d('shared_travels', 'Estos son todos los datos de tu solicitud')?>:</p>
        </div>
        
        <div class="col-md-8 offset-md-1">
            <hr/>
            <?php echo $this->element('shared_travel', compact('request'))?>
        </div>
        
        <div class="col-md-3 alert alert-secondary" style="display: inline-block">
            <?php echo $this->element('suggest_transfers', compact('modality'))?>
        </div>        
    </div>
</div>