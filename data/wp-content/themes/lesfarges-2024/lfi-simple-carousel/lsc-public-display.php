<!-- lsc-public-display.php -->
<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    lfi-simple-carousel
 * @subpackage lfi-simple-carousel/public/partials
 */
?>
<?php
$slider_id = $args['id'];
$db_pref = '_lsc_';
$interval = get_post_meta($slider_id, $db_pref . 'interval', true);
$interval = $interval == 0 ? 'false' : $interval;
$slides = get_post_meta($slider_id, $db_pref . 'slides', true);
$height = get_post_meta($slider_id, $db_pref . 'img_height', true);
$height = empty($height) ? '500' : $height;
$contain = get_post_meta($slider_id, $db_pref . 'same_height', true);
$contain = !empty($contain) ? "bg-contain" : "bg-cover";
$nav = get_post_meta($slider_id, $db_pref . 'nav', true);
$nav = !empty($nav) ? true : false;
?>
<div id="slider-<?= $slider_id ?>" class="carousel slide" data-bs-ride="true" data-bs-interval="<?= $interval ?>" data-bs-pause="false">
    <div class="carousel-inner" style="height:<?= $height ?>px">
        <?php foreach ($slides as $key => $slide) : ?>
            <?php
            $active = $key === 0 ? 'active' : '';
            $color = isset($slide['color']) ? $slide['color'] : 'black';
            $img = isset($slide['img']) ? $slide['img'] : '';
            $text = isset($slide['text']) ? $slide['text'] : '';
            $subtitle = isset($slide['subtitle']) ? $slide['subtitle'] : '';
            $project = isset($slide['project']) && is_array($slide['project']) ? intval($slide['project'][0]) : 0;
            $link = get_permalink($project);                               ?>
            <div class="carousel-item position-relative h-100 <?= $active ?>" data-color="<?= $color ?>" data-count="<?= $key ?>">
                <div class="bg <?= $contain ?> h-100 w-100" style="background-image: url(<?= $img ?>);"></div>
                <?php if ($text) : ?>
                    <div class="position-absolute start-0 top-0 w-100 h-100 bg-dark bg-opacity-50"></div>
                    <div class="position-absolute bottom-0 start-0 ps-4 pb-3 text-white">
                        <div class="title fw-bold">
                            <?= $text ?>
                        </div>
                        <div class="sub-title fw-light">
                            <?= $subtitle ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>

    <?php if ($nav) : ?>
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < count($slides); $i++) : ?>
                <button type="button" data-bs-target="#slider-<?= $slider_id ?>" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="true" aria-label="Slide <?= $i + 1 ?>"></button>
            <?php endfor ?>
        </div>
    <?php endif ?>
</div>