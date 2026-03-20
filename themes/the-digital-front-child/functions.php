<?php

/**
 * The Digital Front Child Theme functions and definitions.
 */

function tdf_child_enqueue_styles()
{
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'tdf_child_enqueue_styles');

// Register Opinion Custom Post Type
function tdf_register_opinion_cpt()
{
    register_post_type('opinion', array(
        'labels' => array(
            'name'          => 'Opinions',
            'singular_name' => 'Opinion',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'opinion'),
        'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'tdf_register_opinion_cpt');

// Create About Us pages on theme activation
function tdf_create_about_pages()
{
    if (!get_page_by_path('about-us')) {
        $about_id = wp_insert_post(array(
            'post_title'  => 'About Us',
            'post_name'   => 'about-us',
            'post_status' => 'publish',
            'post_type'   => 'page',
        ));

        if ($about_id) {
            wp_insert_post(array(
                'post_title'  => 'Team',
                'post_name'   => 'team',
                'post_status' => 'publish',
                'post_type'   => 'page',
                'post_parent' => $about_id,
            ));

            wp_insert_post(array(
                'post_title'  => 'Mission',
                'post_name'   => 'mission',
                'post_status' => 'publish',
                'post_type'   => 'page',
                'post_parent' => $about_id,
            ));
        }
    }
}
add_action('init', 'tdf_create_about_pages');
