<div class="company_payer hide">
<h3 class="fw-bold"><?php _e('Betales av bedrift'); ?></h3>
<div class="company_form rounded7 p-3 w-100">
    <div class="form-grid">
        <div class="form-grid__organization-number">
            <label class="mb-1"><?php _e('Organisasjonsummer (Valgfritt)'); ?></label>
            <input type="text" id="company-organization-number"
                   placeholder="<?php _e('Organisasjonsummer'); ?>"
                   name="company_organization_number"
                   class="company-organization-number p-1 w-100">
        </div>

        <div class="form-grid__company required">
            <label class="mb-1"><?php _e('Organisasjonsnavn'); ?></label>
            <input type="text" id="company-name"
                   placeholder="<?php _e('Organisasjonsnavn'); ?>"
                   name="company_company"
                   class="required company-name p-1 w-100">
        </div>
        <div class="form-grid__fn required">
            <label class="mb-1"><?php _e('Fornavn'); ?></label>
            <input type="text" id="company-firstname"
                   placeholder="<?php _e('Fornavn'); ?>"
                   name="company_first_name"
                   class="required company-firstname p-1 w-100">
        </div>
        <div class="form-grid__ln required">
            <label class="mb-1"><?php _e('Etternavn'); ?></label>
            <input type="text" id="company-lastname"
                   placeholder="<?php _e('Etternavn'); ?>"
                   name="company_last_name"
                   class="required company-lastname p-1 w-100">
        </div>

        <div class="form-grid__mail required">
            <label class="mb-1"><?php _e('E-postadresse'); ?></label>
            <input type="email" id="company-email" placeholder="navn@domene.no"
                   name="company_email"
                   class="required company-email p-1 w-100">
        </div>
        <div class="form-grid__mob required">
            <label class="mb-1"><?php _e('Mobiltelefon'); ?></label>
            <input type="text" id="company-phone" placeholder="99999999"
                   name="company_phone"
                   class="required company-phone p-1 w-100">
        </div>

        <div class="form-grid__address required">
            <label class="mb-1"><?php _e('Gateadresse'); ?></label>
            <input type="text" id="company-street" placeholder="Gatenavn 99"
                   name="company_address_1"
                   class="required company-street p-1 w-100">
        </div>

        <div class="form-grid__zip required">
            <label class="mb-1"><?php _e('Postnummer'); ?></label>
            <input type="text" id="company-postcode" placeholder="9999"
                   name="company_postcode"
                   class="required company-postcode p-1 w-100"
                   maxlength="6">
        </div>
        <div class="form-grid__posted required">
            <label class="mb-1"><?php _e('Poststed'); ?></label>
            <input type="text" id="company-city" placeholder="Stedsnavn"
                   name="company_city"
                   class="required company-city p-1 w-100">
        </div>

        <div class="form-grid__location">
            <label class="mb-1"><?php _e('Land'); ?></label>
            <div class="select-course-locations w-100">

                <select class="p-1 w-100"
                        name="company_country"
                        id="company-location">
                    <?php foreach (get_allowed_countries() as $country_code => $country_name): ?>
                        <option value="<?= $country_code; ?>"><?= $country_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid__po-number">
            <label><?php _e('PO-nummer/ordrereferanse (Valgfritt)'); ?></label>
            <input type="text" id="company-ordrereferanse" placeholder="Po-nummer" name="company-ordrereferanse"
                   class="company-ordrereferanse p-1 w-100">
        </div>

    </div>
</div>
</div>