<?php
/* Template Name: Blank side */
get_header();
$heading = get_field('heading');
$subheading = get_field('subheading');
?>
    <div class="page-content-wrapper blank-side-tpl">

        <div id="page-lead-row">
            <div class="page-lead">
                <div class="page-lead-text-wrapper">
                    <div class="page-lead-text my-3">
                        <h1 class="m-0"><?php echo $heading; ?></h1>
                        <?php if ($subheading): ?>
                            <p class="m-0"><?php echo $subheading; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-blank">
            <?php the_content(); ?>
        </div>
    </div>
    </div>

<?php get_footer(); ?>