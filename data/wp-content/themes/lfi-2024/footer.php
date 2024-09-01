<span class="go-up"></span>
<footer class="stick-to-bottom container-fluid p-0">
    <?php
    $footer_post_id = get_option('footer_page', 0);
    if ($footer_post_id) {
        $post = get_post($footer_post_id, OBJECT);
        setup_postdata($post);
        the_content();
        wp_reset_postdata();
    }
    wp_footer();
    ?>
</footer>
</body>

</html>