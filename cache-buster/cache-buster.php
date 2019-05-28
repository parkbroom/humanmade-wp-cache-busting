<?php
/**
Plugin Name: Cache Buster
Description: A simple cache busting demo for humanmade.com
Version: 1.0
Author: Daniel Li
License: GPLv2 or later
*/

defined( 'ABSPATH' ) || exit;

add_filter( 'style_loader_src', 'remove_ver_css_js', PHP_INT_MAX - 1 );
add_filter( 'script_loader_src', 'remove_ver_css_js', PHP_INT_MAX - 1 );

function remove_ver_css_js( $src ) {
    if ( ! $src ) return;

    if ( strpos( $src, 'ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }

    // No scheme in url
    if ( empty( parse_url( $src, PHP_URL_SCHEME ) ) ) {
        $src = $_SERVER['REQUEST_SCHEME'] . ':' . $src;
    }

    // This is a case when assets are loaded from external URL -- we don't have to add version control
    // For example: http://maps.google.com/maps/api/js?libraries=places
    if ( false === strpos( $src, home_url() ) ) {
        return $src;
    }

    $filename = realpath( $_SERVER['DOCUMENT_ROOT'] ) . parse_url( $src )['path'];

    // Make sure there is no remaining "https:" in the $filename anymore
    // This should resolve the issue with the wrong path such as: /ebs/projects/wp.statlerhttps:/wp/wp-includes/js/jquery/jquery.js
    $filename = str_replace( 'https:', '', $filename );

    // Check if the file exists first
    if ( file_exists( $filename ) ) {
        $version = filemtime( $filename );
        $src = $src . '?ver=' . $version;
    }

    return $src;
}

