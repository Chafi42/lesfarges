<?php
// Récupérer les avis au nombre indiqué en paramètre
// Afficher uniquement le nombre indiqué en paramètre
// WP_Query avec en argument wporg_avis et le nombre de post a afficher
// Les arguments passé par la fonction get_template_part() sont dans le tableau $args.
$posts_per_page = isset($args['num']) ? $args['num'] : 5;
$queryArgs = array(
    'post_type' => 'wporg_avis',
    'posts_per_page' => $posts_per_page,
);
$query = new WP_Query($queryArgs); ?>
<div id="reviews" class="container-fluid px-5">
    <div class="row justify-content-center">
        <?php
        $counter = 0;
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $counter++;
        ?>
                <div class="col-md-4 comment">
                    <div class="card mb-4 border-0 d-flex justify-content-center align-items-center text-start">
                        <div class="card-body">
                            <p class=" text-secondary fw-bold "><?php the_title(); ?></p>
                            <div class=" stars-rating">
                                <?php
                                $stars = '';
                                $review_note = get_post_meta(get_the_ID(), 'review_note', true);
                                ?>
                                <?php
                                for ($i = 0; $i < 5; $i++) {
                                    $class = $i + 1 <= $review_note ? 'solid' : 'regular';
                                    $stars .= '<span class=" gap-1 fs-6 text-warning fa-lg fa-star wporg_avis-star fa-' . $class . '"></span>';
                                }
                                ?>

                                <?php
                                echo $stars;
                                ?>
                                <small class="ps-3"><?= get_the_date('M Y'); ?></small>
                            </div>
                            <p class="mt-4"><?php the_content(); ?></p>

                        </div>
                    </div>
                </div>
        <?php
            }
            wp_reset_postdata();
        }
        ?>
    </div>
    <div class="row justify-content-center">
        <button id="more-review" class="btn rounded-0 has-violet-background-color text-white pt-2" style="width: auto;">Voir les avis</button>
    </div>
</div>