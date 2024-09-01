<?php
$templateVar = get_query_var('templateVar');
$menu = $templateVar['WPMenu'];
$tab = $templateVar['tab'];
?>
<div class="wrap">
    <h2><?php echo $menu->menu_options['page_title']; ?></h2>
    <?php if (!empty($menu->menu_options['desc'])) : ?>
        <p class='description'>
            <?php echo $menu->menu_options['desc']; ?>
        </p>
    <?php endif; ?>
    <?php $menu->render_tabs($tab); ?>

    <form method="POST" action="">
        <input type="hidden" name="tab" value="<?= $tab ?>">
        <div class="postbox">
            <div class="inside">
                <table class="form-table">
                    <?php $menu->render_rows($tab); ?>
                </table>
                <button type="submit" name="<?php echo $menu->menu_slug; ?>_save" class="button button-primary">
                    <?php _e('Save', 'textdomain'); ?>
                </button>
            </div>
        </div>
    </form>
</div>