<?php /** @var $args */ ?>
<div class="busines_pay">
    <div>
        <div>
            <div>
                <div class="col_form p-3 bg-ligthgrey busines_fields mb-4">
                    <div class="form-grid">
                        <div class="py-1 busines-form-grid_company">
                            <label class="mb-1">Organisasjonsnavn</label>
                            <input type="text" id="billing_company" placeholder="Firmanavn AS" name="billing_company"
                                   class="w-100 required" required>
                        </div>
                        <div class="py-1 busines-form-grid_firstname">
                            <label class="mb-1">Fornavn</label>
                            <input type="text" id="billing_first_name" placeholder="Fornavn"
                                   name="billing_first_name" class="w-100 required" required>
                        </div>

                        <div class="py-1 busines-form-grid_firstname">
                            <label class="mb-1">Etternavn</label>
                            <input type="text" id="billing_last_name" placeholder="Etternavn"
                                   name="billing_last_name" class="w-100 required" required>
                        </div>

                        <div class="py-1 busines-form-grid_mail">
                            <label class="mb-1">E-postadresse</label>
                            <input type="email" id="billing_email" placeholder="navn@domene.no" name="billing_email"
                                   class="w-100 required" required>
                        </div>
                        <div class="py-1 busines-form-grid_phone">
                            <label class="mb-1">Telefon</label>
                            <input type="text" id="billing_phone" placeholder="99999999" name="billing_phone"
                                   class="w-100 required" required>
                        </div>
                        <div class="py-1 busines-form-grid_address">
                            <label class="mb-1">Gateadresse</label>
                            <input type="text" id="billing_address_1" placeholder="Gatenavn 99" name="billing_address_1"
                                   class="w-100 required" required>
                        </div>
                        <div class="py-1 busines-form-grid_zip">
                            <label class="mb-1">Postnummer</label>
                            <input type="text" id="billing_pfostcode" placeholder="9999" name="billing_postcode"
                                   class="w-100 required" required>
                        </div>
                        <div class="py-1 busines-form-grid_posted">
                            <label class="mb-1">Poststed</label>
                            <input type="text" id="billing_city" placeholder="Stedsnavn" name="billing_city"
                                   class="w-100 required" required>
                        </div>
                        <div class="py-1 busines-form-grid_land">
                            <label class="mb-1" placeholder="Input title">Land</label>
                            <div>
                                <select name="billing_country" id="billing_country"
                                        class="checkout_select busines w-100">
                                    <?php foreach ($args['countries'] as $code => $country): ?>
                                        <option value="<?= $code ?>"><?= $country ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h4 class="checkout_title">Ordrereferanse</h4>
    <div>
        <div>
            <div class="mb-4">
                <div class="col_form p-3 bg-ligthgrey">
                    <div>
                        <div class="py-1">
                            <label class="mb-1">Po-nummer/ordrereferanse<span class="ml-1 optionaly bg-green white">ikke påkrevd</span></label>
                            <input type="text" id="custom_billing_po_number" placeholder="Po-nummer"
                                   name="custom_billing_po_number">
                        </div>
                    </div>
                </div>
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

