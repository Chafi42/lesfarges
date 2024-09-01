<?php
extract(get_query_var('field'));
$value = get_query_var('value');
$logo_id = get_theme_mod('custom_logo');
$img_src = $logo_id
    ? wp_get_attachment_image_src($logo_id, array(75, 75))
    : LFI_Helper()->get_default_image('', '75x75');
?>
<?php if ($desc) : ?>
    <p class="description"><?= $desc ?></p>
<?php endif; ?>
<div class="container-fluid p-4" id="nav-background">
    <div class="row">
        <div class="col-auto text-nowrap">
            <input type="radio" name="<?= $name  ?>" id="" value="1" <?php checked($value, '1') ?>>
            <div class="container-fluid">
                <div class="row align-items-center justify-content-center">
                    <div class="col">
                        <img src="<?= $img_src ?>" class="img-fluid" alt="">
                    </div>
                    <div class="col">
                        <p class="site-title font-weight-bold"><?php bloginfo('name') ?></p>
                        <p class="site-description small font-italic has-jaune-color"><?php bloginfo('description') ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto text-nowrap">
            <input type="radio" name="<?= $name  ?>" id="" value="2" <?php checked($value, '2') ?>>
            <div class="container-fluid">
                <div class="row align-items-center justify-content-center">
                    <div class="col-12 text-center">
                        <img src="<?= $img_src ?>" class="img-fluid" alt="">
                    </div>
                    <div class="col-12 text-center">
                        <p class="site-title font-weight-bold"><?php bloginfo('name') ?></p>
                        <p class="site-description small font-italic has-jaune-color"><?php bloginfo('description') ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto text-nowrap">
            <input type="radio" name="<?= $name  ?>" id="" value="3" <?php checked($value, '3') ?>>
            <div class="container-fluid">
                <div class="row align-items-center justify-content-center">
                    <div class="col text-center">
                        <img src="<?= $img_src ?>" class="img-fluid" alt="">
                    </div>
                    <div class="col">
                        <p class="site-title font-weight-bold"><?php bloginfo('name') ?></p>
                    </div>
                    <div class="col-12 text-center">
                        <p class="site-description small font-italic has-jaune-color"><?php bloginfo('description') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>