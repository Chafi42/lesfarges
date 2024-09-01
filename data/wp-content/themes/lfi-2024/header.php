<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<?php 
$bodyClass = 'overflow-hidden ';
$bodyClass .= isset($args['body-class']) ? $args['body-class'] : '';
?>
<body <?php body_class($bodyClass); ?>>
    <?php $args = wp_parse_args($args, array()) ?>
    <?php get_template_part('loader', null, $args); ?>
    <?php get_template_part('navbar', null, $args); ?>