<?php

class Offutd_Menu_Walker extends Walker_Nav_Menu
{
    private $curItem;

    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);

        if ($depth === 0) {
            $output .= "\n$indent<div class='container-fluid custom-mega-menu bg-light-1 py-0'><div class='container-sm px-0'><ul class=\" dropdown-menu\">\n";
        } else {
            $output .= "\n$indent<div class='container-fluid post-items bg-light-1 py-0'><div class='container-sm px-0'><ul class=\" inner-items\">\n";
        }
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {

        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $menu_image = (get_field('menu_image', $item->ID) ? get_field('menu_image', $item->ID) : get_stylesheet_directory_uri() . '/assets/images/offshore-placeholder.png');
        $size = 'course-thumbnail';

        if ($menu_image && !is_bool($menu_image)) {

            if (is_array($menu_image)) {
                $thumb = $menu_image['sizes'][$size];
            } else {
                $thumb = $menu_image;
            }
        }

        $menu_description = get_field('menu_description', $item->ID);

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array)$item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        /*grab the default wp nav classes*/
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));

        /*if the current item has children, append the dropdown class*/
        if ($args->has_children && $depth < 1)
            $this->curItem = $item;
        $class_names .= ' dropdown';

        /*if there aren't any class names, don't show class attribute*/
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names . '>';

        $atts = array();
        $atts['title'] = !empty($item->title) ? $item->title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';


        /*if the current menu item has children and it's the parent, set the dropdown attributes*/
        if ($args->has_children && $depth === 0) {
            $atts['href'] = '#';
            $atts['data-toggle'] = 'dropdown';
            $atts['class'] = 'dropdown-toggle';
        } else {
            $atts['href'] = !empty($item->url) ? $item->url : '';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;

        $item_output .= '<a' . $attributes . '>';

        if ($depth > 0 && $depth < 2) {

            $item_output .= '<div class="menu__sub-item">';

            $item_output .= '<img src="' . esc_url($thumb) . '" width="100%" alt="' . $item->ID . '">';

            if ($args->has_children) {
                $item_output .= '<div class="text-dark"><h3 class="my-3">' . apply_filters('the_title', $item->title, $item->ID) . '</h3>';
            } else {
                $item_output .= '<div class="p-3 bg-softdark text-white"><h3 class="my-3">' . apply_filters('the_title', $item->title, $item->ID) . '</h3>';
            }

            if ($menu_description)
                $item_output .= '<p class="m-0 mb-3">' . $menu_description . '</p>';

            $item_output .= '</div>';
            $item_output .= '</div>';

        } else {
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        }

        /*	if the current menu item has children and it's the parent item, append the fa-angle-down icon*/
        $item_output .= ($args->has_children && $depth === 0) ? ' </a>' : '</a>';

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);

    }


    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        if (0 == $depth) {
            if ($this->curItem->title !== 'YRKESFAG') {
                if ($this->curItem->title == 'HJELP') {
                    $title = $this->curItem->title;
                } else {
                    $title = 'kurs';
                }
                $output .= "<li class='last-item'><a href='" . $this->curItem->url . "' class='bg-light-2 text-dark p-3'><div class='text-dark'><img src='" . get_stylesheet_directory_uri() . "/assets/images/arrow-right.svg' alt='arrow-right' width='80'> Vis alle " . ucfirst(strtolower($title)) . "</div></a></li>";
            }
        }

        $indent = str_repeat("\t", $depth);
        $output .= "{$indent}</ul>\n";
    }

    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
    {
        if (!$element)
            return;

        $id_field = $this->db_fields['id'];

        if (is_object($args[0]))
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);

    }
}