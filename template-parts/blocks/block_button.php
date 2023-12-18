<?php
/**
 * Button Block Template.
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param array $context The context provided to the block by the post or it's parent block.
 */


$text = get_field('btn_name') ?: '';
$link = get_field('btn_link');
$background_color = get_field('btn_color');
$text_color = get_field('btn_text_color');
$btn_width = get_field('btn_width');
$btn_position = get_field('btn_position');
$style = ' ';

$class_name = 'offutd_button';
$btn_class = 'btn justify-center rounded7';
if (!empty($block['className'])) {
    $class_name .= ' ' . $block['className'];
}

if (!empty($background_color)) {
    $btn_class .= ' ' . $background_color;
}

if (!empty($text_color)) {
    $btn_class .= ' ' . $text_color;
}

if ($btn_width) {
    $style .= 'max-width:' . $btn_width . 'px; ';
}
if ($btn_position) {
    $style .= 'align-self:' . $btn_position.'; ';
}


?>
<div class="<?php echo esc_attr($class_name); ?>" style="<?= $style; ?>">
    <a href="<?= $link; ?>" class="<?php echo esc_attr($btn_class); ?>"><?= $text; ?></a>
</div>