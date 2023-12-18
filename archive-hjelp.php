<?php get_header(); ?>
    <div class="archive-help-page">
        <div class="bg-light course-title-wrapper">
            <div class="container py-0 position-relative">
                <div class="grid-6-4 align-center help-header">
                    <div>
                        <h1>
                            <?php echo post_type_archive_title(); ?>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="help-posts container">
            <?php $help_terms = get_terms("hjelpcat");
            if (!empty($help_terms) && !is_wp_error($help_terms)) {
                foreach ($help_terms as $term) {

                    $posts = get_posts(array(
                        'post_type' => 'hjelp',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'hjelpcat',
                                'field' => 'slug',
                                'terms' => $term->slug
                            )
                        )
                    ));
                    echo '<h2 class="m-0 py-3">' . $term->name . "</h2>";
                    echo '<div class="term-items">';


                    foreach ($posts as $post) { ?>
                        <a href="<?php the_permalink(); ?>" class="d-flex align-unset justify-between text-dark bg-light">
                            <div class="p-1 item__info"><?php the_title(); ?></div>
                            <div class="item__arrow bg-primary d-flex">
                                <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/icon-right.svg"
                                     alt="icon-right">
                            </div>
                        </a>
                        <?php
                    }

                    echo '</div>';

                }
            } ?>
        </div>
    </div>

<?php get_footer(); ?>