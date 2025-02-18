<div id="add-customer-popup" class="event_popup_main">

     <form id="customerForm" method="post" action="javascript:void(0)">

        <input type="hidden" name="action" value="addEventCustomer">

        <div class="top-info-div">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-fields">
                       <h4>Legg til ny kunde</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-fields close_popup_field">
                        <span class="close_customer_popup"><i class="fa fa-times"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="middle-info-div">
            <div class="row">
                <div class="col-md-12">
                    <div class="show_info_div">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-fields">
                        <label>E-post *</label>
                        <div class="email_divs">
                           <input type="email" class="customer_email" name="email" required>
                           <button type="button" class="btn btn-primary gibbs-btn customer_email_btn">Søk</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 extra_customer_div">
                    <div class="form-fields">
                        <label>Tlf *</label>
                        <input type="text" name="phone" id="customer_phone" required>
                        <input type="hidden" name="country_code" value="+47" required>
                    </div>
                </div>
            </div>
            <div class="extra_customer_div">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>Fornavn *</label>
                            <input type="text" name="first_name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>Etternavn *</label>
                            <input type="text" name="last_name" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-fields">
                            <h4>Kunde faktura informasjon</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-fields">
                            <label>Type </label>
                            <select name="profile_type" class="profile_type">
                                <option value="Personal">Privatperson</option>
                                <option value="Company">Bedrift/organisasjon</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>Navn</label>
                            <input type="text" name="billing_name">
                        </div>
                    </div>
                    <div class="col-md-6 org_div" style="display:none">
                        <div class="form-fields">
                            <label>Organisasjon nummer</label>
                            <input type="text" name="organization_number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>E-post</label>
                            <input type="email" name="billing_email">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>Tlf </label>
                            <input type="text" id="billing_phone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>By</label>
                            <input type="text" name="billing_city">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>Adresse</label>
                            <input type="text" name="billing_address1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-fields">
                            <label>Post nr</label>
                            <input type="text" name="billing_postcode">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="event_popup_btn_main">
                        <div class="col-md-6 event_popup_left_btn">
                        </div>
                        <div class="col-md-6 event_popup_right_btn">
                            <button class="btn btn-primary close_btn close_customer_popup" id="popup-customer-close">Avbryt</button>
                            <button class="btn btn-primary submit_btn" type="submit">Lagre</button>
                        </div>
                    </div>
                </div>
            </div>    
        </div>

    </form>
    <div class="overlay" style="display: none;">
        <div class="overlay__inner">
            <div class="overlay__content"><span class="spinner"></span></div>
        </div>
    </div>

</div>


<script type="text/javascript">
    jQuery(".profile_type").change(function(){
        if(this.value == "Company"){
            jQuery('.org_div').show();
        }else{
            jQuery('.org_div').hide();
        }
    })
    var customer_phone = document.getElementById("customer_phone");
    let customer_phone_func =  jQuery(customer_phone).intlTelInput({
                              initialCountry: "NO",
                              separateDialCode: true,
                              hiddenInput: "full",
                              preferredCountries: ["NO"],
                              utilsScript:
                                "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.14/js/utils.js",
                            });
    var billing_phone = document.getElementById("billing_phone");
    let billing_phone_func =  jQuery(billing_phone).intlTelInput({
                              initialCountry: "NO",
                              separateDialCode: true,
                              hiddenInput: "billing_phone",
                              preferredCountries: ["NO"],
                              utilsScript:
                                "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.14/js/utils.js",
                            });
   

    
</script>