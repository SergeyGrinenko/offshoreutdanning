</div>

<footer class="page-footer bg-light-1">

    <div class="container p-0">
        <div class="page-footer__wrapper px-3 d-flex justify-between">
            <?php if (get_option("company_name") && get_option("company_address")): ?>
                <div class="contact-info">
                    <p class="m-0 fw-normal"><?= get_option("company_name"); ?></p>
                    <p class="my-3 fw-normal"><?= get_option("company_address"); ?></p>
                    <p class="m-0 fw-normal">Organisasjonsnummer:<br>811836982</p>
                </div>
            <?php endif; ?>

            <div class="social-info">
                <div class="d-flex align-start">
                    <img src="<?= get_stylesheet_directory_uri() ?>/assets/images/letter.svg" width="33" alt="letter">
                    <a href="mailto:hjelp@offshoreutdanning.no" class="text-decoration text-dark d-block fw-normal">Send oss en e-post</a>
                </div>
                <div class="d-flex align-start">
                    <img src="<?= get_stylesheet_directory_uri() ?>/assets/images/messenger.svg" width="33" alt="messenger">
                    <a href="https://m.me/145642328789029" target="_blank" class="text-decoration text-dark d-block fw-normal">Snakk med oss på messenger</a>
                </div>
                <div class="d-flex align-start">
                    <img src="<?= get_stylesheet_directory_uri() ?>/assets/images/instagram.svg" width="33" alt="instagram">
                    <a href="https://instagram.com/offshoreutdanning.no" target="_blank" class="text-decoration text-dark d-block fw-normal">Snakk med oss på instagram</a>
                </div>
                <div class="d-flex align-start">
                    <img src="<?= get_stylesheet_directory_uri() ?>/assets/images/phone.svg" width="33" alt="phone">
                    <a href="tel:40002390" class="text-dark d-block fw-normal">40002390<br>Mandag til fredag 08:00 - 16:00</a>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid p-1 bg-softdark">
        <?php wp_nav_menu(
            array(
                'theme_location' => 'footer-menu',
                'container_class' => 'main-navigation fw-normal',
                'menu_class' => 'justify-center text-white',
                'add_li_class' => 'btn justify-center',
                'link_before' => '<span></span><span class="button-text text-decoration">',
                'link_after' => '</span>'
            )
        ); ?>
    </div>

</footer>

<?php wp_footer(); ?>
