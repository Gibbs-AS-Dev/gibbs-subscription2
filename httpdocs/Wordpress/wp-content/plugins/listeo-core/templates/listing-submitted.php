<div class="listing-added-notice">
	<div class="booking-confirmation-page">
		<i class="fa fa-check-circle"></i>
		<h2 class="margin-top-30"><?php esc_html_e('Suksess!','listeo_core') ?></h2>
		<p><?php // Successful

switch (get_post_status($data->id)) {
    case 'publish':
        $random_message = rand(1, 5); // Velg tilfeldig melding fra 1 til 5

        switch ($random_message) {
            case 1:
                esc_html_e('Annonsen er publisert! Gratulerer, du er nå en bookingsuperhelt!', 'listeo_core');
                break;
            case 2:
                esc_html_e('Annonsen er publisert! Hold fast, vi går på en bookingberg-og-dalbane!', 'listeo_core');
                break;
            case 3:
                esc_html_e('Annonsen er publisert! Sett deg godt til rette, vi er klare for en bookningsfest!', 'listeo_core');
                break;
            case 4:
                esc_html_e('Annonsen er publisert! Forbered deg på bookingstormen som kommer!', 'listeo_core');
                break;
            case 5:
                esc_html_e('Annonsen er publisert! Løp og hent popcorn, bookinger blir showet i dag!', 'listeo_core');
                break;
            default:
                esc_html_e('Annonsen er publisert! La oss ønske velkommen alle de nye bookingene!', 'listeo_core');
                break;
        }

        break;

    case 'draft':
        $random_message = rand(1, 5); // Velg tilfeldig melding fra 1 til 5

        switch ($random_message) {
            case 1:
                esc_html_e('Annonsen er lagret som utkast. Fort deg å publisere den, vi kan ikke gå glipp av bookinger!', 'listeo_core');
                break;
            case 2:
                esc_html_e('Annonsen er lagret som utkast. Din annonse ser flott ut som et utkast! Bare et lite steg unna fra å bli fullført!', 'listeo_core');
                break;
            case 3:
                esc_html_e('Annonsen er lagret som utkast. Kom deg ut av utkastmodus så fort som mulig!', 'listeo_core');
                break;
            case 4:
                esc_html_e('Annonsen er lagret som utkast. Publiser den og la bookingene strømme inn!', 'listeo_core');
                break;
            case 5:
                esc_html_e('Annonsen er lagret som utkast. Annonsen din venter tålmodig i utkastmodus. Klar til å skinne!', 'listeo_core');
                break;
            default:
                esc_html_e('Annonsen er publisert! La oss ønske velkommen alle de nye bookingene!', 'listeo_core');
                break;
        }

        break;

    case 'pending_payment':
        esc_html_e('Your listing has been saved and is pending payment. It will be published once the order is completed', 'listeo_core');
        break;

	case 'expired':
		esc_html_e('Annonsen er for øyeblikket deaktivert. For å vekke den til live når den er ferdig med ferien sin, klikker du bare på (Publiser)', 'listeo_core');
		break;

    case 'pending':
        esc_html_e('Annonsen er lagret som utkast', 'listeo_core');
        break;

    default:
        esc_html_e('', 'listeo_core');
        break;
}

		 ?>
		</p>
		<?php if(get_post_status( $data->id ) == 'publish') : ?>
			<a class="button margin-top-30" href="/my-listings"><?php esc_html_e( 'Mine annonser &rarr;', 'listeo_core' ); ?></a>

			<a class="button margin-top-30" href="<?php echo get_permalink( $data->id ); ?>"><?php  esc_html_e( 'Vis annonsen &rarr;', 'listeo_core' );  ?></a>
		<?php endif; ?>

		<?php if(get_post_status( $data->id ) == 'pending') : ?>
			<a class="button margin-top-30" href="/my-listings"><?php esc_html_e( 'Mine annonser &rarr;', 'listeo_core' ); ?></a>

			<a class="button margin-top-30" href="<?php echo get_permalink( $data->id ); ?>"><?php  esc_html_e( 'Vis annonsen &rarr;', 'listeo_core' );  ?></a>
		<?php endif; ?>

		<?php if(get_post_status( $data->id ) == 'draft') : ?>
			<a class="button margin-top-30" href="/my-listings"><?php esc_html_e( 'Mine annonser &rarr;', 'listeo_core' ); ?></a>

		<?php endif; ?>

		<?php if(get_post_status( $data->id ) == 'expired') : ?>
			<a class="button margin-top-30" href="/my-listings"><?php esc_html_e( 'Mine annonser &rarr;', 'listeo_core' ); ?></a>

		<?php endif; ?>
	</div>
</div>

