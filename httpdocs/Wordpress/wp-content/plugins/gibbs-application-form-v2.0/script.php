<?php
global $group_id;
global $season_id;
?>
<link href="<?php echo plugin_dir_url(__FILE__);?>css/toastr.css" rel="stylesheet"/>
<script src="<?php echo plugin_dir_url(__FILE__);?>js/toastr.js" type="text/javascript"></script>
<script type="text/javascript">

var phonenumber = document.getElementById("phonenumberForm");
    jQuery(phonenumber).intlTelInput({
      initialCountry: "NO",
      separateDialCode: true,
      hiddenInput: "full",
      preferredCountries: ["NO"],
      utilsScript:
        "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.14/js/utils.js",
    });

  function setPickerValues(){
      jQuery(".date-picker").each(function(){
          if(this.value != ""){
              let start_date = new Date(this.value);
              let minDate;
              if(this.name.includes("from_date") == true){
                 minDate = startDatedd;
              }else{
                 minDate = start_date;
              }
             datepcikerr(this,start_date,endDatedd,minDate);
          }else{
             datepcikerr(this,startDatedd,endDatedd);
          }
      })
  }

  function hideDeleteButton(){

    jQuery(".application_section").find(".single_application").each(function(){

        if( jQuery(this).find(".main_get_day_app").find(".inner-get_day").length > 1){
            jQuery(this).find(".main_get_day_app").find(".inner-get_day").find(".delete_reserve").show();
        }else{
             jQuery(this).find(".main_get_day_app").find(".inner-get_day").first().find(".delete_reserve").hide();
        }

    })
    

  }

  function select2func(){
    jQuery(".select2_field").select2({
                    placeholder: 'Velg',
                    closeOnSelect: false
                    /*
                    width: 'resolve',
                    dropdownAutoWidth: 'true',
                    allowClear: 'true'*/
                  });

    changeslect();
  }
  function changeslect(){

      jQuery("body").find("select").each(function(){

          if(this.multiple == true){


             let optionss = [];

             jQuery(this).find("option:selected").each(function(){
                optionss.push(this.value);
             })

              /*if(optionss > 0 ){

                jQuery(this).parent().find(".select2-container").find(".selection").html("Selected ("+optionss.length+")");
              }else{
                jQuery(this).parent().find(".select2-selection--multiple").html("");
              }*/
              var uldiv = jQuery(this).siblings('span.select2').find('ul')
              var count = jQuery(this).select2('data').length
              if(count==0){
                uldiv.html("")
                jQuery(this).siblings('span.select2').find(".select2-search").show();
              }
              else{
                jQuery(this).siblings('span.select2').find(".select2-search").hide();
                uldiv.html("<li>Valgt ("+count+")</li>")
              }

             let data = optionss.join(",");
             jQuery(this).parent().find("input").val(data);
          } 
      })  

    }

  function datepcikerr(target,start_date,end_date,minDate = ""){
      if(minDate == ""){
        minDate = start_date;
      }
      jQuery("body").find(target).each(function(){
          jQuery(this).daterangepicker({
              "opens": "right",
              autoUpdateInput: false,
              // checking attribute listing type and set type of calendar
              singleDatePicker: true, 
              timePicker: false,
              minDate: minDate,
              maxDate: end_date,
              startDate : start_date,
              endDate : end_date,
              locale: {
                  format: wordpress_date_format.date,
                  "firstDay": parseInt(wordpress_date_format.day),
                  "applyLabel"  : listeo_core.applyLabel,
                      "cancelLabel" : listeo_core.cancelLabel,
                      "fromLabel"   : listeo_core.fromLabel,
                      "toLabel"   : listeo_core.toLabel,
                      "customRangeLabel": listeo_core.customRangeLabel,
                      "daysOfWeek": [
                          listeo_core.day_short_su,
                          listeo_core.day_short_mo,
                          listeo_core.day_short_tu,
                          listeo_core.day_short_we,
                          listeo_core.day_short_th,
                          listeo_core.day_short_fr,
                          listeo_core.day_short_sa
                      ],
                      "monthNames": [
                          listeo_core.january,
                          listeo_core.february,
                          listeo_core.march,
                          listeo_core.april,
                          listeo_core.may,
                          listeo_core.june,
                          listeo_core.july,
                          listeo_core.august,
                          listeo_core.september,
                          listeo_core.october,
                          listeo_core.november,
                          listeo_core.december,
                      ],
                    
                },
          });
          jQuery(this).on('apply.daterangepicker', function (ev, picker) {
              ev.currentTarget.value = moment( picker.startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

              if(ev.currentTarget.name.includes("from_date") == true){

                  var to_date = jQuery(ev.currentTarget).parent().parent().parent().find(".to_date input");

                  to_date.val(ev.currentTarget.value);


                  to_date = to_date[0];

                  var startddd = new Date(ev.currentTarget.value);

                  datepcikerr(to_date,startddd,endDatedd);
              }

              /*setTimeout(function(){
                var to_date_t = to_date;
                jQuery(to_date_t).data().minDate = new Date(ev.currentTarget.value);
                jQuery(to_date_t).data().startDate = new Date(ev.currentTarget.value);
              },1000)*/
          });

      });  
  }  
    let startDatedd = jQuery("input[name=start_date]").val();
    let endDatedd = jQuery("input[name=end_date]").val();

    if(startDatedd != "" && startDatedd != undefined){
      startDatedd = new Date(startDatedd);
    }
    if(endDatedd != "" && endDatedd != undefined){
      endDatedd = new Date(endDatedd);
    }
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-bottom-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }

    function add_new_application(event){
      jQuery(event).prop("disabled",true);
    	let datas = {
    		   "action" : "add_new_application_ajax",
    		   "form_name" : "<?php echo $form_name;?>",
           "group_id" : "<?php echo $group_id;?>",
           "season_id" : "<?php echo $season_id;?>",
    		   "index" : jQuery('.single_application').length + 1,
    		}
        jQuery.ajax({
    		      type: "GET",
    		      url: "<?php echo admin_url( 'admin-ajax.php' );?>",
    		      data: datas,
    		      dataType: "json",
    		      success: function(resultData){
                    jQuery(event).prop("disabled",false);
    		            jQuery(".application_section").append(resultData.content);
                    setPickerValues();
                    select2func();
                    hideDeleteButton();
    		      }
    		});
    }
    function add_reservation(event,application_id){

        var index = jQuery(".main_get_day_"+application_id).find(".inner-get_day").last().index() + 1;

        jQuery(event).prop("disabled",true);

        index = index + 1;

        let datas = {
               "action" : "add_reservation_ajax",
               "form_name" : "<?php echo $form_name;?>",
               "group_id" : "<?php echo $group_id;?>",
               "season_id" : "<?php echo $season_id;?>",
               "application_id" : application_id,
               "index" : index,
            }
        jQuery.ajax({
                  type: "POST",
                  url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                  data: datas,
                  dataType: "json",
                  success: function(resultData){
                       jQuery(event).prop("disabled",false);
                       jQuery(".main_get_day_"+application_id).append(resultData.content);

                       var ds_days = [];

                       jQuery(".main_get_day_"+application_id).find(".day select").each(function(){
                           if(this.value != ""){
                              ds_days.push(this.value);
                              jQuery(".main_get_day_"+application_id).find(".day select").last().find("option[value='"+parseInt(this.value)+"']").prop("disabled",true);
                           }
                       })

                       //datepcikerr(".date-picker",startDatedd,endDatedd);

                      setPickerValues();

                      select2func();
                      hideDeleteButton();


                  }
            });
    }
    function add_fields(event, application_id){


        var res_count = jQuery(event).parent().parent().parent().index();

        var index = jQuery(event).parent().parent().parent().find(".custom_fields").find(".inner-advanced_fields").last().index() + 1;

        index = index + 1;

        let datas = {
               "action" : "add_fields_ajax",
               "form_name" : "<?php echo $form_name;?>",
               "group_id" : "<?php echo $group_id;?>",
               "season_id" : "<?php echo $season_id;?>",
               "application_id" : application_id,
               "res_count" : res_count,
               "index" : index,
            }
          jQuery.ajax({
                type: "POST",
                url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                data: datas,
                dataType: "json",
                success: function(resultData){
                    jQuery(event).parent().parent().parent().find(".custom_fields").append(resultData.content);

                    setPickerValues();

                    select2func();


                }
          });
    }
    jQuery(document).on("change","select",function(){
       changeslect();
    });
    jQuery(document).on("change",".day select",function(){

        let that;
        that = this;

        let application_id = jQuery(that).attr("application_id");


        //if(jQuery(this).data("pre_value") != undefined){

          var previous = jQuery(that).attr("data-pre_value");
          jQuery(".main_get_day_"+application_id).find(".day").find("select").each(function(){

                if(previous != ""){
                   jQuery(this).find("option[value='"+parseInt(previous)+"']").prop("disabled",false);
                }
                if(that.value != ""){
                  jQuery(this).find("option[value='"+parseInt(that.value)+"']").prop("disabled",true);
                }  
          })
        //}
        if(that.value != ""){
          jQuery(that).find("option[value="+that.value+"]").prop("disabled",false);
        }
        

        jQuery(that).attr("data-pre_value",that.value);



        if(jQuery(that).val() != ""){

           jQuery(that).parent().parent().parent().find(".location").show();
        }else{

           jQuery(that).parent().parent().parent().find(".location select").val("");

           jQuery(that).parent().parent().parent().find(".location, .sub-location, .from-time, .to-time").hide();
        }



        //jQuery(that).parent().parent().parent().find(".delete_reserve").show();

    })
    jQuery(document).on("change",".sub-location select",function(){

       let optionss = [];

       jQuery(this).find("option:selected").each(function(){
          optionss.push(this.value);
       })
       console.log(optionss)

        /*if(optionss > 0 ){

          jQuery(this).parent().find(".select2-container").find(".selection").html("Selected ("+optionss.length+")");
        }else{
          jQuery(this).parent().find(".select2-selection--multiple").html("");
        }*/
        var uldiv = jQuery(this).siblings('span.select2').find('ul')
        var count = jQuery(this).select2('data').length
        if(count==0){
          uldiv.html("")
          jQuery(this).siblings('span.select2').find(".select2-search").show();
        }
        else{
          jQuery(this).siblings('span.select2').find(".select2-search").hide();
          uldiv.html("<li>Valgt ("+count+")</li>")
        }

       let data = optionss.join(",");
       jQuery(this).parent().find("input").val(data);

    })

    jQuery(document).on("change",".location select",function(){

     // jQuery(this).parent().parent().parent().find(".location").show();

        /*var indexx = jQuery(this).parent().parent().parent().index();
        var slect_name = jQuery(this).parent().parent().parent().find(".sub-location").find("select").attr("name");
        slect_name = slect_name.replace("[]","["+indexx+"][]");
        jQuery(this).parent().parent().parent().find(".sub-location").find("select").attr("id",slect_name);*/

        let that;
        that = this;
        let datas = {
               "action" : "get_sub_location_ajax",
               "form_name" : "<?php echo $form_name;?>",
               "parent_id" : jQuery(this).val(),
               "day" : jQuery(this).parent().parent().parent().find(".day").find("select").find("option:selected").val()
            }  
        jQuery.ajax({
                  type: "POST",
                  url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                  data: datas,
                  dataType: "json",
                  success: function(resultData){

                    if(resultData.empty_sub == false){
                      jQuery(that).parent().parent().parent().find(".sub-location").find("select").html(resultData.sub_listings);
                      jQuery(that).parent().parent().parent().find(".sub-location").show();
                      if(jQuery(that).parent().parent().parent().find(".sub-location").find("select").hasClass("required1")){
                         jQuery(that).parent().parent().parent().find(".sub-location").find("select").addClass("required").removeClass("required1");
                      }
                    }else{
                      jQuery(that).parent().parent().parent().find(".sub-location").hide();
                      jQuery(that).parent().parent().parent().find(".sub-location").find("select").html("");
                      
                      if(jQuery(that).parent().parent().parent().find(".sub-location").find("select").hasClass("required")){
                         jQuery(that).parent().parent().parent().find(".sub-location").find("select").addClass("required1").removeClass("required");
                      }
                    }

                    jQuery(that).parent().parent().parent().find(".from-time").find("select").html(resultData.from_times);
                    jQuery(that).parent().parent().parent().find(".to-time").find("select").html(resultData.to_times);
                    
                    jQuery(that).parent().parent().parent().find(".from-time").show();
                    jQuery(that).parent().parent().parent().find(".to-time").show();
                    jQuery(that).parent().parent().parent().find(".from-time-loc").show();
                    jQuery(that).parent().parent().parent().find(".to-time-loc").show();

                   /* jQuery(that).parent().parent().parent().find(".sub-location").find("select").select2({
                      placeholder: 'Velg',
                      width: 'resolve',
                      dropdownAutoWidth: 'true',
                      allowClear: 'true'
                    });*/

                    //select2func();

                    

                     //  jQuery(".main_get_day_"+application_id).append(resultData.content);
                  }
            });
    })
    jQuery(document).on("click",".delete_field",function(){
       jQuery(this).parent().parent().remove();
    });
    jQuery(document).on("click",".delete_reserve",function(){

       // jQuery(this).parent().parent().parent().find(".location").show();

        let that;
        that = jQuery(this).parent().parent().parent().find(".day").find("select")[0];

        let application_id = jQuery(this).parent().parent().parent().attr("application_id");

        //if(jQuery(this).data("pre_value") != undefined){

          var previous = jQuery(that).val();
          jQuery(".main_get_day_"+application_id).find(".day").find("select").each(function(){
                jQuery(this).find("option[value='"+parseInt(previous)+"']").prop("disabled",false);
          })

       jQuery(this).parent().parent().parent().remove();

       var indexxx = 1;

        jQuery(".main_get_day_"+application_id).find(".inner-get_day").each(function(){

              jQuery(this).find(".res_count").text(indexxx);
              indexxx++;

                let res_index = jQuery(this).index();

                jQuery(this).find(".inner-advanced_fields").each(function(){

                	jQuery(this).find(".input_field").each(function(){

	                	var name_cr =  this.name;
    	                	var org_name =  jQuery(this).attr("org-name");

    	                	mySubString = name_cr.substring(
          						   name_cr.indexOf("[custom_fields][")
          						);
      						   name_cr = name_cr.replaceAll(mySubString,"");
        						name_cr = name_cr+"[custom_fields]["+res_index+"]["+org_name+"][]";
        						this.name = name_cr;
        		               
		              })
	               
	              })
        })

        hideDeleteButton();
        toastr.warning('Delete was successfull.')
        
    })
    jQuery(document).on("focus",".empty_div",function(){
        jQuery(this).removeClass("empty_div");
    })

    jQuery(document).on("change",".empty_div",function(){
        jQuery(this).removeClass("empty_div");
    })

    jQuery(".submit_form").click(function(){

      jQuery(".empty_div").removeClass("empty_div");

      let error = false;
        jQuery(".main-form-div").find(".required").each(function(){

            if(this.value == ""){
              jQuery(this).focus();
              jQuery(this).addClass("empty_div");
              jQuery(this).parent().find(".select2-container").addClass("empty_div");
              error = true;
              
            }
        })
        jQuery(".main-form-div").find("input[type=checkbox]").each(function(){

            if(jQuery(this).hasClass("required")){
                 if(this.checked == false){
                    jQuery(this).focus();
                    jQuery(this).addClass("empty_div_checkbox");
                    error = true;
                    return false;
                 }
            }
        })

        if(error == false){
          let country_code = jQuery(phonenumber).intlTelInput("getSelectedCountryData").dialCode

          country_code = "+"+country_code;
          
          jQuery(".application_form_new").append("<input type='hidden' name='country_code' value='"+country_code+"'>");
          jQuery(".application_form_new").submit();
        }else{
          toastr.warning('Ikke alle felt er utfylt. Vennligst se om alle obligatoriske felt er utfylt.')
        } 
    })
    jQuery(".save_as_draft").click(function(){

        let country_code = jQuery(phonenumber).intlTelInput("getSelectedCountryData").dialCode

        country_code = "+"+country_code;
        jQuery(".application_form_new").append("<input type='hidden' name='country_code' value='"+country_code+"'>");

        jQuery(".application_form_new").append("<input type='hidden' name='form_type' value='draft'>");

        jQuery(".application_form_new").submit(); 
      
    })
    jQuery(".main-form-div").find("input[type=checkbox]").change(function(){
       jQuery(this).removeClass("empty_div_checkbox");
    })
    jQuery(".inner-get_day").find(".sub-location").find("select").hide();
    jQuery(document).ready(function(){
        select2func();
       /*jQuery(".inner-get_day").find(".sub-location").find("select").select2({
              placeholder: 'Velg',
              width: 'resolve',
              dropdownAutoWidth: 'true',
              allowClear: 'true'
            });*/
        jQuery(".inner-get_day").find(".sub-location").find("select").change();
        //jQuery(".inner-get_day").find(".location").find("select").change();

        jQuery(document).on("click",".delete_application",function(){
             jQuery(this).parent().parent().parent().remove();
             hideDeleteButton();
             toastr.warning('Delete was successfull.')
        })
        jQuery(document).on("click","#same_user",function(){
            jQuery("#menudrpcontent").addClass("show");
            jQuery(".user_divv").addClass("focus_div")
        })
        jQuery(document).on("click",".delete_form",function(){
           
            if(confirm("Are you sure")){
              let redirect = jQuery(this).attr("data-redirect");

                let from_admin = jQuery("input[name=from_admin]").val();
                let datas = {
                   "action" : "delete_formm",
                   "application_id" : jQuery(this).attr("data-id"),
                   "from_admin" : from_admin,
                }  
                jQuery.ajax({
                          type: "POST",
                          url: "<?php echo admin_url( 'admin-ajax.php' );?>",
                          data: datas,
                          dataType: "json",
                          success: function(resultData){

                            if(resultData.success == true){
                              window.location.href = redirect;
                            }else{
                               
                            }

                             //  jQuery(".main_get_day_"+application_id).append(resultData.content);
                          }
                    });
            }    
        })
    })

    jQuery(document).on("change",".priority_listing select",function(){
        var application_id = jQuery(this).prop("application_id");

        var previous = jQuery(this).attr("data-pre_value");

        jQuery(this).parent().parent().parent().parent().find(".priority_listing select").find("option[value='"+parseInt(previous)+"']").prop("disabled",false);
        jQuery(this).parent().parent().parent().parent().find(".priority_listing select").find("option[value='"+parseInt(this.value)+"']").prop("disabled",true);

        jQuery(this).find("option[value='"+parseInt(this.value)+"']").prop("disabled",false);
       

        jQuery(this).attr("data-pre_value",this.value);
    })
    jQuery(document).on("change",".from-time select",function(){
      let this_value = this.value;
       jQuery(this).parent().parent().parent().find(".to-time select").find("option").each(function(){
            if(this_value >= this.value){
               jQuery(this).prop("disabled",true);
            }else{
               jQuery(this).prop("disabled",false);
            }
       })
       jQuery(this).parent().parent().parent().find(".to-time select").val("");
    })
    jQuery(document).on("change",".from-time-loc select",function(){
      let this_value = this.value;
       jQuery(this).parent().parent().parent().find(".to-time-loc select").find("option").each(function(){
            if(this_value >= this.value){
               jQuery(this).prop("disabled",true);
            }else{
               jQuery(this).prop("disabled",false);
            }
       })
       jQuery(this).parent().parent().parent().find(".to-time-loc select").val("");
    })

    hideDeleteButton();

    jQuery(document).ready(function(){

        hideDeleteButton();


        jQuery(".priority_listing").find("select").each(function(){
           jQuery(this).change();
        })
        jQuery(".day").find("select").each(function(){
           jQuery(this).change();
        })
        jQuery(document).on("keypress","input", function(){
            jQuery(this).addClass('green_border')
        })

        jQuery(document).on("change","input", function(){
            jQuery(this).removeClass('green_border')
        })
        jQuery(document).on("click", function(){
            jQuery("input").removeClass('green_border')
        })
        setPickerValues();

        select2func();

       /* jQueryy(document).on("click",".select2-container",function(){
            setTimeout(function(){
              debugger;
               jQuery(".select2-search--dropdown").find("input").focus()
            },500)
        })*/
        jQuery(document).on('select2:open', (e) => {
            const selectId = e.target.id

            jQuery(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
                key,
                value,
            ){
                value.focus();
            })
        })

        jQuery(document).on("click",".tooltip_main .tip_app",function(e){
            if(jQuery(e.target).closest(".close-tip").length === 0){
              jQuery(this).addClass("open");
            }
        })
        jQuery(document).on("click",".tooltip_main .close-tip",function(){
            jQuery(".tip_app").removeClass("open");
        })

        jQuery(document).on('click', function (e) {

            if (jQuery(e.target).closest(".tooltip_main").length === 0 && jQuery(e.target).closest("input").length === 0 && jQuery(e.target).closest(".tip-content").length === 0) {
                jQuery(".tip_app").removeClass("open");
            }

            
        });
        jQuery(".tip-content").find("a").attr("target","_blank");

        

        
    })

   

    
</script>