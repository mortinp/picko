<?php use App\Model\Entity\SharedTravel;?>

<p>Hola Andiel, tenemos una solicitud que completa un viaje de 4 pax.</p>

<p><b>NOTA: Este viaje ya fue confirmado al cliente.</b></p>

<p>A continuación los detalles de la solicitud:</p>

<div><?php echo $this->element('shared_travel_facilitator', compact('request'))?></div>