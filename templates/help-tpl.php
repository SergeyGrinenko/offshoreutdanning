<?php
/* Template Name: Help Page Template */

get_header();
?>

    <div class="archive-help-page">
        <div class="bg-softdark course-title-wrapper">
            <div class="container py-0 position-relative">
                <div class="help-header">
                    <img src="<?php the_post_thumbnail_url(get_the_ID(), 'full'); ?>" width="100%" class="d-block">
                </div>
            </div>
        </div>
        <div class="container p-0">
            <div class="px-3 inner-page-subheader">
                <h2 class="m-0 fw-normal text-center">Vanlige spørsmål</h2>
                <div class="profesional-courses">

                    <?php $categories = get_terms(array(
                        'taxonomy' => 'works_cat',
                        'hide_empty' => true,
                    ));;

                    foreach ($categories as $category) { ?>
                        <?php $caption = get_field('caption', 'works_cat_' . $category->term_id); ?>
                        <div class="course-item">
                            <a href="<?= get_term_link($category->term_id); ?>"
                               class="course-item__link btn bg-pink text-white rounded7 justify-center m-0">
                                <?= $category->name; ?>
                            </a>
                            <?php if ($caption): ?>
                                <p class="mt-1 m-0 text-center"><?= $caption; ?></p>
                            <?php endif; ?>
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="help-posts container-fluid bg-white">
            <div class="container">
                <?php $help_terms = get_terms("hjelpcat");
                if (!empty($help_terms) && !is_wp_error($help_terms)) { ?>
                    <ul id="accordion" class="accordion main m-0">
                        <?php foreach ($help_terms as $term) {

                            $posts = get_posts(array(
                                'post_type' => 'hjelp',
                                'posts_per_page' => -1,
                                'orderby' => 'date',
                                'order' => 'ASC',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'hjelpcat',
                                        'field' => 'slug',
                                        'terms' => $term->slug
                                    )
                                )
                            ));

                            echo '<li class="bg-light-1" data-slug="' . $term->slug . '">';
                            echo '<div class="category-name d-flex align-center justify-between">' . $term->name . '<img src="' . get_stylesheet_directory_uri() . '/assets/images/icon-plus.svg" alt="circle-arrow" width="26.67">';
                            echo '</div>';

                            echo '<div class="accordion-content m-0">';
                            foreach ($posts as $post) { ?>
                                <a href="<?php the_permalink(); ?>"
                                   class="d-flex align-unset justify-between text-dark">
                                    <div class="my-1 item__info text-decoration"><?php the_title(); ?></div>
                                </a>
                                <?php
                            }
                            echo '</div>';
                            echo '</li>';
                        } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>

<?php get_footer(); ?>