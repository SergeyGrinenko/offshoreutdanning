<?php
function block_row( $block ){
    $classes = '';
    if( !empty( $block['className'] ) ) {
        $classes .= sprintf( ' %s', $block['className'] );
    }
    if( !empty( $block['align'] ) ) {
        $classes .= sprintf( ' align%s', $block['align'] );
    }

    $cols = get_field('cols');
    if( empty( $cols ) ) {
        $cols = 1;
    }

    for( $x = 0; $x < $cols; $x++ ) {
        $template[] = array( 'acf/column' );
    }
    ?>
    <div class="row-block row <?php echo esc_attr($classes); ?>">
        <h1>Row</h1>
        <?php
        echo '<InnerBlocks template="' . esc_attr( wp_json_encode( $template ) ) . '" templateLock="all"/>';
        ?>
    </div>
    <?php
}
