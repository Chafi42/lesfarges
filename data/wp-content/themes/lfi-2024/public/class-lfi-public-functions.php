<?php

if (!function_exists('theme_menu_toggler')) {
    /**
     * Menu burger style for Bootstrap
     *
     * @param array $args Optionnal classes for the elements
     * @return string
     **/
    function theme_menu_toggler($args = array(), $sizes = array())
    {
        $defaults = array(
            'target_id' => '',
            'button_class' => array(
                'navbar-toggler',
                'collapsed',
                'border-0',
                'p-0',
                'bg-transparent',
                'me-4',
            ),
            'span_class' => 'bg-dark',
        );
        $args = wp_parse_args($args, $defaults);
        extract($args);
        $button_class = is_array($button_class) ? implode(' ', $button_class) : $button_class;

        $defaults_sizes = array(
            'width'     => 40,
            'height'    => 20,
            'thickness' => 2,
        );
        $sizes = wp_parse_args($sizes, $defaults_sizes);
        $span_4_top = $sizes['height'] - $sizes['thickness'];
        $span_2_3_top = $sizes['height'] / 2 - $sizes['thickness'] / 2;

?>
        <style>
            .burger-menu {
                width: <?= $sizes['width'] ?>px;
                height: <?= $sizes['height'] ?>px;
            }
            .burger-menu span {
                height: <?= $sizes['thickness'] ?>px;
            }
            .burger-menu span:nth-child(1){
                top: 0;
            }
            .burger-menu span:nth-child(2),
            .burger-menu span:nth-child(3) {
                top: <?= $span_2_3_top ?>px;
            }

            .burger-menu span:nth-child(4) {
                top: <?= $span_4_top ?>px;
            }
        </style>
        <button class="<?= $button_class ?>" type="button" data-bs-toggle="collapse" data-bs-target="<?= $target_id ?>">
            <div class="burger-menu">
                <span class="<?= $span_class ?>"></span>
                <span class="<?= $span_class ?>"></span>
                <span class="<?= $span_class ?>"></span>
                <span class="<?= $span_class ?>"></span>
            </div>
        </button>
<?php
    }
}
