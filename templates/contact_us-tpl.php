<?php
/* Template Name: Contact Us */
get_header();

//contact_form_success
?>

    <div class="contact-us">
        <div class="contact-us__header">
            <div class="bg-softdark">
                <div class="container p-0 position-relative">
                    <img src="<?= get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" width="100%"
                         class="d-block page-image" alt="page-image">
                </div>
            </div>
        </div>

        <div class="contact-us__content">
            <div class="container-fluid bg-white py-0">
                <div class="container p-0 bg-light-1">
                    <div class="container-sm">
                        <div class="content-wrapper">

                            <h1><?php the_title(); ?></h1>

                            <p>Ønsker du utdanning for å jobbe innen olje og gass, fiskeri eller skipsfart? Vi
                                hjelper deg, slik at du får kompetansen du trenger. Book time hos en rådgiver
                                nedenfor for veiledning.</p>

                            <form id="submit-enroll" class="submit-enroll" action="submit_enroll_form">
                                <div class="selector">
                                    <div>
                                        <div class="selector_wrapper position-relative">
                                            <select id="method" name="method">
                                                <option value="phone" selected>Telefon</option>
                                                <option value="email">Email</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="selector_wrapper position-relative">
                                        <input type="text" name="datepicker" id="datepicker"
                                               placeholder="Dato">
                                    </div>

                                    <?php
                                    $start_time = new DateTime('09:00');
                                    $end_time = new DateTime('16:00');
                                    $interval = new DateInterval('PT30M');
                                    $currentTime = date("H:i", time()); ?>
                                    <div class="selector_wrapper position-relative disabled">
                                        <select id="time" name="time" class="time" disabled>
                                            <option selected disabled hidden>Klokkeslett</option>
                                            <?php while ($start_time < $end_time) {
                                                $interval_start = $start_time->format('H:i');
                                                $start_time->add($interval);
                                                $interval_end = $start_time->format('H:i');
                                                $time = $interval_start . ' - ' . $interval_end; ?>
                                                <option value="<?= $time; ?>"><?= $time; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form text-left hidden">
                                    <label>Navn:
                                        <input type="text" name="fullname" class="form__input fullname w-100">
                                    </label>

                                    <label>E-post:
                                        <input type="text" name="email" class="form__input email w-100">
                                        <span class="message text-left d-block w-100"></span>
                                    </label>

                                    <label>Mobil:
                                        <input type="text" name="phone" class="form__input phone w-100">
                                        <span class="message text-left d-block w-100"></span>
                                    </label>

                                    <label>Kommentar:
                                        <textarea name="comment" class="form__input comment w-100"
                                                  rows="10"></textarea>
                                    </label>

                                    <input type="hidden" name="page_id" value="<?= get_the_ID(); ?>">

                                    <button type="submit" class="btn bg-pink justify-center text-light rounded7">
                                        Bestill
                                        rådgivning
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>


<?php get_Footer(); ?>