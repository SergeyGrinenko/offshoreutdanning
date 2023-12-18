<?php
/* Template Name: Contact Us */
get_header();
$heading = get_field('heading');
$subheading = get_field('subheading');
?>
<div class="page-contentform-wrapper">

    <div id="page-lead-row">
        <div class="page-lead">
            <div class="page-lead-text-wrapper">
                <div class="page-lead-text">
                    <h1><?php echo $heading; ?></h1>
                    <p><?php echo $subheading; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="px-2">
        <div class="page-contactus-form">
            <?php the_content(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
