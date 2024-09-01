<?php
add_action('wp_enqueue_scripts', 'ajax_comment_enqueue');
function ajax_comment_enqueue()
{
    wp_enqueue_script(
        'ajax-comment',
        get_stylesheet_directory_uri() . '/assets/js/comment-ajax.js',
        array('jquery'),
        '1.0.0',
        true
    );
    wp_localize_script(
        'ajax-comment',
        'ajaxComment',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-comment-nonce')
        )
    );
}

add_action('wp_ajax_nopriv_ajax_comment', 'ajax_comment');
add_action('wp_ajax_ajax_comment', 'ajax_comment');
function ajax_comment()
{
    check_ajax_referer('ajax-comment-nonce', '_ajax_nonce');

    // Récupérer le nombre de commentaires déjà affichés envoyé par la requête JS
    $displayed_comments = isset($_POST['comment_count']) ? absint($_POST['comment_count']) : 0;

    // Modifier notre requête pour obtenir un décalage en fonction du nb de commentaire déjà affiché
    $queryArgs = array(
        'post_type' => 'wporg_avis',
        'posts_per_page' => 3,
        'offset' => $displayed_comments, // Ajouter le décalage ici
    );
    $query = new WP_Query($queryArgs);
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post(); ?>
            <div class="col-md-4 comment">
                <div class="card mb-4 border-0 d-flex justify-content-center align-items-center text-start">
                    <div class="card-body">
                        <p class=" fw-bold text-secondary"><?php the_title(); ?></p>
                        <div class="stars-rating gap-1">
                            <?php
                            $stars = '';
                            $review_note = get_post_meta(get_the_ID(), 'review_note', true);
                            for ($i = 0; $i < 5; $i++) {
                                $class = $i + 1 <= $review_note ? 'solid' : 'regular';
                                $stars .= '<span class="gap-1 fs-6 text-warning fa-lg fa-star wporg_avis-star fa-' . $class . '"></span>';
                            }
                            echo $stars;
                            ?>
                            <small class="ps-3"><?= get_the_date(); ?></small>
                        </div>
                        <p class="mt-4"><?php the_content(); ?></p>
                    </div>
                </div>
            </div>
<?php
        }
        wp_reset_postdata();
    }
    $response = ob_get_clean();
    wp_send_json_success($response);
}
