<?php
/* Template Name: Professions Guide */

get_header(); ?>

<div id="page-lead-row">
    <div class="page-lead">
        <div class="page-lead-text-wrapper">
            <div class="page-lead-text">
                <h1>
                    <?php $custom_title = get_field('custom_course_title');
                    if (!empty($custom_title)) {
                        echo $custom_title;
                    } else {
                        the_title();
                    } ?>
                </h1>
                <p><?php echo get_the_excerpt(); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="professions-guide-content">
    <div class="categories-filter">
        <div class="p-3 bg-darkgrey">

            <?php
            while (have_posts()) :
                the_post();

                $parent_terms = get_categories(array(
                    'taxonomy' => 'category',
                    'type' => 'post',
                    'child_of' => 0,
                    'parent' => 0,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => 1,
                    'hierarchical' => 1,
                    'number' => 0,
                    'pad_counts' => false,
                ));

                if ($parent_terms) {
                    foreach ($parent_terms as $parent_term) {
                        echo '<h3 class="white">' . $parent_term->name . '</h3>';

                        $parent_term_id = $parent_term->cat_ID;

                        $args = array(
                            'taxonomy' => 'category',
                            'type' => 'post',
                            'child_of' => $parent_term_id,
                            'hide_empty' => false,
                        );

                        $child_terms = get_terms($args);

                        $post_args = array(
                            'numberposts' => -1,
                            'post_type' => 'post',
                        );
                        $posts = get_posts($post_args);
                        $coutn_posts = array();
                        foreach ($posts as $post) {
                            array_push($coutn_posts, $post->ID);
                        }

                        echo '<div class="filterInputs">';
                        echo '<div class="d-flex mb-1">';
                        echo '<div class="bg-ligthgrey d-flex">';
                        echo '<div class="radio_btn active"></div>';
                        echo '</div>';
                        echo '<label class="bg-ligthgrey w-100"><input type="radio" name="' . $parent_term->slug . '" value="alls"><i></i>All</label>';
                        echo '<div class="count_posts">';
                        echo '<div class="bg-blue white counter" data-val="' . count($coutn_posts) . '">' . count($coutn_posts) . '</div>';
                        echo '</div>';
                        echo '</div>';

                        foreach ($child_terms as $child_term) {

                            $category = get_category($child_term->term_id);
                            $count = $category->category_count;

                            echo '<div class="d-flex mb-1"><div class="bg-ligthgrey d-flex"><div class="radio_btn"></div></div><label class="bg-ligthgrey w-100"><input type="radio" name="' . $parent_term->slug . '" value="' . $child_term->slug . '"><i></i>' . $child_term->name . '</label><div class="count_posts"><div class="bg-blue white counter" data-val="' . $count . '">' . $count . '</div></div></div>';
                        }
                        echo '</div>';
                    }
                }

            endwhile; ?>
        </div>
    </div>

    <div class="all-posts">

        <div class="page-title">
            <div>
                <h2 class="mt-0">Yrkesguide</h2>
            </div>
            <div class="text-right">
                <input class="page-searchbox live-search-box" type="text" placeholder="Søk i alle kurs"
                       id="search-input-ajax">
            </div>
        </div>

        <div class="info-box">
            <div class="">
                <p class="white bg-blue p-3">
                    Les om hvilke typer yrker som finnes offshore og hva som kreves av utdanning, erfaring og kurs
                    for å kvalifisere til dem. Du kan bruke menyen til venstre for å filtrere listen.
                </p>
            </div>
        </div>
        <div class="search_item">
            <?php
            $args = array(
                'numberposts' => -1,
                'post_type' => 'post',
                'order' => 'ASC',
            );
            $posts = get_posts($args);

            foreach ($posts as $post) {
                $post_cat = wp_get_post_categories($post->ID, array('fields' => 'all'));
                $post_cat_array = array();

                foreach ($post_cat as $item) {
                    array_push($post_cat_array, $item->slug);
                } ?>

                <div class="archive_product_item bg-ligthgrey mb-1" data-name="<?php the_title(); ?>"
                     data-filterable="alls <?php echo implode(" ", $post_cat_array); ?>">
                    <a href="<?php the_permalink(); ?>">
                        <div class="d-flex justify-content-between">

                            <p class="m-0 p-2 pl-3"><?php the_title(); ?></p>

                            <div class="bg-blue archive_product_arrow">
                            </div>

                        </div>

                    </a>
                </div>

            <?php } ?>
        </div>

        <div class="p-2 bg-red white not_available no-items">
            Ingen treff på ditt søk.
        </div>

    </div>

</div>


<?php get_footer(); ?>
