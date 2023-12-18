<?php $pageID = $args['page_id'] ?>
<div id="submited">
    <h1 class="text-center">Bekreftelse på rådgivning</h1>
    <?php if (have_rows('contact_form_success', $pageID)):
        while (have_rows('contact_form_success', $pageID)) : the_row();
            $editor = get_sub_field('editor', $pageID);
            if ($editor):
                echo '<div class="card">';
                echo $editor;
                echo '</div>';
            endif;
        endwhile;
    endif; ?>
</div>