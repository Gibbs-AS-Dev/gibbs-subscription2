<div id="algorithm_popup" class="mbsc-cloak">

    <form class="algorithm_popup_form" method="post" action="javascript:void(0)">
   
        <div class="algorithm_popup_main">
            <div class="row first_row">

                <div class="col-md-10">
                    <button type="submit" class="btn btn-primary gibbs-btn">Kjør algoritme <i class="fa-solid fa-wand-magic-sparkles"></i></button>
                </div>
                <div class="col-md-2">
                    <i class="fa fa-close algo-close"></i>
                </div>
                
            </div>
            <div class="row">

                <div class="col-md-12">
                    <div class="form-fields">
                        <label>Flytt booking til annet sted/rom (enktelt bookinger) </label>
                        <select id="algo_location_single_booking" class="required"  name="change_location_single_booking">
                            <option value="0" <?php if(isset($template_data["change_location_single_booking"]) && $template_data["change_location_single_booking"] == "0"){ echo 'selected';}?>>Tillat</option>
                            <option value="1" <?php if(isset($template_data["change_location_single_booking"]) && $template_data["change_location_single_booking"] == "1"){ echo 'selected';}?>>Ikke tillat</option>
                        </select>
                    </div>
                </div>
                <?php if($type_of_form == "1"){ ?>

                    <div class="col-md-12">
                        <div class="form-fields">
                            <label>Flytt booking til annet sted/rom (sammenkoblet bookinger) </label>
                            <select id="algo_location_grouped_booking" class="required"  name="change_location_grouped_booking">
                                <option value="0" <?php if(isset($template_data["change_location_grouped_booking"]) && $template_data["change_location_grouped_booking"] == "0"){ echo 'selected';}?>>Ikke tillat</option>
                                <option value="1" <?php if(isset($template_data["change_location_grouped_booking"]) && $template_data["change_location_grouped_booking"] == "1"){ echo 'selected';}?>>Tillat</option>
                            </select>
                        </div>
                    </div>

                <?php } ?>
                <div class="col-md-12">
                    <div class="form-fields">
                        <label>Flytt dato & tid </label>
                        <select id="algo_time" class="required"  name="algo_time">
                            <option value="0" <?php if(isset($template_data["algo_time"]) && $template_data["algo_time"] == "0"){ echo 'selected';}?>>Tillat å endre dato og tid</option>
                            <option value="1" <?php if(isset($template_data["algo_time"]) && $template_data["algo_time"] == "1"){ echo 'selected';}?> >Tillat kun å endre dato</option>
                            <option value="2" <?php if(isset($template_data["algo_time"]) && $template_data["algo_time"] == "2"){ echo 'selected';}?> >Tillat kun å endre tid</option>
                            <option value="3" <?php if(isset($template_data["algo_time"]) && $template_data["algo_time"] == "3"){ echo 'selected';}?> >Ikke tillat å endre dato og tid</option>
                        </select>
                    </div>
                </div>

                <?php if($type_of_form == "2"){ ?>

                    <div class="col-md-12">
                        <div class="form-fields">
                            <label>Flytt hendelsene frem eller tilbake x uker innad i sesongen</label>
                            <select id="algo_move_booking" class="required"  name="algo_move_booking">
                                <?php for($ii = 0;$ii <= 51; $ii++){ ?>
                                    <option value="<?php echo $ii;?>" <?php if(isset($template_data["algo_move_booking"]) && $template_data["algo_move_booking"] == $ii){ echo 'selected';}?>><?php echo $ii;?> uker</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                <?php } ?>

                <div class="col-md-12">
                    <div class="form-fields">
                        <label>Sammenkoblet bookinger</label>
                        <select id="algo_optimalization" class="required"  name="algo_optimalization">
                            <option value="1" <?php if(isset($template_data["algo_optimalization"]) && $template_data["algo_optimalization"] == "1"){ echo 'selected';}?>>Gi så mye som mulig</option>
                            <option value="0" <?php if(isset($template_data["algo_optimalization"]) && $template_data["algo_optimalization"] == "0"){ echo 'selected';}?>>Gi alt eller ingenting</option>
                            
                            <option value="2" <?php if(isset($template_data["algo_optimalization"]) && $template_data["algo_optimalization"] == "2"){ echo 'selected';}?>>Splitt opp sammenkoblet bookinger</option>
                        </select>
                    </div>
                </div>
                
            </div>
        </div>
    </form> 
</div>