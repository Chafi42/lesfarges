jQuery(document).ready(function ($) {
    let commentCount = $("div.comment").length;
    // Dans notre script js, qui s'occupe de la requete ajax, il va falloir savoir si on le "post_id" à été ajouté dans le code court
    // ce qui va modifier la requete en bdd pour n'obtenir que les commentaires désirés.
    // La méthode jQuery "attr()"" permet de récupérer la valeur d'un attribut HTML
    let selectedPage = $("#reviews").attr("data-selected-page");
    console.log(selectedPage);
    $("#more-review").click("button", function (e) {
        e.preventDefault();
        let $button = $(this).find("button");
        $button.prop("disabled", true);
        $.post(
            ajaxComment.ajax_url,
            // Ici c'est l'objet/array que l'on envoie au script php
            // On a le droit de rajouter autant de "clé -> valeur" que l'on veut
            {
                action: "ajax_comment",
                nonce: ajaxComment.nonce,
                comment_count: commentCount,
                selected_page: selectedPage,
            },
            function (response) {
                $button.prop("disabled", false);
                console.log(response);
                if (response.success) {
                    if (response.data.html) {
                        commentCount += parseInt(response.data.post_per_page);
                        $(response.data.html).insertBefore("#more-review");
                    }
                    // Ici on teste cette valeur de mor_comment pour savoir si on doit cacher le bouton
                    if (response.data.more_comment === false) {
                        $button.css("display", "none");
                    }
                } else {
                    console.log("Erreur lors du chargement des commentaires");
                }
            }
        ).fail(function () {
            $button.prop("disabled", false);
            console.log("Erreur lors de la requête AJAX");
        });
    });
});
