<?php
use WpAssetCleanUp\HardcodedAssets;
use WpAssetCleanUp\Main;
use WpAssetCleanUp\ObjectCache;

if (! isset($data)) {
	exit; // no direct access
}

$totalFoundHardcodedTags = 0;
$hardcodedTags = $data['all']['hardcoded'];

$contentWithinConditionalComments = ObjectCache::wpacu_cache_get('wpacu_hardcoded_content_within_conditional_comments');

$totalFoundHardcodedTags  = isset($hardcodedTags['link_and_style_tags']) ? count($hardcodedTags['link_and_style_tags']) : 0;
$totalFoundHardcodedTags += isset($hardcodedTags['script_src_or_inline_and_noscript_inline_tags'])
                            ? count($hardcodedTags['script_src_or_inline_and_noscript_inline_tags']) : 0;

if ($totalFoundHardcodedTags === 0) {
	return; // Don't print anything if there are no hardcoded tags available
}

$handlesInfo = Main::getHandlesInfo();

// Fetch all output rows under an array
$allHardcodedTagsList = array();

$totalStyles = $totalScripts = 0;

foreach ( $hardcodedTags as $targetKey => $listAssets) {
    if ( ! in_array($targetKey, array('link_and_style_tags', 'script_src_or_inline_and_noscript_inline_tags')) ) {
        // Go through the tags only; other information should not be included in the loop
        continue;
    }

    foreach ( $listAssets as $indexNo => $tagOutput ) {
        $contentUniqueStr = HardcodedAssets::determineHardcodedAssetSha1($tagOutput);

        $assetType = '';
        $templateRowOutput = ''; // default (will be filled in the inclusions)

        if ($targetKey === 'link_and_style_tags') {
            $assetType = 'styles';
            include __DIR__ . '/_assets-hardcoded-style-single-row.php';
        } elseif ($targetKey === 'script_src_or_inline_and_noscript_inline_tags') {
            $assetType = 'scripts';
            include __DIR__ . '/_assets-hardcoded-script-single-row.php';
        }

        if ($templateRowOutput !== '' && $assetType !== '') {
            if ($assetType === 'styles') {
                $totalStyles++;
            } elseif ($assetType === 'scripts') {
                $totalScripts++;
            }

            $offset = $hardcodedTags['offset'][$targetKey][$indexNo];
            $allHardcodedTagsList[$offset] = $templateRowOutput;
        }
    }
}

ksort($allHardcodedTagsList);

$totalHardcodedTags  = count($allHardcodedTagsList);
$afterHardcodedTitle = ' &#10141; Total: '. $totalHardcodedTags .' (Styles: '.$totalStyles.', Scripts: '.$totalScripts.')';

if (isset($data['print_outer_html']) && $data['print_outer_html']) { ?>
<div class="wpacu-assets-collapsible-wrap wpacu-wrap-area wpacu-hardcoded">
    <a class="wpacu-assets-collapsible wpacu-assets-collapsible-active" href="#" style="padding: 15px 15px 15px 44px;">
        <span class="dashicons dashicons-code-standards"></span> Hardcoded (non-enqueued) Styles &amp; Scripts<?php echo $afterHardcodedTitle; ?>
    </a>
    <div class="wpacu-assets-collapsible-content" style="max-height: inherit;">
<?php } ?>
    <div style="padding: 0;">
        <div style="margin: 15px 0 0; padding: 0 10px;">
            <p><span style="color: #0073aa;" class="dashicons dashicons-info"></span> The following tags are NOT LOADED via the recommended <a target="_blank"
                                                                 href="https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/">wp_enqueue_scripts()</a>
            action hook (despite the name, it is used for enqueuing both scripts and styles) which is the proper one to use when enqueuing scripts and styles that are meant to appear on
            the front end. The standard functions that are used inside the hook to do an enqueuing are: <a target="_blank"
                                                                                                           href="https://developer.wordpress.org/reference/functions/wp_enqueue_style/">wp_enqueue_style()</a>,
            <a target="_blank" href="https://codex.wordpress.org/Function_Reference/wp_add_inline_style">wp_add_inline_style()</a>,
            <a target="_blank" href="https://developer.wordpress.org/reference/functions/wp_enqueue_script/">wp_enqueue_script()</a>
            &amp; <a target="_blank"
                     href="https://developer.wordpress.org/reference/functions/wp_add_inline_script/">wp_add_inline_script()</a>. The tags could have been added via editing the PHP code (not using the right standard functions), directly inside posts content, widgets or via plugins such as "Insert Headers and Footers", "Head, Footer and Post Injections", etc. Be careful when unloading any of these tags as they might be related to Google Analytics/Google Ads, StatCounter, Facebook Pixel, etc.
            </p>
        </div>
        <div>
            <div class="wpacu-content-title wpacu-has-toggle-all-assets">
                <div class="wpacu-area-toggle-all-assets wpacu-right" style="padding: 0 16px 16px; margin-top: -10px;">
                    <a class="wpacu-area-contract-all-assets wpacu_area_handles_row_expand_contract"
                       data-wpacu-area="hardcoded_all" href="#">Contract</a>
                    |
                    <a class="wpacu-area-expand-all-assets wpacu_area_handles_row_expand_contract"
                       data-wpacu-area="hardcoded_all" href="#">Expand</a>
                    All Assets
                </div>
            </div>
            <table class="wpacu_list_table wpacu_striped" data-wpacu-area="hardcoded_all">
                <tbody>
                <?php
                foreach ($allHardcodedTagsList as $offset => $outputRow) {
                    echo $outputRow."\n";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php if (isset($data['print_outer_html']) && $data['print_outer_html']) { ?>
    </div>
</div>
<?php }
