<?php
// Fichiers obligatoires au bon fonctionnement du thème, ordre important.
require_once get_template_directory() . '/includes/lfi-functions.php';
require_once get_template_directory() . '/includes/class-lfi-helper.php';

if (!function_exists('LFI_Helper')) {
    /**
     * Retourne une instance de LFI_Helper
     *
     * @since  1.0.0
     * @return LFI_Helper
     */
    function LFI_Helper() { 
        return LFI_Helper::getInstance(array('status' => 'dev'));
    }
}
// Lancement de la classe maître du thème
require_once LFI_Helper()->get_abs_path('includes/class-lfi.php');
$lfi = LFI::getInstance();
// $lfi->run();