<?php
$posttypes = array(
    'post' => 'Articles',
    'page' => 'Pages'
);
$getPostTypes = get_post_types(
    array(
        '_builtin' => false
    ),
    'objects'
);
foreach ($getPostTypes as $key => $post) {
    $posttypes[$post->name] = $post->label;
}
extract(get_query_var('field'));
$languages = icl_get_languages('skip_missing=0');
$default_lang = apply_filters('wpml_default_language', NULL);
?>
<?php get_loader('mini') ?>
<?php wp_nonce_field($action) ?>
<table class="table duplicate-post-table">
    <thead>
        <tr>
            <th class="fit"><input type="checkbox" value="all" class="check-all"></th>
            <th>Type publication</th>
            <?php foreach ($languages as $lang) : ?>
                <th><?= $lang['native_name'] ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($posttypes as $type => $name) : ?>
            <tr data-type="<?= $type ?>">
                <td class="fit"><input type="checkbox" value="<?= $type ?>"></td>
                <td><?= $name ?></td>
                <?php foreach ($languages as $lang) : ?>
                    <?php
                    // change language
                    do_action('wpml_switch_language', $lang['code']);
                    // query
                    $query = new WP_Query(array(
                        'post_type' => $type,
                        'posts_per_page' => -1,
                        'post_status' => 'any',
                        'suppress_filters' => false
                    ));
                    ?>
                    <td data-lang="<?= $lang['code'] ?>">
                        <?= $query->post_count ?>
                    </td>
                    <?php wp_reset_postdata() ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<span class="duplicate-post btn-link d-inline-block pointer py-4">Dupliquer !</span>
<span class="message ml-4 text-success d-none"></span>
<span class="message ml-4 text-danger d-none"></span>
<!-- Revert to default language -->
<?php  do_action('wpml_switch_language', $default_lang); ?>