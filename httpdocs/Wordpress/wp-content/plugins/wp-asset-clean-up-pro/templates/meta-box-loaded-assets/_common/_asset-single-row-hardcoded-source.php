<?php
/*
 * The file is included from /templates/meta-box-loaded-assets/_common/_asset-single-row-hardcoded.php
*/

use WpAssetCleanUp\Misc;

if ( ! isset($data) ) {
	exit; // no direct access
}

$assetType  = $data['row']['asset_type'];
$assetTypeS = substr($data['row']['asset_type'], 0, -1); // "styles" to "style" & "scripts" to "script"

if (isset($data['row']['obj']->src, $data['row']['obj']->srcHref) && trim($data['row']['obj']->src) !== '' && $data['row']['obj']->srcHref) {
	$isExternalSrc = true;

	if (Misc::getLocalSrcIfExist($data['row']['obj']->src)
        || strpos($data['row']['obj']->src, '/?') !== false // Dynamic Local URL
        || strpos(str_replace(site_url(), '', $data['row']['obj']->src), '?') === 0 // Starts with ? right after the site url (it's a local URL)
	) {
		$isExternalSrc = false;
	}

	$srcHref = $data['row']['obj']->srcHref;

	// If the source starts with '../' mark it as external to be checked via the AJAX call (special case)
	if (strpos($srcHref, '../') === 0) {
		$currentPageUrl = Misc::getCurrentPageUrl();
		$srcHref        = trim( $currentPageUrl, '/' ) . '/' . $data['row']['obj']->srcHref;
		$isExternalSrc  = true; // simulation
	}

	$relSrc = str_replace(site_url(), '', $data['row']['obj']->src);

	if (isset($data['row']['obj']->baseUrl)) {
		$relSrc = str_replace($data['row']['obj']->baseUrl, '/', $relSrc);
	}
	?>
    <div class="wpacu-source-row">
		<?php _e( 'Source:', 'wp-asset-clean-up' ); ?>
        <a target="_blank"
           style="color: green;" <?php if ( $isExternalSrc ) { ?> data-wpacu-external-source="<?php echo esc_attr($srcHref); ?>" <?php } ?>
           href="<?php echo esc_attr($data['row']['obj']->src); ?>"><?php echo esc_html($relSrc); ?></a>
		<?php if ( $isExternalSrc ) { ?><span data-wpacu-external-source-status></span><?php } ?>

        <?php
        // [wpacu_pro]
        // Preload?
        if ($assetTypeS === 'style') {
            $isCssPreload = (isset($data['preloads']['styles'][$data['row']['obj']->handle]) && $data['preloads']['styles'][$data['row']['obj']->handle])
                ? $data['preloads']['styles'][$data['row']['obj']->handle]
                : false;
        } elseif ($assetTypeS === 'script') {
            $isJsPreload = (isset($data['preloads']['scripts'][$data['row']['obj']->handle]) && $data['preloads']['scripts'][$data['row']['obj']->handle])
                ? $data['preloads']['scripts'][$data['row']['obj']->handle]
                : false;
        }
        include dirname(__DIR__).'/_asset-'.$assetTypeS.'-single-row/_preload.php';

        $extraInfo            = array();
        $assetHandleHasSrc    = true;
        $assetPosition        = isset($data['row']['obj']->position)     ? $data['row']['obj']->position     : '';
        $assetPositionNew     = isset($data['row']['obj']->position_new) ? $data['row']['obj']->position_new : $assetPosition;
        $assetLocationChanged = $assetPositionNew !== $assetPosition;

        include dirname(__DIR__) . '/_common/_asset-single-row-position.php';

        if (isset($extraInfo[0]) && $extraInfo[0]) {
            echo '&nbsp;/&nbsp;' . $extraInfo[0];

            if ($assetLocationChanged) {
                ?>
                <div style="display: inline-block; color: #004567; font-style: italic; font-size: 90%; font-weight: 600; margin: 4px 0 10px;">
                    <span class="dashicons dashicons-info" style="font-size: 19px; line-height: normal;"></span> <?php _e('This file has its initial location changed.', 'wp-asset-clean-up'); ?>
                </div>
                <?php
            }
        }
        // [/wpacu_pro]
        ?>
    </div>
	<?php
}
