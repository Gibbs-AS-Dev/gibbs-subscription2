<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     4.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<div id="comments">
		<h2 class="woocommerce-Reviews-title">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'listeo' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Reviews', 'listeo' );
			}
			?>
		</h2>

		<?php if ( have_comments() ) : ?>

			<ul class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ul>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'listeo' ); ?></p>

		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) : ?>
		<a href="#small-dialog" class="popup-with-zoom-anim button color add-review-button"><?php esc_html_e('Add Review','listeo') ?></a>
		<div id="small-dialog" class="zoom-anim-dialog mfp-hide small-dialog">
			<div id="review_form_wrapper">
				<div id="review_form">
					<h3 class="headline"><?php esc_html_e('Add Review','listeo') ?></h3><span class="line margin-bottom-25"></span><div class="clearfix"></div>
					<?php
					$commenter = wp_get_current_commenter();

					$comment_form = array(
						'title_reply'          => have_comments() ? esc_html__( 'Add a review', 'listeo' ) : esc_html__( 'Be the first to review', 'listeo' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
						'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'listeo' ),
						'title_reply_before'   => '<span id="reply-title" class="comment-reply-title">',
						'title_reply_after'    => '</span>',
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', 'listeo' ) . ' <span class="required">*</span></label> ' .
							            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
							'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'listeo' ) . ' <span class="required">*</span></label> ' .
							            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
						),
						'label_submit'  => esc_html__( 'Submit', 'listeo' ),
						'logged_in_as'  => '',
						'comment_field' => ''
					);

					if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
						$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'listeo' ), esc_url( $account_page_url ) ) . '</p>';
					}

					if ( wc_review_ratings_enabled() ) {
						$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . esc_html__( 'Your Rating', 'listeo' ) .'</label><select name="rating" id="rating">
							<option value="">' . esc_html__( 'Rate&hellip;', 'listeo' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'listeo' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'listeo' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'listeo' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'listeo' ) . '</option>
							<option value="1">' . esc_html__( 'Very Poor', 'listeo' ) . '</option>
						</select></p>';
					}

					$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your Review', 'listeo' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
					?>
				</div>
			</div>
		</div>

	<?php else : ?>

		<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'listeo' ); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
</div>