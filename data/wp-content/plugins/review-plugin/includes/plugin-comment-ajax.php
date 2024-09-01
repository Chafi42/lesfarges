<?php

add_action('wp_enqueue_scripts', 'ajax_comment_enqueue');
function ajax_comment_enqueue()
{
    $handle_js = 'ajax-comment';
    wp_enqueue_script(
        $handle_js,
        PLUGIN_DIR_URL . 'assets/js/comment-ajax.js', // Ensure correct URL path
        array('jquery'),
        '1.0.0',
        true
    );
    // Ici cette fonction comme on l'a vu tout au début permet d'envoyer un objet 
    // au script JS, en donnat la "handle" enregistrer avec wp_enqueu_script
    wp_localize_script(
        $handle_js,
        'ajaxComment', // ici le nom de l'objet, que l'on pourra utiliser dans le script
        array(  // Ici l'objet lui même, ici un tableau, mais ça pourrait être n'importe quoi
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
        )
    );
}

add_action('wp_ajax_nopriv_ajax_comment', 'ajax_comment');
add_action('wp_ajax_ajax_comment', 'ajax_comment');
// Ici c'est la fonction exécutée lorsque l'on fait notre requete ajax (click sur le bouton)
function ajax_comment()
{
    check_ajax_referer('ajax-nonce', 'nonce');
    // Nombre total de commentaires
    $totalComment = absint(wp_count_posts(PLUGIN_CPT_NAME)->publish);
    // Ici on définie combien de posts la requete ajax va aller chercher
    $posts_per_page = 3;
    $displayed_comments = isset($_POST['comment_count']) ? absint($_POST['comment_count']) : 0;
    // On récupère la valeur envoyée par le script js
    $selected_page = isset($_POST['selected_page']) ? absint($_POST['selected_page']) : 0;


    $queryArgs = array(
        'post_type' => PLUGIN_CPT_NAME,
        'posts_per_page' => $posts_per_page,
        'offset' => $displayed_comments,
    );
    // Si différent de zéro
    if ($selected_page) {
        // Comme pour la requete du code court on teste si la valeur est différente de 0
        // on va chercher les commentaires en fonction de la valeur de "selected_page"
        $queryArgs['meta_query'] = array(
            array(
                'key' => 'selected_page',
                'value' => $selected_page,
            )
        );

        // Alors on va faire une requete WP_Query pour simplement compter les ocmmentaires
        // Mais uniquement lorsque $selected_page est actif.
        $slectedPageQueryArgs = array(
            'post_type' => PLUGIN_CPT_NAME,
            'meta_query' => array(
                array(
                    'key' => 'selected_page',
                    'value' => $selected_page,
                )
            )
        );
        $slectedPageQuery = new WP_Query($slectedPageQueryArgs);
        // Du coup on change le nombre total qui sert de comparatif
        $totalComment = $slectedPageQuery->post_count;
    }
    // Grâce à cette class "WP_Query" on execute notre requete au serveur de base de donnée (mysql ou mariadb)
    // Avec les arguments on choisit ce que l'on demande, ici les commentaires.
    // Maintenant que nous avons rajouté un apramètre dans code-court (shortcode) on va devoir essayé de le récupérer
    // ce paramètre est "post_id".
    $query = new WP_Query($queryArgs);
    ob_start();
    if ($query->have_posts()) {
        // Ici nous allons obtenir de nombre commentaire que nous renvoi WP_Query
        $postCount = $query->post_count;
        while ($query->have_posts()) {
            $query->the_post();
            include PLUGIN_DIR_PATH . 'public/comment-html.php';
        }
        wp_reset_postdata();
    }
    // Ici on a le code html des commentaires dasn la variable $response.
    $response = ob_get_clean();
    // Là la réponse est un tableau, dans lequel on intègre notr code html dans la clé "html"
    // et le nombre de commentaires renvoyé, valuere déclaré en début de fonction.
    // ALors pour être parfait de chez parfait, il va arriver un moment ou l'on en renverra pas le nombre exacte $post_per_page.
    // Si lors de notre requete WP_Qeury on obtiens plus que 2 commentaire, on n'aura pas renvoyé 3 commentaires mais seulement 2
    $res_array = array(
        'html' => $response,
        // On regarde si notre compteur de post (WP_Query) est plus petit que le nombre déclaré si oui c'est lui que l'on renvoi, ok?
        'post_per_page' => $postCount < $posts_per_page ? $postCount : $posts_per_page,
        // L'idée est de transmettre avant la demande si il reste ou non des commentaires à afficher.
        'more_comment' => $totalComment > $displayed_comments + $postCount ? true : false,
    );
    // Cette fonction comme on l'avait vu au début envoie notre réponse au script js 
    // et s'occupe de formatter correctement la variable pour js.
    wp_send_json_success($res_array);
}
