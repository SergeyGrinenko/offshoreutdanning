<?php get_header(); ?>
<?php $obj = get_post_type_object(get_post_type()); ?>
<div class="single-post-page">
    <div class="bg-light course-title-wrapper">
        <div class="container py-0 position-relative">
            <div class="grid-6-4 align-center help-header">
                <div>
                    <a href="/hjelp/om-a-jobbe-offshore/yrkesguide/"
                       class="d-flex align-unset justify-between text-dark bg-white text-center my-3">
                        <div class="item__arrow bg-primary d-flex">
                            <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/icon-left.svg"
                                 alt="icon-left">
                        </div>
                        <div class="p-1 item__info text-center w-100"><?= __('Tilbake til'); ?>&nbsp;<?= $obj->labels->name; ?></div>
                    </a>

                    <div class="py-1 bg-primary text-light text-center help-header__TypeName"><?= $obj->labels->name; ?></div>
                    <h1><?php echo get_the_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <?php if (have_posts()) :
            while (have_posts()) :
                the_post();
                echo do_shortcode(get_the_content());
            endwhile;
        endif; ?>
    </div>
</div>

<?php get_footer(); ?>
