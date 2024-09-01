<?php
// Récupérer les avis au nombre indiqué en paramètre

if (have_posts()) {
    while (have_posts()) {
        the_post();

        $post_id = get_the_ID();

        // Récupérer la note de l'avis
        $review_note = get_post_meta($post_id, 'review_note', true);

        // Récupérer le gîte/chambre d'hôte sélectionné
        $selected_page_id = get_post_meta($post_id, 'selected_page', true);
        $selected_page_title = get_the_title($selected_page_id);
    ?>

        <div class="avis-details">
            <h2>Avis :</h2>
            <div class="review-note">
                <?php
                $stars = '';
                for ($i = 0; $i < 5; $i++) {
                    $class = $i + 1 <= $review_note ? 'solid' : 'regular';
                    $stars .= '<span class="fa-lg fa-star wporg_avis-star fa-' . $class . '"></span>';
                }
                echo $stars;
                ?>
            </div>

            <h2>Gîte/Chambre d'hôte sélectionné :</h2>
            <div class="selected-gite">
                <?php echo esc_html($selected_page_title); ?>
            </div>
        </div>

<?php
    }
}