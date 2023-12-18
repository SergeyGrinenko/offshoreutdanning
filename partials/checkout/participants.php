<?php /** @var $args */

if (isset($args)) :
    $participants = json_decode(stripcslashes($args));
    $participant_field = [];

    foreach ($participants as $index => $participant):
        foreach ($participant as $key => $data) :
            $participant_field[$index][preg_replace('/(-[0-9]+)/', '', $data->name)] = $data->value;
        endforeach;
    endforeach;

    foreach ($participant_field as $person): ?>
        <div class="participants bg-ligthgrey payer-person mb-2"
             data-firstname="<?= $person['firstname'] ?>"
             data-lastname="<?= $person['lastname'] ?>"
             data-birthday="<?= $person['birthday'] ?>"
             data-email="<?= $person['email'] ?>">

            <div>
                <div class="form-grid">
                    <div class="form-grid__fn py-1"><label class="mb-1">Fornavn</label>
                        <p class="m-0 bg-grey part-firstname" id="billing_first_name"><?= $person['firstname'] ?></p></div>
                    <div class="form-grid__ln py-1"><label class="mb-1">Etternavn</label>
                        <p class="m-0 bg-grey part-lastname"id="billing_last_name"><?= $person['lastname'] ?></p></div>
                    <div class="form-grid__dob py-1"><label class="mb-1">Fødselsdato</label>
                        <p class="m-0 bg-grey part-birthday"><?= $person['birthday'] ?></p></div>
                    <div class="form-grid__mail py-1"><label class="mb-1">E-postadresse</label>
                        <p class="m-0 bg-grey part-email" id="billing_email"><?= $person['email'] ?></p></div>
                    <div class="form-grid__mob py-1"><label class="mb-1">Mobiltelefon</label>
                        <p class="m-0 bg-grey part-phone" id="billing_phone"><?= $person['phone'] ?></p></div>
                    <div class="form-grid__address py-1"><label class="mb-1">Gateadresse</label>
                        <p class="m-0 bg-grey part-street" id="billing_address_1"><?= $person['street'] ?></p></div>
                    <div class="form-grid__zip py-1"><label class="mb-1">Postnummer</label>
                        <p class="m-0 bg-grey part-postcode" id="billing_postcode"><?= $person['postcode'] ?></p></div>
                    <div class="form-grid__posted py-1"><label class="mb-1">Poststed</label>
                        <p class="m-0 bg-grey part-city" id="billing_city"><?= $person['city'] ?></p></div>
                    <div class="custom_land py-1"><label class="mb-1">Land</label>
                        <p class="m-0 bg-grey part-select" id="billing_country"><?= $person['select'] ?></p></div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

