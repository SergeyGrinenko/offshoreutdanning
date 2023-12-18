<?php
/* Template Name: Promotion */
get_header();
?>
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

    <div class="promotion-section pt-5 pb-5">

            <?php
            while (have_posts()) : the_post();

                the_content();

            endwhile; ?>
       
    </div>

<?php get_footer(); ?>