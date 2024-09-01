<?php

/**
 * The template for displaying 404 pages (Not Found)
 */
get_header();

$page_404_id = get_option('page_404', 0);
if ($page_404_id) {
    $post = get_post($page_404_id, OBJECT);
    setup_postdata($post);
    the_content();
    wp_reset_postdata();
} else {
?>
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">

            <header class="page-header">
                <h1 class="page-title"><?php _e('Not Found', LFI_Helper()->domain()); ?></h1>
            </header>

            <div class="page-wrapper">
                <div class="page-content">
                    <h2><?php _e('This is somewhat embarrassing, isn’t it?', LFI_Helper()->domain()); ?></h2>
                    <p><?php _e('It looks like nothing was found at this location. Maybe try a search?', LFI_Helper()->domain()); ?></p>

                    <?php get_search_form(); ?>
                </div><!-- .page-content -->
            </div><!-- .page-wrapper -->

        </div><!-- #content -->
    </div><!-- #primary -->
<?php
}
get_sidebar();
get_footer();
