<?php /** @var $args */ ?>
<div class="member_pay">
    <div>
        <div class="p-3 bg-ligthgrey">
            <div class="payerinfo">
                <label>Navn</label>
                <select name="select" class="checkout_select name_select w-100">
                    <?php if (isset($args['payers'])) :
                        $payers = json_decode(stripcslashes($args['payers']));
                        $array = [];
                        foreach ($payers as $key => $payer) :
                            foreach ($payer as $data) :
                                $name = preg_replace('/(-[0-9]+)/', '', $data->id);
                                $array[$key]['data-' . $name . ''] = $data->value;
                            endforeach;
                        endforeach;

                        foreach ($array as $key => $values) : ?>
                            <option
                                <?php foreach ($values as $data => $value) :
                                    echo $data . '="' . $value . '"';
                                endforeach; ?>>
                                <?= $values['data-firstname'] . ' ' . $values['data-lastname']; ?>
                            </option>
                        <?php endforeach; endif ?>
                </select>
            </div>
        </div>
    </div>
    <h4 class="checkout_title">Betales med</h4>
    <div class="field-error"><p>Betales av må velges.</p></div>
    <div class="d-flex align-items-center payment_methods_wrap">
        <div class="payment_methods">
            <?php foreach ($args['payments'] as $method) :
                get_template_part('partials/checkout/payment_methods', null, $method);
            endforeach; ?>
        </div>
        <div class="message ml-3">
        </div>
    </div>
</div>