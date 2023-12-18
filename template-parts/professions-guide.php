<div class="professions-guide-content">
    <div class="grid-4-6">

        <?php
        $PostID = $args['post_id'];
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
                echo '<div class="categories-filter">';
                echo '<div class="bg-softdark p-3">';

                foreach ($parent_terms as $parent_term) {
                    echo '<h3 class="my-3 text-light">' . $parent_term->name . '</h3>';

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

                    echo '<div class="filter_inputs">';
                    echo '<div class="filter_inputs__item d-flex align-center">';
                    echo '<label>' . __('Alle') . '<input type="radio" name="' . $parent_term->slug . '" value="alls" checked><span class="checkmark"></span><div class="count bg-primary text-light" data-count="' . count($coutn_posts) . '">' . count($coutn_posts) . '</div></label>';
                    echo '</div>';

                    foreach ($child_terms as $child_term) {

                        $category = get_category($child_term->term_id);
                        $count = $category->category_count;

                        echo '<div class="filter_inputs__item d-flex align-center">';
                        echo '<label>' . $child_term->name . '<input type="radio" name="' . $parent_term->slug . '" value="' . $child_term->slug . '"><span class="checkmark"></span><div class="count bg-primary text-light" data-count="' . $count . '">' . $count . '</div></label>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
            }

        endwhile; ?>
        <div>
            <h2 class="mt-0 mb-3 fw-normal"><?= get_the_title($PostID); ?></h2>

            <div class="search-course-bar d-flex">
                <input type="text" class="search-course-bar__input w-100" placeholder="Søk i alle kurs">
            </div>

            <div class="info-box bg-primary text-light p-3 my-3">
                Les om hvilke typer yrker som finnes offshore og hva som kreves av utdanning, erfaring og kurs for å
                kvalifisere til dem. Du kan bruke menyen til venstre for å filtrere listen.
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

                    <div class="post_item bg-ligthgrey" data-name="<?php the_title(); ?>"
                         data-filterable="alls <?php echo implode(" ", $post_cat_array); ?>">
                        <a href="<?php the_permalink(); ?>">
                            <div class="d-flex justify-between w-100 bg-light">

                                <p class="m-0 text-dark px-3"><?php the_title(); ?></p>

                                <div class="item__arrow bg-softdark d-flex justify-center">
                                    <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/icon-right.svg"
                                         alt="icon-right">
                                </div>

                            </div>

                        </a>
                    </div>

                <?php } ?>
            </div>

        </div>
    </div>
</div>
