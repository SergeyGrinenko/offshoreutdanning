<?php
get_header();

if (have_posts()) :

   the_content();

else :

    get_template_part('content', 'none');

endif;

get_footer();
