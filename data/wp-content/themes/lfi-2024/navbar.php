<?php
$container_id = 'header-menu';
$container_class = array(
    'collapse',
    'navbar-collapse',
    'position-fixed',
    'position-md-relative',
    'top-0',
    'start-0',
    'w-100',
    'w-md-auto',
);
$menu_class = array(
    'navbar-nav',
    'vh-100',
    'h-md-auto',
    'bg-white',
    'bg-md-transparent',
    'align-items-center',
    'justify-content-center',
    'ms-auto',
);
?>
<nav class="navbar navbar-expand-md fixed-top bg-white" data-margin="1">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-4" href="/">
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            ?>
            <?php if (LFI_Helper()->is_setting_enabled('general', 'logo')) : ?>
                <div class="custom-logo">
                    <img src="<?= esc_url($logo[0]) ?>" alt="<?= get_bloginfo('name') ?>">
                </div>
            <?php endif ?>
            <?php if (display_header_text()) : ?>
                <div class="bloginfo">
                    <?php if (LFI_Helper()->is_setting_enabled('general', 'site-title')) : ?>
                        <span class="title d-block"><?= get_bloginfo('name') ?></span>
                    <?php endif ?>
                    <?php if (LFI_Helper()->is_setting_enabled('general', 'tagline')) : ?>
                        <span class="tagline d-block"><?= get_bloginfo('description') ?></span>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </a>
        <?php theme_menu_toggler(array('target_id' => '#' . $container_id)) ?>
        <?php $args = array(
            'menu'                 => '',
            'container'            => 'div',
            'container_class'      => implode(' ', $container_class),
            'container_id'         => $container_id,
            'container_aria_label' => '',
            'menu_class'           => implode(' ', $menu_class),
            'menu_id'              => '',
            'echo'                 => true,
            'fallback_cb'          => 'wp_page_menu',
            'before'               => '',
            'after'                => '',
            'link_before'          => '',
            'link_after'           => '',
            'items_wrap'           => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'item_spacing'         => 'preserve',
            'depth'                => 0,
            'walker'               => new Bootstrap_Walker_Nav_Menu(),
            'theme_location'       => 'primary-menu',
        );
        if (has_nav_menu('primary-menu')) {
            wp_nav_menu($args);
        }
        ?>
    </div>
</nav>