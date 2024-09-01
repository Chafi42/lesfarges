<?php
$bodyID = isset($args['body-id']) ? $args['body-id'] : 'lfi-loader';
$bodyClass = isset($args['body-class']) ? $args['body-class'] : 'loader-body lfi-loader';
$loaderClass = isset($args['loader-class']) ? $args['loader-class'] : 'loader';
?>
<!-- LOADER -->
<div class="<?= $bodyClass ?>" id="<?= $bodyID ?>">
    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center justify-content-center">
            <div class="<?= $loaderClass ?>"></div>
        </div>
    </div>
</div>
<!-- LOADER END -->