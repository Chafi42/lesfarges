<?php

// Pouvoir ajouter des avis pour un gites/chambre d'hote en particulier
// Pouvoir ajouter du texte et une notation sur 5 "étoiles"

// Etape 1 : Créer un post type pour Wordpress

function notice_custom_post_type()
{
    register_post_type(
        'wporg_avis',
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

// <----------------------------------------->

function wporg_add_custom_box()
{
    // Meta box notation
    add_meta_box(
        'wporg_box_id',                 // Unique ID
        "Avis",      // Box title
        'wporg_custom_box_html',  // Content callback, must be of type callable
        'wporg_avis'                            // Post type
    );
    // Meta box choix gite/chambre d'hote
    add_meta_box(
        'wporg_page_select_id',
        'Sélection de Gîte/Chambre d\'hôte',
        'render_page_select',
        'wporg_avis'
    );
}
add_action('add_meta_boxes', 'wporg_add_custom_box');

// <----------------------------------------->

function wporg_custom_box_html($post)
{
    $value = get_post_meta($post->ID, 'review_note', true);
?>
    <!-- <label for="wporg_field">Note de l'avis</label> -->
    <input type="hidden" name="wporg_field" class="wporg_field" value="<?= $value ?>">
    <?php for ($i = 0; $i < 5; $i++) : ?>
        <?php $class = $i + 1 <= $value ? 'solid' : 'regular'; ?>
        <span data-star="<?= $i + 1  ?>" class="fa-xl fa-star wporg_avis-star fa-<?= $class ?>"></span>
    <?php endfor; ?>
<?php
}

// <----------------------------------------->

function render_page_select($post)
{
    // Récupérer toutes les pages (get_pages())

    $pages = get_pages();
    $selected_page = get_post_meta($post->ID, 'selected_page', true);
?>
    <label for="wporg_page_select">Sélectionner un gîte/chambre d'hôte :</label>
    <!-- Créer un boite de selection -->
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

// <----------------------------------------->

function wporg_save_postdata($post_id)
{
    // Sauvegarde de la note de l'avis
    if (array_key_exists('wporg_field', $_POST)) {
        update_post_meta(
            $post_id,
            'review_note',
            $_POST['wporg_field']
        );
    }
    // Sauvegarde du choix de gite/chambre d'hote
    if (array_key_exists('wporg_page_select', $_POST)) {
        update_post_meta(
            $post_id,
            'selected_page',
            $_POST['wporg_page_select']
        );
    }
}
add_action('save_post', 'wporg_save_postdata');

// <----------------------------------------->

function wporg_avis_columns($columns)
{
    $new_columns = array(
        'review_note' => 'Note',
        'selected_page' => 'Gîte/Chambre d\'hôte',
    );
    // On récupère les valeurs du tableau "avant" notre position
    $before = array_slice($columns, 0, 2);
    // On récupère les valeurs du tableau "après" notre position
    $after  = array_diff_key($columns, $before);
    // On ré-assemble les colonnes
    $columns  = array_merge($before, $new_columns, $after);
    return $columns;
}
add_filter('manage_wporg_avis_posts_columns', 'wporg_avis_columns');

// <----------------------------------------->

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
add_action('manage_wporg_avis_posts_custom_column', 'wporg_avis_custom_columns', 99, 2);

function manage_wporg_avis_sortable_columns($columns)
{
    $columns['selected_page'] = 'selected_page';
    $columns['review_date'] = 'review_date';
    return $columns;
}
add_filter('manage_edit-wporg_avis_sortable_columns', 'manage_wporg_avis_sortable_columns');

function wporg_avis_orderby($query)
{
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');

    // Handle sorting by 'selected_page'
    if ('selected_page' == $orderby) {
        // Assume 'selected_page' is a meta key, and we want to sort by its value
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
        ),
        $atts,
        'review'
    );

    ob_start();
    // include 'review.php';
    get_template_part('review', null, $atts);
    return ob_get_clean();
}

// <----------------------------------------->


include_once get_stylesheet_directory() . '/includes/comment-ajax.php';
// require LFG_INCLUDES . 'comment-ajax.php';

?>