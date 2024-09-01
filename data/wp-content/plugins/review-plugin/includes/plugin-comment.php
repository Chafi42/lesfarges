<?php

function notice_custom_post_type()
{
    register_post_type(
        PLUGIN_CPT_NAME,
        array(
            'labels'      => array(
                'name'          => __('Avis', 'textdomain'),
                'singular_name' => __('avis', 'textdomain'),
            ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array('slug' => 'avis'),
            'supports'    => array(
                'title',
                'editor',
                // 'author',
                // 'thumbnail',
                // 'excerpt',
                // 'comments',
            ),
            'menu_icon'   => 'dashicons-format-status',
        ),
    );
}
add_action('init', 'notice_custom_post_type');

function custom_review_admin_acripts($hook)
{
    global $post_type;
    if (
        $post_type == PLUGIN_CPT_NAME
        // && $hook == 'post.php' && $hook == 'post-new.php'
    ) {
        wp_enqueue_style(
            PLUGIN_CPT_NAME . '-css',
            PLUGIN_DIR_URL . 'assets/css/admin-custom-review.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            PLUGIN_CPT_NAME . '-js',
            PLUGIN_DIR_URL . 'assets/js/admin-custom-review.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'custom_review_admin_acripts');


// <----------------------------------------->

function wporg_add_custom_box()
{
    // Meta box notation
    add_meta_box(
        'wporg_box_id',
        "Avis",
        'wporg_custom_box_html',
        PLUGIN_CPT_NAME
    );
    // Meta box choix gite/chambre d'hote
    add_meta_box(
        'wporg_page_select_id',
        'Sélection de Gîte/Chambre d\'hôte',
        'render_page_select',
        PLUGIN_CPT_NAME
    );
}
add_action('add_meta_boxes', 'wporg_add_custom_box');

// <----------------------------------------->

function wporg_custom_box_html($post)
{
    $value = get_post_meta($post->ID, 'review_note', true);
?>

    <input type="hidden" name="wporg_field" class="wporg_field" value="<?= $value ?>">
    <?php for ($i = 0; $i < 5; $i++) : ?>
        <?php $class = $i + 1 <= $value ? 'solid' : 'regular'; ?>
        <span data-star="<?= $i + 1  ?>" class="fa-xl fa-star wporg_avis-star fa-<?= $class ?>"></span>
    <?php endfor; ?>
<?php
}

function render_page_select($post)
{

    $pages = get_pages();
    $selected_page = get_post_meta($post->ID, 'selected_page', true);
?>
    <label for="wporg_page_select">Sélectionner un gîte/chambre d'hôte :</label>

    <select name="wporg_page_select" id="wporg_page_select">
        <?php foreach ($pages as $page) : ?>

            <!-- <option value="id-page">Titre de la page</option> -->
            <option value="<?= $page->ID; ?>" <?= $selected_page == $page->ID ? 'selected' : ''; ?> name="<?= $page->post_title; ?>">
                <?= $page->post_title; ?>
            </option>
        <?php endforeach; ?>
    </select>
<?php
}

function wporg_save_postdata($post_id)
{

    if (array_key_exists('wporg_field', $_POST)) {
        update_post_meta(
            $post_id,
            'review_note',
            $_POST['wporg_field']
        );
    }

    if (array_key_exists('wporg_page_select', $_POST)) {
        update_post_meta(
            $post_id,
            'selected_page',
            $_POST['wporg_page_select']
        );
    }
}
add_action('save_post', 'wporg_save_postdata');

function wporg_avis_columns($columns)
{
    $new_columns = array(
        'review_note' => 'Note',
        'selected_page' => 'Gîte/Chambre d\'hôte',
    );

    $before = array_slice($columns, 0, 2);
    $after  = array_diff_key($columns, $before);
    $columns  = array_merge($before, $new_columns, $after);
    return $columns;
}
add_filter('manage_custom-review_posts_columns', 'wporg_avis_columns');

function wporg_avis_custom_columns($column, $post_id)
{
    if ($column == 'review_note') {
        $note = get_post_meta($post_id, 'review_note', true);
        $stars = '';
        for ($i = 0; $i < 5; $i++) {
            $class = $i + 1 <= $note ? 'solid' : 'regular';
            $stars .= '<span class="fa-lg fa-star wporg_avis-star fa-' . $class . '"></span>';
        }
        echo $stars;
    }
    if ($column == 'selected_page') {
        $selected_page = get_post_meta($post_id, 'selected_page', true);
        echo esc_html(get_the_title($selected_page));
    }
}
add_action('manage_custom-review_posts_custom_column', 'wporg_avis_custom_columns', 99, 2);

function manage_custom_review_sortable_columns($columns)
{
    $columns['selected_page'] = 'selected_page';
    $columns['review_date'] = 'review_date';
    return $columns;
}
add_filter('manage_edit-custom-review_sortable_columns', 'manage_custom_review_sortable_columns');

function wporg_avis_orderby($query)
{
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ('selected_page' == $orderby) {
        $query->set('meta_key', 'selected_page');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'wporg_avis_orderby');


add_action('init', 'review_shortcode');
function review_shortcode()
{
    add_shortcode('review', 'review_shortcode_callback');
}

function review_shortcode_callback($atts)
{
    $atts = shortcode_atts(
        array(
            'num' => 5,
            'selected_page' => 0
        ),
        $atts,
        'review'
    );

    ob_start();
    include PLUGIN_DIR_PATH . 'public/comment-container-html.php';
    // get_template_part('review', null, $atts);
    return ob_get_clean();
}



// include_once get_stylesheet_directory() . '/includes/comment-ajax.php';
// require LFG_INCLUDES . 'comment-ajax.php';
require PLUGIN_DIR_PATH . 'includes/plugin-comment-ajax.php';

?>