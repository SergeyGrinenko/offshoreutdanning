<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-1068195124"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'G-ZDH4G0HPXP');
        gtag('config', 'AW-1068195124');
    </script>

    <script>(function (w, d, t, r, u) {
            var f, n, i;
            w[u] = w[u] || [], f = function () {
                var o = {ti: "134613201"};
                o.q = w[u], w[u] = new UET(o), w[u].push("pageLoad")
            }, n = d.createElement(t), n.src = r, n.async = 1, n.onload = n.onreadystatechange = function () {
                var s = this.readyState;
                s && s !== "loaded" && s !== "complete" || (f(), n.onload = n.onreadystatechange = null)
            }, i = d.getElementsByTagName(t)[0], i.parentNode.insertBefore(n, i)
        })(window, document, "script", "//bat.bing.com/bat.js", "uetq");</script>

    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2.0">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <title><?php the_title(); ?></title>

    <?php wp_head(); ?>

</head>

<?php
$cart_item = '';

if (is_product() && WC()->cart->get_cart_contents_count() > 0) {
    $cart_item = 'cart_item';
}
?>

<body <?php body_class($cart_item); ?> >

<header class="page-header bg-light-1 position-relative <?= (is_product() && WC()->cart->get_cart_contents_count() == 0) ? 'dynamic' : ''; ?>">
    <div class="container-fluid">
        <div class="d-flex justify-between">


            <div class="logo-wrapper">
                <a href="<?= get_site_url() ?>">
                    <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/logo-offshoreutdanning.svg"
                         class="site-logo" alt="site-logo" width="267" height="44">
                </a>
            </div>

            <?php wp_nav_menu(
                array(
                    'theme_location' => 'header-menu',
                    'container_class' => 'main-navigation fw-normal',
                    'add_li_class' => 'btn justify-center',
                    'link_before' => '<span class="button-text btn rounded7 justify-center">',
                    'link_after' => '</span>',
                    'walker' => new Offutd_Menu_Walker()
                )
            ); ?>


            <div class="d-flex header-right">
                <a href="<?= wc_get_checkout_url(); ?>"
                   class="mini-subtotal <?= (WC()->cart->get_cart_contents_count() === 0 ? 'hidden' : '') ?>">
                    <div class="mini-cart-wrapper bg-pink trigger-cart btn rounded7">
                        <div class="d-flex align-center">
                            <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/icon-cart.svg"
                                 alt="mini-cart"
                                 width="20">

                            <span class="mini-items-data text-white"><?= count(WC()->cart->get_cart_contents()); ?> <?php _e('kurs'); ?>, <?= formatted_price(WC()->cart->cart_contents_total); ?></span>
                        </div>
                    </div>
                </a>

                <div class="toggle-menu btn justify-center rounded7 bg-white">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="modal-cart">
                <div class="modal-content mini-cart-container">
                    <h2 class="text-center fw-normal m-0 text-light bg-dark-2"><?php _e('Du har lagt til følgende i handlekurven:'); ?></h2>
                    <div class="cart-content">
                        <?php get_template_part('template-parts/mini-cart'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile-menu bg-washedblue">
        <div class="container-fluid">
            <?php wp_nav_menu(
                array(
                    'theme_location' => 'dropdown-menu',
                    'container_class' => 'dropdown-menu-navigation p-3',
                    'add_li_class' => '',
                )
            ); ?>
        </div>
    </div>
</header>