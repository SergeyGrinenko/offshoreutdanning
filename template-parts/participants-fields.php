<?php
//$item = $args['item'];
$participant = null;
$order = null;
if (isset($args['order-received']) && $args['order-received'] === true)
    $order = $args['order'];

if (isset($args['participant']))
    $participant = $args['participant'];

//$editable = true;
//if (isset($args['editable']) && !is_null($args['editable']))
//    $editable = $args['editable'];

$billing = false;
if (isset($args['billing']) && !is_null($args['billing']))
    $billing = $args['billing'];

$min_age = date('Y-m-d', strtotime('16 years ago'));
$max_age = date('Y-m-d', strtotime('85 years ago')); ?>

<li class="item-id mb-3 rounded7 open" id="1">
    <div>
        <div class="align-unset">
            <div class="col_form_header d-flex justify-between fw-bold p-3 bg-light-1">
                <div><?php _e('Deltaker:'); ?> <span class="fullname"></span></div>
                <div>
                    <img src="<?= get_stylesheet_directory_uri() . '/assets/images/circle-arrow.svg' ?>"
                         alt="icon-toggle" class="icon-toggle" width="28">
                    <img src="<?= get_stylesheet_directory_uri() . '/assets/images/icon-plus-red.svg' ?>"
                         alt="icon-close" class="col_button">
                </div>
            </div>
            <div class="col_form col_form_body p-0 w-100 show">
                <div class="form-grid p-3">
                    <div class="form-grid__fn required">
                        <label class="mb-1"><?php _e('Fornavn'); ?></label>
                        <input type="text" id="firstname"
                               placeholder="<?php _e('Fornavn'); ?>"
                               name="<?= ($billing === true ? 'billing_first_name' : 'firstname'); ?>"
                               class="required firstname p-1 w-100"
                               required aria-required="true"
                               value="<?= (!is_null($participant) ? $participant['firstname'] : (!is_null($order) ? $order->get_billing_first_name() : '')) ?>">
                    </div>
                    <div class="form-grid__ln required">
                        <label class="mb-1"><?php _e('Etternavn'); ?></label>
                        <input type="text" id="lastname"
                               placeholder="<?php _e('Etternavn'); ?>"
                               name="<?= ($billing === true ? 'billing_last_name' : 'lastname'); ?>"
                               class="required lastname p-1  w-100"
                               required aria-required="true"
                               value="<?= (!is_null($participant) ? $participant['lastname'] : (!is_null($order) ? $order->get_billing_last_name() : '')) ?>">
                    </div>
                    <?php if (is_null($order)): ?>
                        <div class="form-grid__dob required">
                            <label class="mb-1"><?php _e('Fødselsdato'); ?></label>
                            <input type="date" id="birthday" name="birthday"
                                   min="<?= $max_age; ?>"
                                   max="<?= $min_age; ?>"
                                   class="required birthday p-1  w-100"
                                   required aria-required="true"
                                   value="<?= (!is_null($participant) ? $participant['birthday'] : '') ?>">
                        </div>
                    <?php endif; ?>

                    <div class="form-grid__mail required">
                        <label class="mb-1"><?php _e('E-postadresse'); ?></label>
                        <input type="email" id="email" placeholder="navn@domene.no"
                               name="<?= ($billing === true ? 'billing_email' : 'email'); ?>"
                               class="required email p-1  w-100"
                               required aria-required="true"
                               value="<?= (!is_null($participant) ? $participant['email'] : (!is_null($order) ? $order->get_billing_email() : '')) ?>">
                    </div>
                    <div class="form-grid__mob required">
                        <label class="mb-1"><?php _e('Mobiltelefon'); ?></label>
                        <input type="text" id="phone" placeholder="99999999"
                               name="<?= ($billing === true ? 'billing_phone' : 'phone'); ?>"
                               class="required phone p-1  w-100"
                               required aria-required="true"
                               value="<?= (!is_null($participant) ? $participant['phone'] : (!is_null($order) ? $order->get_billing_phone() : '')) ?>">
                    </div>

                    <div class="form-grid__address required">
                        <label class="mb-1"><?php _e('Gateadresse'); ?></label>
                        <input type="text" id="street" placeholder="Gatenavn 99"
                               name="<?= ($billing === true ? 'billing_address_1' : 'street'); ?>"
                               class="required street p-1  w-100"
                               required aria-required="true"
                               value="<?= (!is_null($participant) ? $participant['street'] : (!is_null($order) ? $order->get_billing_address_1() : '')) ?>">
                    </div>

                    <div class="form-grid__zip required">
                        <label class="mb-1"><?php _e('Postnummer'); ?></label>
                        <input type="text" id="postcode" placeholder="9999"
                               name="<?= ($billing === true ? 'billing_postcode' : 'postcode'); ?>"
                               class="required postcode p-1 w-100"
                               maxlength="6"
                               required aria-required="true"
                               value="<?= (!is_null($participant) ? $participant['postcode'] : (!is_null($order) ? $order->get_billing_postcode() : '')) ?>">
                    </div>
                    <div class="form-grid__posted required">
                        <label class="mb-1"><?php _e('Poststed'); ?></label>
                        <input type="text" id="city" placeholder="Stedsnavn"
                               name="<?= ($billing === true ? 'billing_city' : 'city'); ?>"
                               class="required city p-1  w-100"
                               required aria-required="true"
                               value="<?= (!is_null($participant) ? $participant['city'] : (!is_null($order) ? $order->get_billing_city() : '')) ?>">
                    </div>
                    <div>
                        <label class="mb-1"><?php _e('Land'); ?></label>
                        <div class="select-course-locations w-100">
                            <select class="p-1 w-100 required"
                                    name="<?= ($billing === true ? 'billing_country' : 'location'); ?>"
                                    id="location">
                                <?php if (!is_null($participant)): ?>
                                    <option value="<?= $participant['location']; ?>"><?= WC()->countries->countries[$participant['location']]; ?></option>
                                <?php elseif (!is_null($order)): ?>
                                    <option value="<?= $order->get_billing_country(); ?>"><?= WC()->countries->countries[$order->get_billing_country()]; ?></option>
                                <?php else: ?>
                                    <?php foreach (get_allowed_countries() as $country_code => $country_name): ?>
                                        <option value="<?= $country_code; ?>"><?= $country_name; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
