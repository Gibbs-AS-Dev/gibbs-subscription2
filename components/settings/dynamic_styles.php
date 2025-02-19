<?php

class Dynamic_Styles
{
  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************

  public static function get_user_styles($settings)
  {
?>

/* Styles that are set dynamically, based on settings, to match the storage provider's profile. */
body
{
  background-color: <?= $settings->get_bg_colour() ?>;
}

button
{
  background-color: <?= $settings->get_button_bg_colour() ?>;
  color: <?= $settings->get_button_text_colour() ?>;
}

button i
{
  color: <?= $settings->get_button_text_colour() ?>;
}

button:hover
{
  background-color: <?= $settings->get_button_hover_bg_colour() ?>;
  color: <?= $settings->get_button_hover_text_colour() ?>;
}

button:hover i
{
  color: <?= $settings->get_button_hover_text_colour() ?>;
}

/* Custom checkbox. */
input[type="checkbox"]:checked
{
  background-color: <?= $settings->get_button_bg_colour() ?>;
  border-color: <?= $settings->get_button_bg_colour() ?>;
}

/* Display checkbox check mark as a rotated border. */
input[type="checkbox"]:checked::after
{
  content: '';
  position: absolute;
  left: 5px;
  top: 2px;
  width: 4px;
  height: 9px;
  border: solid <?= $settings->get_button_text_colour() ?>;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

/* Custom radio button. */
input[type="radio"]:checked
{
  border-color: <?= $settings->get_button_bg_colour() ?>;
}

input[type="radio"]:checked::before
{
  content: "";
  display: block;
  width: 12px;
  height: 12px;
  background-color: <?= $settings->get_button_bg_colour() ?>;
  border-radius: 50%;
  position: absolute;
  left: 2px;
  top: 2px;
}

.completed-tab-button
{
  background-color: <?= $settings->get_completed_step_bg_colour() ?>;
  color: <?= $settings->get_completed_step_text_colour() ?>;
}

.active-tab-button
{
  background-color: <?= $settings->get_active_step_bg_colour() ?>;
  color: <?= $settings->get_active_step_text_colour() ?>;
}

.incomplete-tab-button
{
  background-color: <?= $settings->get_incomplete_step_bg_colour() ?>;
  color: <?= $settings->get_incomplete_step_text_colour() ?>;
}

button.date-editor-button
{
  background-color: <?= $settings->get_button_bg_colour() ?>;
  color: <?= $settings->get_button_text_colour() ?>;
}

button.date-editor-button:hover
{
  background-color: <?= $settings->get_button_hover_bg_colour() ?>;
}

td.sum
{
  background-color: <?= $settings->get_sum_bg_colour() ?>;
  color: <?= $settings->get_sum_text_colour() ?>;
}

.info-button i,
.profile-text-colour
{
  color: <?= $settings->get_button_bg_colour() ?>;
}

.info-button:hover i
{
  color: <?= $settings->get_button_hover_bg_colour() ?>;
}

div.terms-box a
{
  color: <?= $settings->get_button_bg_colour() ?>;
}

div.terms-box a:hover
{
  color: <?= $settings->get_button_hover_bg_colour() ?>;
}

<?php
  }

  // *******************************************************************************************************************
}
?>
