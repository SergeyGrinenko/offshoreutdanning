<?php
get_header(); ?>
<?php if (!is_cart() && !is_checkout()): ?>
    <div class="bg-light course-title-wrapper">
        <div class="container py-3 position-relative">
            <div class="grid-6-4 py-3 align-center help-header">
                <div>
                    <h1><?= get_the_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <div class="container">
        <?php
        while (have_posts()) :
            the_post();


            the_content();

            /**
             * Functions hooked in to storefront_page_after action
             *
             * @hooked storefront_display_comments - 10
             */


        endwhile; // End of the loop. ?>
    </div>
<?php get_footer();