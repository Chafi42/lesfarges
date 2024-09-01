<?php
$container_id = 'header-menu';
$container_class = array(
    'collapse',
    'navbar-collapse',
    'position-fixed',
    'top-0',
    'end-0',
    'has-violet-background-color',
    "col-12 ",
    "col-md-6 ",
    "col-lg-4 "
);
$menu_class = array(
    'text-uppercase',
    'w-auto',
    'mx-auto',
    'list-unstyled',
    'pt-5',
    'align-self-start',
    'col-auto',
    'mb-5',
    'align-self-end',
);
?>
<nav class="navbar fixed-top bg-white" data-margin="1">
    <div class="container-fluid h-100 position-relative">
        <a class="navbar-brand align-self-end flex-grow-1 logo" href="/">
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            ?>
            <?php if (LFI_Helper()->is_setting_enabled('general', 'logo')) : ?>
                <div class="custom-logo col-auto ">
                    <img src="<?= esc_url($logo[0]) ?>" class="logo-header" alt="<?= get_bloginfo('name') ?>">
                </div>
            <?php endif ?>
            <?php if (display_header_text()) : ?>
                <div class="bloginfo d-md-block d-none has-violet-color font-montserrat text-uppercase">
                    <?php if (LFI_Helper()->is_setting_enabled('general', 'site-title')) : ?>
                        <span class="title d-block"><?= get_bloginfo('name') ?></span>
                    <?php endif ?>
                    <?php if (LFI_Helper()->is_setting_enabled('general', 'tagline')) : ?>
                        <span class="tagline fw-bold"><?= get_bloginfo('description') ?></span>
                        <span class="tagline-2nd fw-light"><?= get_option('lfa_slogan_2') ?></span>
                    <?php endif ?>
                    <hr class="line-info has-violet-color">
                </div>
            <?php endif ?>
        </a>
        <?php
        $burger_args = array(
            'target_id' => '#menu-lesfarges',
            'button_class' => array(
                'navbar-toggler',
                'collapsed',
                'border-0',
                'p-0',
                'bg-transparent',
                'me-2',
                'me-lg-4',
            ),
            'span_class' => 'has-violet-background-color'
        );
        theme_menu_toggler($burger_args);
        ?>
        <div id="menu-lesfarges" class="collapse navbar-collapse position-fixed top-0 end-0 m-0 row has-violet-background-color col-12 col-md-6 col-lg-4">
            <div class="vh-100 text-white m-0 row">
                <?php
                // Menu principal
                $args = array(
                    'container'            => '',
                    'container_class'      => '',
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
                    'item_class'           => 'pt-2 pb-3',
                    'item_spacing'         => 'preserve',
                    'depth'                => 0,
                    'walker'               => new Bootstrap_Walker_Nav_Menu(),
                    'theme_location'       => 'primary-menu',
                );
                if (has_nav_menu('primary-menu')) {
                    wp_nav_menu($args);
                }

                ?>

                <!-- // Image entre les menus -->
                <div class="text-center align-self-start">
                    <img src="<?= esc_url($logo[0]) ?>" alt="<?= get_bloginfo('name') ?>" class="logo-menu">
                </div>

                <?php
                // Menu secondaire
                if (has_nav_menu('secondary')) {
                    $argsSecondary = array(
                        'theme_location'       => 'secondary',
                        'container'            => '',
                        'container_class'      => '',
                        'menu_id'              => '',
                        'menu_class'           => 'row justify-content-between align-self-end mb-2 mx-0 list-unstyled',
                        'items_wrap'           => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'item_class'           => 'col-auto px-0',
                        'walker'               => new Bootstrap_Walker_Nav_Menu(),
                    );
                    wp_nav_menu($argsSecondary);
                }
                ?>
            </div>
        </div>
    </div>
</nav>