
<form action="/access" method="POST" class="main_form_access_key">
    <div class="find_key_div"> 
        <h3>Finn adgangskode</h3>
        <label for="reservationIdEdit">
            Skriv inn ordre-ID, reservasjonskode eller bekreftelseskode for å få adgangskoden din. 
        </label>
        <input type="text" id="reservationIdEdit" name="<?= RESERVATION_ID ?>" placeholder="Ordre-ID" value="<?= $reservationId ?>" onkeyup="enableSubmitButton();" />
        <input type="submit" id="submitButton" class="check_code_button" value="Hent adgangskode" <?= $submitButtonStatus ?>/>
        <p>
            <a href="https://support.gibbs.no/index.php/knowledge-base/hvordan-finner-jeg-reservasjons-id/" target="_blank">Hvordan finner jeg reservasjons-ID?</a>
        </p>
    </div>
</form>