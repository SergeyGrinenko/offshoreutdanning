<?php
function get_post_breadcrumb($post_id, $taxonomy)
{
    $terms = get_the_terms($post_id, $taxonomy);
    $term = array_pop($terms);
    echo '<a href="' . home_url() . '" class="text-decoration text-dark" rel="nofollow">Forsiden</a>';
    if (is_category() || is_single()) {
        echo "&nbsp;&nbsp;>&nbsp;&nbsp;";

        if ($taxonomy === 'hjelpcat') {

            echo '<a href="' . get_the_permalink(get_page_by_title(get_post_type($post_id))) . '" class="text-decoration text-dark" rel="nofollow">' . ucfirst(get_post_type($post_id)) . '</a>';
        } else {
            echo '<a href="' . get_term_link($term->term_id, $taxonomy) . '" class="text-decoration text-dark" rel="nofollow">' . $term->name . '</a>';
        }
        if (is_single()) {
            echo " &nbsp;&nbsp;>&nbsp;&nbsp; ";
            the_title();
        }

    }
}

add_action('init', 'worksection'); // create custom post type WorkSection
function worksection()
{
    $labels = array(
        'name' => 'WorkSection',
        'singular_name' => 'WorkSection',
        'menu_name' => 'WorkSection'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'taxonomies' => array('works_cat'),
        'rewrite' => array('slug' => 'tema/%works_cat%', 'with_front' => false),
        'capability_type' => 'post',
        'has_archive' => 'true',
        'hierarchical' => true,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-book'
    );
    register_post_type('worksection', $args);

    $labels = array(
        'name' => 'Categories',
        'singular_name' => 'Category',
    );

    $args2 = array(
        'hierarchical' => true,
        'rewrite' => array('slug' => 'tema', 'with_front' => true),
        'show_in_nav_menus' => true,
        'labels' => $labels,
        'show_in_rest' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
    );

    register_taxonomy('works_cat', 'worksection', $args2);
}

function wpa_show_permalinks($post_link, $post)
{
    if (is_object($post) && $post->post_type == 'worksection') {
        $terms = wp_get_object_terms($post->ID, 'works_cat');
        if ($terms) {
            return str_replace('%works_cat%', $terms[0]->slug, $post_link);
        }
    }
    return $post_link;
}

add_filter('post_type_link', 'wpa_show_permalinks', 1, 2);


function wpd_locations_may_be_topics($request)
{
    if (isset($request['name'])) {
        if ($request['name'] === 'tema') {
            $request['post_type'] = 'worksection';
        }
    }

    return $request;
}

add_filter('request', 'wpd_locations_may_be_topics');

function custom_post_type_request_filter($query)
{
    if (!is_admin()) {
        if ($query->query['name'] && $query->query['post_type']) {
            if ($query->query['name'] === 'tema' && $query->query['post_type'] === 'worksection') {
                foreach (wp_get_active_and_valid_themes() as $theme) {
                    $query->query_vars['post_type'] = 'worksection';
                    $query->query_vars['is_single'] = false;
                    $query->query_vars['is_singular'] = false;
                    $query->query_vars['is_archive'] = true;
                    $query->is_single = false;
                    $query->is_singular = false;
                    $query->is_archive = true;
                    $query->is_post_type_archive = true;
                }
            }
        }

    }
}

add_action('pre_get_posts', 'custom_post_type_request_filter');

add_action('init', 'help_cpt'); // create custom post type Hjelp
function help_cpt()
{
    $labels = array(
        'name' => 'Hjelp',
        'singular_name' => 'Hjelp',
        'menu_name' => 'Hjelp'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'taxonomies' => array('hjelpcat'),
        'rewrite' => array('slug' => 'hjelp/%hjelpcat%', 'with_front' => false),
        'capability_type' => 'post',
        'has_archive' => 'false',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-book'
    );
    register_post_type('hjelp', $args);

    $labels = array(
        'name' => 'Category',
        'singular_name' => 'Category',
    );

    $args = array(
        'hierarchical' => true,
        'rewrite' => array('slug' => 'hjelp'),
        'show_in_nav_menus' => true,
        'labels' => $labels
    );

    register_taxonomy('hjelpcat', 'hjelp', $args);

    unset($labels);
    unset($args);
}

add_action('init', 'create_tag_taxonomies', 0); //create taxonomy tag
function create_tag_taxonomies()
{
    $labels = array(
        'name' => _x('Tags', 'taxonomy general name'),
        'singular_name' => _x('Tag', 'taxonomy singular name'),
        'all_items' => __('All Tags'),
        'menu_name' => __('Tags'),
    );

    register_taxonomy('tags', 'hjelp', array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array('slug' => 'hjelp_tags'),
    ));
}


add_filter('post_type_link', 'projectcategory_permalink_structure', 10, 4); // set projectcategory permalink structure
function projectcategory_permalink_structure($post_link, $post, $leavename, $sample)
{
    if (false !== strpos($post_link, '%hjelpcat%')) {
        $projectscategory_type_term = get_the_terms($post->ID, 'hjelpcat');
        if (!empty($projectscategory_type_term))
            $post_link = str_replace('%hjelpcat%', array_pop($projectscategory_type_term)->
            slug, $post_link);
        else
            $post_link = str_replace('%hjelpcat%', 'uncategorized', $post_link);
    }
    return $post_link;
}

function do_shortcode_in_gut($block_content, $block)
{
    return do_shortcode($block_content);
}

add_filter('render_block', 'do_shortcode_in_gut', 99, 2);

add_action('init', 'landing_pages'); // create custom post type WorkSection
function landing_pages()
{
    $labels = array(
        'name' => 'Landing Pages',
        'singular_name' => 'Landing Page',
        'menu_name' => 'Landing Pages'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'capability_type' => 'post',
        'has_archive' => 'false',
        'hierarchical' => true,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-book'
    );
    register_post_type('landing', $args);
}