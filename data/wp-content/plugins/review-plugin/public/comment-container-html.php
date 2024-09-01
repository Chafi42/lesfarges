<?php
// Maintenant vous allez me trouver le moyen de limiter les résultat de la BDD
// en fonction du "post_id", c'est à dire que l'on ne veut afficher que les commentaires
// ayant ce "post_id" en meta_key "selected_page" 

// 2. Vous faites une requête WP_Query qui va chercher les commentaires en fonction du "post_id" (Malin ce copilot ;) )
// 3. Vous affichez les commentaires
$posts_per_page = isset($atts['num']) ? $atts['num'] : 5;
// $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : false;
$selected_page = isset($atts['selected_page']) ? $atts['selected_page'] : 0;

// echo '<pre>';
// var_dump($selected_page);
// echo '</pre>';

// Nombre total de commentaires, sans distinction.
$totalComment = absint(wp_count_posts(PLUGIN_CPT_NAME)->publish);

// Il faut me faire un test du selected_page, et si false ilo ne faut pas faire la requête avec le vhamps personalisé
// Requete en base de donnée au chargement de la page.
// Ok on a problème avec notre requete du faite de "false"
$queryArgs = array(
    'post_type' => PLUGIN_CPT_NAME,
    'posts_per_page' => $posts_per_page,
    // 'meta_key' => 'selected_page',
    // 'meta_value' => $selected_page,
);
// Donc on va tester $selected_page et si n'est pas "false" alors on modifiera notre array d'argument
// 0 equivaut en false dans le test php
if ($selected_page) {
    $queryArgs['meta_query'] = array(
        array(
            'key' => 'selected_page',
            'value' => $selected_page,
        )
    );

    // Requete pour savoir le nombre total de commentaire en fonction de selected_page
    $selectedPageQueryArgs = $queryArgs;
    $selectedPageQueryArgs['meta_query'] = array(
        array(
            'key' => 'selected_page',
            'value' => $selected_page,
        )
    );
    $selectedPageQuery = new WP_Query($selectedPageQueryArgs);
    $totalComment = $selectedPageQuery->found_posts;
}
$query = new WP_Query($queryArgs);
// Pour passer l'information au script on peut utiliser les attribut d'élément html.
// l'attribut on le nomme comme on veut, ici "data-selected-page" et on lui donne la valeur de $selected_page
// Bien-sûr certains attribut sont réservé  tel quel id, class, rel, src, href, style, title, alt, width, height, ...
?>
<div id="reviews" class="container-fluid px-5" data-selected-page="<?= $selected_page ?>">
    <div class="row justify-content-center">
        <?php
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include PLUGIN_DIR_PATH . 'public/comment-html.php';
            }
            wp_reset_postdata();
        }
        // On test si le nombre de commentaire est supérieur au nombre de commentaire affiché
        // Si c'est le cas on affiche le bouton "Voir les avis"
        if ($totalComment > $query->post_count) : ?>
            <div id="more-review" class="col-12 text-center">
                <button class="btn rounded-0 has-violet-background-color text-white pt-2" style="width: auto;">Voir les avis</button>
            </div>
        <?php endif; ?>
    </div>
</div>