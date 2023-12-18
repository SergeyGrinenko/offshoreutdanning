<?php
add_action('acf/init', 'acf_blocks');
function acf_blocks()
{

    if (function_exists('acf_register_block_type')) {
        acf_register_block_type(array(
            'name' => 'grouped_courses',
            'title' => __('Grouped Courses'),
            'render_template' => 'template-parts/blocks/grouped-courses.php',
            'mode' => 'edit',
            'enqueue_assets' => function () {
                wp_enqueue_style('grouped_courses', get_stylesheet_directory_uri() . '/assets/scss/blocks/grouped_courses.css');
                wp_enqueue_script('grouped_courses', get_template_directory_uri() . '/assets/js/blocks.js', array('jquery'), '', true);
            }
        ));


        acf_register_block_type(array(
            'name' => 'row',
            'title' => 'Row',
            'description' => 'A row content block.',
            'category' => 'formatting',
            'mode' => 'preview',
            'supports' => array(
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'mode' => false,
                'jsx' => true,
                'color' => array(
                    'background' => true,
                    'text' => false
                )
            ),
            'render_callback' => 'block_row',
            'enqueue_assets' => function () {
                wp_enqueue_style('grouped_courses', get_stylesheet_directory_uri() . '/assets/scss/blocks/grouped_courses.css', '', '', '',);
            }


        ));

        acf_register_block_type(array(
            'name' => 'column',
            'title' => 'Column',
            'description' => 'A column content block.',
            'category' => 'formatting',
            'mode' => 'preview',
            'supports' => array(
                'align' => false,
                'anchor' => true,
                'customClassName' => true,
                'jsx' => true,
            ),
            'render_callback' => 'block_col',
        ));

        acf_register_block_type(array(
            'name' => 'offutd-button',
            'category' => 'formatting',
            'title' => __('Button'),
            'render_template' => 'template-parts/blocks/block_button.php',
            'mode' => 'preview',
            'enqueue_assets' => function () {
                wp_enqueue_style('button', get_stylesheet_directory_uri() . '/assets/scss/blocks/button.css');
            }
        ));
    }
}


function block_row($block)
{
    $classes = '';
    if (!empty($block['className'])) {
        $classes .= sprintf(' %s', $block['className']);
    }
    if (!empty($block['align'])) {
        $classes .= sprintf(' align%s', $block['align']);
    }

    $cols = get_field('cols');
    $container_size = get_field('container_size');
    $content_align = get_field('content_alight');
    $horizontal_align = get_field('horizontal_align');

    if (empty($cols)) {
        $cols = 1;
    }

    for ($x = 0; $x < $cols; $x++) {
        $template[] = array('acf/column', array('col_id' => $x));
    }
    $background = '';
    if ($block['style']['color']['background']) {
        $background = $block['style']['color']['background'];
    } ?>

    <div class="container-fluid custom-row  <?= $block['id'] ?>" style="background-color: <?= $background; ?>">
        <div class="<?= $container_size; ?>">
            <div class="row-block row <?php echo esc_attr($classes); ?>"
                 style="align-items: <?= $content_align; ?>; justify-items: <?= $horizontal_align; ?>">
                <?php
                echo '<InnerBlocks template="' . esc_attr(wp_json_encode($template)) . '" templateLock="all"/>';
                ?>
            </div>
        </div>
    </div>


    <style>
        body.wp-admin .custom-row.<?= $block['id'] ?> .row-block .block-editor-inner-blocks > .block-editor-block-list__layout {
            grid-template-columns: repeat(auto-fit, minmax(calc(min(100% / <?= $cols + 1; ?>, max(64px, 100% / <?= $cols + 1; ?>))), 1fr));
        }

        body.wp-admin .custom-row.<?= $block['id'] ?> .row-block > .block-editor-inner-blocks > .block-editor-block-list__layout {
            display: grid;
            gap: 15px;
        }

        body:not(.wp-admin) .custom-row.<?= $block['id'] ?> .row-block {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(calc(min(100% / 3, max(64px, 100% / 4))), 1fr));
            gap: 15px;
        }

        body:not(.wp-admin) .custom-row.<?= $block['id'] ?> .row-block.grid-4-6 {
            display: grid;
            grid-template-columns: 40% calc(60% - 40px);
            grid-column-gap: 40px;
        }
    </style>
    <?php
}

function block_col($block)
{

    $column_align = get_field('column_align');
    $column_width = get_field('column_width');
    $style = 'display: flex; flex-direction: column; height: 100%; ';
    $classes = '';
    if (!empty($block['className'])) {
        $classes .= sprintf(' %s', $block['className']);
    }
    if (!empty($block['align'])) {
        $classes .= sprintf(' align%s', $block['align']);
    }

    if ($column_align) {
        $style .= 'justify-content:' . $column_align . '; ';
    }

    if ($column_width) {
        $style .= 'width: 100%; max-width:' . $column_width . 'px; ';
    }

//    $style

    $template = array(
        array(
            'core/paragraph', array('content' => 'Column'),
            'core/heading',
            'core/image',
            'core/quote',
            'core/list',
            'acf/offutd-button'

        )
    );

    $AllowedBlocks = array(
        'core/paragraph',
        'core/heading',
        'core/image',
        'core/quote',
        'core/list',
        'acf/offutd-button'
    )
    ?>

    <div class="col-block <?php echo esc_attr($classes); ?> <?= $block['id'] ?>"
         style="<?= $style; ?>">
        <?php echo '<InnerBlocks allowedBlocks="' . esc_attr(wp_json_encode($AllowedBlocks)) . '" template="' . esc_attr(wp_json_encode($template)) . '" templateLock="false"/>'; ?>
    </div>

    <style>
        body.wp-admin .wp-block[data-block="<?= str_replace('block_','', $block['id']) ?>"] div {
            height: 100% !important;
            width: 100% !important;
        }

        body.wp-admin .wp-block[data-block="<?= str_replace('block_','', $block['id']) ?>"] figure div {
            height: max-content !important;
        }

        body.wp-admin .wp-block .col-block.<?= $block['id'] ?> .block-editor-block-list__layout {
            display: flex;
            flex-direction: column;
            justify-content: <?= $column_align; ?>;
        }

        body.wp-admin .editor-styles-wrapper .wp-block {
            width: 100%;
        }

    </style>

    <?php
}

function get_dates_in_range($course_dates, $from = null, $to = null, $limit = null)
{
    $data = [];
    if ($course_dates) {
        foreach ($course_dates as $year => $years) {
            foreach ($years as $month => $months) {
                foreach ($months as $type => $courses) {
                    foreach ($courses as $index => $course) {
                        $course['year'] = $year;
                        $course['month'] = $month;
                        $course['type'] = $type;
                        $course['index'] = $index;
                        $course_startdate = date('d-m-Y', $course['startdate']);
                        if (strtotime($course_startdate) >= strtotime($from) && strtotime($course_startdate) <= strtotime($to)) {
                            $data[$course['locality']][] = $course;
                        }
                    }
                }
            }
        }
    }
    return $data;
}

add_action('wp_ajax_change_course_location', 'change_course_location');
add_action('wp_ajax_nopriv_change_course_location', 'change_course_location');
function change_course_location()
{
    if (isset($_POST)) {

        $course_dates = get_post_meta($_POST['product_id'], 'product_dates', true);
        $get_dates_range = get_dates_in_range($course_dates, $_POST['from'], $_POST['to']);
        $main = '';
        $data = [];
        $main_status = true;
        $next5_status = true;

        $productsInCart = array_fill_keys(
            array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'product_id')),
            array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'course_id'))
        );

        foreach ($get_dates_range as $locality => $dates):
            if ($locality === $_POST['location']):
                if (!array_slice($dates, 0, 5)):
                    $main .= '<div class="not-found p-1 bg-danger text-light">' . __("Beklager, datoene ble ikke funnet.") . '</div>';
                    $data['main'] = $main;
                    $main_status = false;
                else:
                    foreach (array_slice($dates, 0, 5) as $date):
                        $date_structure = date("d", $date['startdate']) . 'to' . date("d", $date['enddate']) . '-' . date("m", $date['startdate']) . '-' . date('Y', $date['startdate']);
                        $main .= '<div class="grouped-course-item d-flex mb-2 ' . ($productsInCart[$_POST['product_id']] ? in_array($date['course_id'], $productsInCart[$_POST['product_id']]) ? "active" : " " : " ") . ' " id="' . $date['course_id'] . '" data-id="' . $date['course_id'] . '" data-year="' . $date['year'] . '" data-month="' . $date['month'] . '" data-type="' . $date['type'] . '" data-group_type="main" data-index="' . $date['index'] . '">';
                        $main .= '<div class="bg-info w-100 grouped-course-item__label p-1">' . $date_structure . '-' . $date['locality'] . '</div>';
                        $main .= '<div class="bg-primary text-light grouped-course-item__time p-1 text-center">' . $date['dayphase'] . '</div>';
                        $main .= '<div class="bg-warning text-light grouped-course-item__price p-1 text-center">' . formatted_price($date['price']) . '</div>';
                        $main .= '</div>';
                    endforeach;
                endif;
            endif;
        endforeach;
        if ($main_status === true):
            $main .= '<div class="w-100 text-center">';
            $main .= '<a class="pamelding bg-success text-light px-3 w-100 py-2 mt-3 d-block rounded7">' . __("Påmelding") . '</a>';
            $main .= '</div>';
        endif;
        $data['main'] = $main;

        if ($_POST['next5'] === '1') {
            $next5 = '';
            foreach ($get_dates_range as $locality => $dates):
                if ($locality === $_POST['location']):
                    if (!array_slice($dates, 5, 5)):
                        $next5 .= '<div class="not-found p-1 bg-danger text-light">' . __("Beklager, datoene ble ikke funnet.") . '</div>';
                        $data['next5'] = $next5;
                        $next5_status = false;
                    else:
                        foreach (array_slice($dates, 5, 5) as $date):
                            $date_structure = date("d", $date['startdate']) . 'to' . date("d", $date['enddate']) . '-' . date("m", $date['startdate']) . '-' . date('Y', $date['startdate']);
                            $next5 .= '<div class="grouped-course-item d-flex mb-2 ' . ($productsInCart[$_POST['product_id']] ? in_array($date['course_id'], $productsInCart[$_POST['product_id']]) ? "active" : " " : " ") . '" id="' . $date['course_id'] . '" data-id="' . $date['course_id'] . '" data-year="' . $date['year'] . '" data-month="' . $date['month'] . '" data-type="' . $date['type'] . '" data-group_type="next5" data-index="' . $date['index'] . '">';
                            $next5 .= '<div class="bg-info w-100 grouped-course-item__label p-1">' . $date_structure . '-' . $date['locality'] . '</div>';
                            $next5 .= '<div class="bg-primary text-light grouped-course-item__time p-1 text-center">' . $date['dayphase'] . '</div>';
                            $next5 .= '<div class="bg-warning text-light grouped-course-item__price p-1 text-center">' . formatted_price($date['price']) . '</div>';
                            $next5 .= '</div>';
                        endforeach;
                    endif;
                endif;
            endforeach;
            if ($next5_status === true):
                $next5 .= '<div class="w-100 text-center">';
                $next5 .= '<a class="pamelding bg-success text-light px-3 w-100 py-2 mt-3 d-block rounded7">' . __("Påmelding") . '</a>';
                $next5 .= '</div>';
            endif;

            $data['next5'] = $next5;
        }

        echo json_encode($data);
    }

    exit();
}