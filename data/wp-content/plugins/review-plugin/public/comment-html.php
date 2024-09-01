<?php // Ce fichier/code html nous permet de n'avoir qu'un seul endroit pour le design des commentaires 
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