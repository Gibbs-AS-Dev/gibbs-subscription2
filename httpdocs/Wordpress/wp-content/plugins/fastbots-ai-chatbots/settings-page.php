<?php

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

add_action("admin_menu", "fastbots_embed_menu");

function fastbots_embed_menu()
{
    add_options_page(
        "FastBots Settings",
        "FastBots",
        "manage_options",
        "fastbots",
        "fastbots_settings_page"
    );
}

function fastbots_settings_page()
{
    ?>
    <div class="wrap">
        <h2>FastBots</h2>
        <h3>Instructions</h3>

        <ol>
            <li>Go to <b>Embed / Share</b> page of your bot</li>
            <li>Under <b>Using Embed Script</b> section you would see the embed script like so: <code> &lt;script defer src="https://app.fastbots.ai/embed.js" data-bot-id="YOUR_BOT_ID"&gt;&lt;script&gt; </code> </li>
            <li>Copy <code>https://app.fastbots.ai/embed.js</code> or your custom domain URL and paste it in <b>Source URL</b> input field</li>
            <li>Copy <code>YOUR_BOT_ID (which might be someting like this: clvnszmoj00012e2flzihll25)</code> and paste it in <b>Bot ID</b> input field</li>
            <li>Click on Save Changes</li>
        </ol>
        <form method="post" action="options.php">
            <?php settings_fields("fastbots-settings-group"); ?>
            <?php do_settings_sections("fastbots-settings-group"); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Source URL</th>
                    <td><input type="text" id="fastbots_chatbot_embed_code" name="fastbots_chatbot_embed_code" value="<?php echo esc_attr(get_option("fastbots_chatbot_embed_code")); ?>"></td>
                </tr>
                <tr valign="top">
                    <th scope="row">FastBots Embed ID</th>
                    <td><input type="text" id="fastbots_chatbot_embed_code_two" name="fastbots_chatbot_embed_code_two" value="<?php echo esc_attr(get_option("fastbots_chatbot_embed_code_two")); ?>"></td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>
    </div>
<?php
}

add_action("admin_init", "fastbots_settings");

function fastbots_settings()
{
    register_setting("fastbots-settings-group", "fastbots_chatbot_embed_code");
    register_setting(
        "fastbots-settings-group",
        "fastbots_chatbot_embed_code_two"
    );
}
