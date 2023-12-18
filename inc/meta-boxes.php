<?php
/* CREATE NEW META BOX 'members' */
add_action('add_meta_boxes', 'members_add_custom_box'); // add custom metadata to order list (admin area)
function members_add_custom_box()
{
    $screens = ['shop_order'];
    foreach ($screens as $screen) {
        add_meta_box(
            'members_box_id',
            'Participants',
            'members_custom_box_html',
            $screen
        );
    }
}

/* ADD HTML TO META BOX 'members' */
function members_custom_box_html($order_id)
{
    $order = wc_get_order($order_id);
    $participants = $order->get_meta('participants'); ?>
    <div class="memebrs-meta-box" id="memebrs-meta-box">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr>
                <th>№</th>
                <th><?php _e('Navn'); ?></th>
                <th><?php _e('Fødselsdato'); ?></th>
                <th><?php _e('E-postadresse'); ?></th>
                <th><?php _e('Mobiltelefon'); ?></th>
                <th><?php _e('Gateadresse'); ?></th>
                <th><?php _e('Postnummer'); ?></th>
                <th><?php _e('Poststed'); ?></th>
                <th><?php _e('Land'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            <?php foreach ($participants as $participant): ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $participant->firstname . ' ' . $participant->lastname; ?></td>
                    <td><?php echo $participant->birthday; ?></td>
                    <td><?php echo $participant->email; ?></td>
                    <td><?php echo $participant->phone; ?></td>
                    <td><?php echo $participant->street; ?></td>
                    <td><?php echo $participant->postcode; ?></td>
                    <td><?php echo $participant->city; ?></td>
                    <td><?php echo WC()->countries->countries[$participant->location]; ?></td>
                </tr>
                <?php $i++; endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/* CREATE ORDER WITH CUSTOM COURSE AND MEMBER DATA */
add_action('woocommerce_checkout_create_order', 'before_checkout_create_order', 20, 2);
function before_checkout_create_order($order, $data)
{
    $total = $order->get_total();
    $items = $order->get_items();
    $return = array();  // course data (cource_date, cource_location, etc...)
    $participants = array(); // member additional field (name, bday, email, etc...)

    if (isset($_POST['checkout_data'])) {
        $checkout_data = json_decode(wp_unslash($_POST['checkout_data']), true);
        $participants = array();

        foreach ($checkout_data as $key => $value) {
            if (strpos($key, 'participant_') !== false) {
                $participantNumber = str_replace('participant_', '', $key);
                $participantData = json_decode($value, true);

                // Create participant object
                $participant = new stdClass();

                // Dynamically add properties to the participant object
                foreach ($participantData as $property => $propertyValue) {
                    $participant->{$property} = $propertyValue;
                }

                // Add participant to the array
                $participants[] = $participant;
            }
        }

//        json_encode($participants, JSON_PRETTY_PRINT);
    }

    foreach ($items as $item) {


        $product_id = $item->get_product_id();

        $date = $item->legacy_values['data_from'] . ' til ' . $item->legacy_values['data_to'];

        $return[] = array(
            'cource_date_' . $product_id => $date,
            'cource_location_' . $product_id => $item->legacy_values['location'],
            'cource_time_' . $product_id => $item->legacy_values['data_time'],
            'product_id' => $item->legacy_values['product_id'],
            'few_seats_' . $product_id => $item->legacy_values['few_seats'],
            'course_price_' . $product_id => $item->legacy_values['course_price'],
            'course_id' => $item->legacy_values['course_id'],
            'variation_name' => $item->legacy_values['practices'],
            'subtotal' => $total
        );
    }


    $order->update_meta_data('order_items', $return); // course data (cource_date, cource_location, etc...)
    $order->update_meta_data('participants', $participants); // member additional field (name, bday, email, etc...)

    $total_participants = count($participants);
    $new_total = $total;
    $order->set_total($new_total);
}

add_action('woocommerce_before_order_itemmeta', 'custom_field_display_admin_order_meta', 10, 2); // add field to Order details+

function custom_field_display_admin_order_meta($item_id)
{
    echo '<div>';
    echo '<div><span><b>Dato:&nbsp&nbsp</b></span><span>' . wc_get_order_item_meta($item_id, 'cource_date', true) . '</span>' . (wc_get_order_item_meta($item_id, 'few_seats', true) ? '<span style="color: red;"> (Få plasser)</span>' : '') . '</div>';
    echo '<div><span><b>Time:&nbsp&nbsp</b></span><span>' . wc_get_order_item_meta($item_id, 'cource_time', true) . '</span></div>';
    echo '<div><span><b>Sted:&nbsp&nbsp</b></span><span>' . wc_get_order_item_meta($item_id, 'cource_location', true) . '</span></div>';
    echo '<div><span><b>Course ID:&nbsp&nbsp</b></span><span>' . wc_get_order_item_meta($item_id, 'course_id', true) . '</span></div>';

    if (wc_get_order_item_meta($item_id, 'variation_name', true)) :
        $html = '';
        $html .= '<div><span><b>Praksis:&nbsp&nbsp</b></span>';
        foreach (wc_get_order_item_meta($item_id, 'variation_name', true) as $name => $price) :
            $html .= '<span>' . $name . ' - ' . get_woocommerce_currency_symbol() . $price . ';' . '</span>';
        endforeach;
        $html .= '</div>';
        echo $html;
    endif;
    echo '</div>';
}

add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta');
function custom_checkout_field_update_order_meta($order_id)
{

    if (!empty($_POST['billing_po_number'])) {
        update_post_meta($order_id, '_billing_po_number', sanitize_text_field($_POST['billing_po_number']));
    }

    $order = wc_get_order($order_id);
    $temporary = $order->get_meta('order_items');
    $items = $order->get_items();

    $i = 0;
    foreach ($items as $key => $item) {
        $product_id = $item->get_product_id();
        wc_update_order_item_meta($key, 'cource_date', $temporary[$i]['cource_date_' . $product_id]);
        wc_update_order_item_meta($key, 'cource_location', $temporary[$i]['cource_location_' . $product_id]);
        wc_update_order_item_meta($key, 'cource_time', $temporary[$i]['cource_time_' . $product_id]);
        wc_update_order_item_meta($key, 'course_price', $temporary[$i]['course_price_' . $product_id]);
        wc_update_order_item_meta($key, 'few_seats', $temporary[$i]['few_seats_' . $product_id]);
        wc_update_order_item_meta($key, 'course_id', $temporary[$i]['course_id']);
        wc_update_order_item_meta($key, 'variation_name', $temporary[$i]['variation_name']);
        $i++;
    }
}

add_filter('woocommerce_hidden_order_itemmeta', 'woocommerce_hidden_order_itemmeta', 10, 1);

function woocommerce_hidden_order_itemmeta($arr)
{
    $arr[] = 'cource_date';
    $arr[] = 'cource_location';
    $arr[] = 'cource_time';
    $arr[] = 'course_price';
    $arr[] = 'few_seats';
    $arr[] = 'course_id';
    return $arr;
}


add_action('woocommerce_before_checkout_process', 'customize_checkout_posted_data');

function customize_checkout_posted_data()
{
    // Check if the 'checkout_data' field is set in the posted data
    if (isset($_POST['checkout_data'])) {
        $checkout_data = json_decode(wp_unslash($_POST['checkout_data']), true);

        if (isset($checkout_data['payerType'])) {
            if ($checkout_data['payerType'] === 'bussiness' && isset($checkout_data['company_fields'])) {
                $company_fields = json_decode($checkout_data['company_fields'], true);
                foreach ($company_fields as $company_key => $company_value) {
                    $billing_key = str_replace('company_', 'billing_', $company_key);
                    $_POST[$billing_key] = $company_value;
                }
            } elseif (isset($checkout_data['participantPayer'], $checkout_data[$checkout_data['participantPayer']])) {
                $participantKey = $checkout_data['participantPayer'];
                $participantData = json_decode($checkout_data[$participantKey], true);
                $billing_field_mapping = generate_billing_field_mapping($participantData);
                foreach ($billing_field_mapping as $participant_key => $billing_key) {
                    if (isset($participantData[$participant_key])) {
                        $_POST[$billing_key] = $participantData[$participant_key];
                    }
                }
            }
        }
    }
}

function generate_billing_field_mapping($participantData)
{
    // Define a default mapping
    $default_mapping = $keyMappings = [
        'street' => 'billing_address_1',
        'location' => 'billing_country',
        'firstname' => 'billing_first_name',
        'lastname' => 'billing_last_name',
    ];

    foreach ($keyMappings as $participant_key => $billing_key) {
        if (isset($participantData[$participant_key])) {
            $participantData[$billing_key] = $participantData[$participant_key];
            unset($participantData[$participant_key]);
        }
    }

    // Map remaining keys
    foreach ($participantData as $participant_key => $participant_value) {
        $default_mapping[$participant_key] = 'billing_' . $participant_key;
    }

    return $default_mapping;
}


