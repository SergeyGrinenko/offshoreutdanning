<?php get_header(); ?>
<?php $subtitle = get_field('subtitle'); ?>
<?php $obj = get_post_type_object(get_post_type()); ?>
    <div class="landing-page">
        <div class="container-fluid page-heading bg-light-2 text-center py-0">
            <h2><?php the_title(); ?></h2>
            <?php if ($subtitle): ?>
                <h3><?= $subtitle; ?></h3>
            <?php endif; ?>


            <div class="row">
                <div class="bg-softdark w-100">
                    <div class="container-lg p-0">
                        <div class="landing-bg"
                             style="background-image: url(<?= wp_get_attachment_image_src(get_post_thumbnail_id(), 'full')[0] ?>)"></div>
                    </div>
                </div>
            </div>


        </div>

        <div class="course-content">
            <?php the_content(); ?>
        </div>

    </div>

<?php get_footer(); ?>