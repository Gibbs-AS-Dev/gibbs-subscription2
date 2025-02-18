<?php



function top_section(){
	$user_data = get_user_by("ID",get_current_user_id());



	$metas = get_user_meta(get_current_user_id());

	$country_code = str_replace("+", "", $user_data->country_code);

	if($country_code != ""){
		$country_code = "+".$country_code;
	}

	global $json_data;
	global $group_id;
	global $season_id;

	if(isset($_GET["admin"]) && $_GET["admin"] == "true"){
	   $readonly = "readonly";
	}else{
       $readonly = "";
	}

	$about_fields = (isset($json_data['about_fields'])) ? $json_data['about_fields'] : array();




	$top_section = array(
			'applicant' => array(
				'title' 	=> __('Generell informasjon','gibbs_core'),
				'class' 	=> '',
				'icon' 		=> 'sl sl-icon-doc',
				'delete_form' => array(
						'label'       => __( 'Slett hele søknaden', 'gibbs_core' ),
						'type'        => 'button',
						'name'        => 'delete_form',
						'class'		  => 'col-md-6',
						'redirect'		  => home_url()."/mine-soknader",
						'redirect-admin'		  => home_url()."/administrer-soknader",
				),
				'save_as_draft' => array(
						'label'       => __( 'Lagre som utkast', 'gibbs_core' ),
						'type'        => 'button',
						'name'        => 'save_as_draft',
						'class'		  => 'col-md-6',
						'redirect'		  => home_url()."/mine-soknader",
						'redirect-admin'		  => home_url()."/administrer-soknader"
				),
				/*fields*/
				'fields' 	=> array(
					/*row start */
						'rows'  => array(
							        array(
									    'new_org_message' => array(
											'type'        => 'new_org_message',
											'name'       => 'new_org_message',
											'class'		  => 'col-md-12',
											
										),
										/* 'heading_with_hr' => array(
											'label'        => __( 'About', 'gibbs_core' ),
											'type'        => 'heading_with_hr',
											'name'       => 'heading_with_hr',
											'class'		  => 'col-md-12',
										), */
									),
							        array(
							        	'for_company' => array(
											'type'        => 'for_company',
											'fields'		  => array(

												'first_name' => array(
													'label'       => __( 'Organization/Name', 'gibbs_core' ),
													'type'        => 'text',
													'name'       => 'first_name',
													'tooltip'	  => __( 'Organization/Name', 'gibbs_core' ),
													'important'	  => __( 'Organization/Name', 'gibbs_core' ),
													'required'    => true,
													'placeholder' => '',
													'class'		  => 'col-md-6',
													'value'  => (isset($json_data['first_name'])) ? $json_data['first_name'] : $user_data->first_name
													
												),
												'company_number' => array(
													'label'       => __( 'Org number', 'gibbs_core' ),
													'type'        => 'text',
													'name'       => 'company_number',
													'tooltip'	  => __( 'Org number', 'gibbs_core' ),
													'important'	  => __( 'Org number', 'gibbs_core' ),
													'required'    => true,
													'placeholder' => '',
													'class'		  => 'col-md-6',
													'value'  => (isset($json_data['company_number'])) ? $json_data['company_number'] : $user_data->company_number
												),
												
												'applicant_type' => array(
													'label'       => __( 'Type søker', 'gibbs_core' ),
													'type'        => 'select',
													'name'       => 'applicant_type',
													'tooltip'	  => __( 'Velg om du søker fra kommunen eller høyskolen', 'gibbs_core' ),
													'required'    => true,
													'multiple'   => false,
													'type_select'   => "without_search",
													'placeholder' => '',
													'class'		  => 'col-md-4',
													'options'     =>  get_applicant_type($group_id),
													'selected'     => (isset($json_data['applicant_type'])) ? $json_data['applicant_type'] : ""
												),
												

											),
											
										),
										'for_private' => array(
											'type'        => 'for_private',
											'fields'		  => array(

												'first_name' => array(
													'label'       => __( 'Fornavn Bestiller/Emneansvarlig', 'gibbs_core' ),
													'type'        => 'text',
													'name'       => 'first_name',
													'tooltip'	  => __( 'Navnet for personen som er ansvarlig for hele søknaden', 'gibbs_core' ),
													'required'    => true,
													'placeholder' => '',
													'class'		  => 'col-md-4',
													'value'  => (isset($json_data['first_name'])) ? $json_data['first_name'] : $user_data->first_name
													
												),
												'last_name' => array(
													'label'       => __( 'Etternavn', 'gibbs_core' ),
													'type'        => 'text',
													'name'       => 'last_name',
/* 													'tooltip'	  => __( 'Person Last name', 'gibbs_core' ),
													'important'	  => __( 'Person Last name', 'gibbs_core' ), */
													'required'    => true,
													'placeholder' => '',
													'class'		  => 'col-md-4',
													'value'  => (isset($json_data['last_name'])) ? $json_data['last_name'] : $user_data->last_name
													
												),
												'applicant_type' => array(
													'label'       => __( 'Type søker', 'gibbs_core' ),
													'type'        => 'select',
													'name'       => 'applicant_type',
													'tooltip'	  => __( 'Velg om du søker fra kommunen eller høyskolen', 'gibbs_core' ),
													'required'    => true,
													'multiple'   => false,
													'type_select'   => "without_search",
													'placeholder' => '',
													'class'		  => 'col-md-4',
													'options'     =>  get_applicant_type($group_id),
													'selected'     => (isset($json_data['applicant_type'])) ? $json_data['applicant_type'] : ""	
												),

											),
											
										),
									),array(
										    'phone' => array(
												'label'       => __( 'Telefon', 'gibbs_core' ),
												'type'        => 'tel',
												'name'       => 'phone',
/* 												'tooltip'	  => __( 'Tlf', 'gibbs_core' ),
												'important'	  => __( 'Tlf', 'gibbs_core' ), */
												'required'    => true,
												'placeholder' => '',
												'class'		  => 'col-md-6',
												'value'  => (isset($json_data['phone'])) ? $json_data['country_code'].$json_data['phone'] : $country_code.$user_data->phone
												
											),
											'email' => array(
												'label'       => __( 'E-post', 'gibbs_core' ),
												'type'        => 'email',
												'name'       => 'email',
/* 												'tooltip'	  => __( 'Email', 'gibbs_core' ), */
												'required'    => true,
												'placeholder' => '',
												'class'		  => 'col-md-6',
												'value'  => (isset($json_data['email'])) ? $json_data['email'] : $user_data->user_email,
												"attribute" => $readonly,
												
											),
									),/* array(
										    'address' => array(
												'label'       => __( 'Address', 'gibbs_core' ),
												'type'        => 'text',
												'name'       => 'address',
												'tooltip'	  => __( 'Address', 'gibbs_core' ),
												'required'    => false,
												'placeholder' => '',
												'class'		  => '',
												'priority'    => 1,
												'class'		  => 'col-md-4',
												'value'  => (isset($json_data['address'])) ? $json_data['address'] : $user_data->billing_address_1." ".$user_data->billing_address_2
												
											),
											'zip' => array(
												'label'       => __( 'Post no', 'gibbs_core' ),
												'type'        => 'text',
												'name'       => 'zip',
												'tooltip'	  => __( 'Post no', 'gibbs_core' ),
												'required'    => false,
												'placeholder' => '',
												'class'		  => 'col-md-4',
												'value'  => (isset($json_data['zip'])) ? $json_data['zip'] : $user_data->billing_postcode
												
											),
											'city' => array(
												'label'       => __( 'City', 'gibbs_core' ),
												'type'        => 'text',
												'name'       => 'city',
												'tooltip'	  => __( 'Item no', 'gibbs_core' ),
												'required'    => false,
												'placeholder' => '',
												'class'		  => 'col-md-4',
												'value'  => (isset($json_data['city'])) ? $json_data['city'] : $user_data->billing_city
												
											),
									), */	
							),					
					/*row end */	/*row end */	
				    /*ad rows*/
				       "aditional_rows" => additional_about_fields($group_id, $about_fields),	
				    /*end ad rows*/

				)
				/*end fields*/
			),
		);

	return $top_section;
}
function add_button_section(){
	$add_button_section = array(
			
			'add_new_section' => array(
					'label'       => __( 'Legg til ny søknad', 'gibbs_core' ),
					'type'        => 'add_new_section',
					'name'        => 'add_new_section',
					"attribute"   => "onClick='add_new_application(this)'"
			),
		);

	return $add_button_section;
}
function bottom_section(){
	$bottom_section = array(
			'accept_term' => array(
					'label'       => __( 'Jeg godkjenner bruker vilkår og betingelser', 'gibbs_core' ),
					'type'        => 'checkbox',
					'name'       => 'term_and_condition',
					'required'    => true,
					'placeholder' => '',
					'class'		  => 'col-md-12',
			),
			'save_as_draft' => array(
					'label'       => __( 'Lagre som utkast', 'gibbs_core' ),
					'type'        => 'button',
					'name'        => 'save_as_draft',
					'class'		  => 'col-md-6',
					'redirect'		  => home_url()."/mine-soknader",
					'redirect-admin'		  => home_url()."/administrer-soknader"
			),
			'save' => array(
					'label'       => __( 'Send', 'gibbs_core' ),
					'type'        => 'button',
					'name'        => 'submit',
					'class'		  => 'col-md-6',
			),
		);

	return $bottom_section;
}

function get_application($count,$group_id,$season_id){

	global $json_data;

	$app_data = array();

	if(isset($json_data["application"][$count]) && isset($json_data["application"][$count]["team-name"])){
        $app_data = $json_data["application"][$count];
	}




	$top_section = array(
			'application'.$count => array(
				'title' 	=> __('Søknad #'.$count,'gibbs_core'),
				'class' 	=> '',
				'icon' 		=> 'sl sl-icon-doc',
				/*fields*/
				'fields' 	=> array(
					/*row start */
						'rows'  => array ( 
							    array(
									'level' => array(
										'label'       => __( 'Avdeling eller emne kode', 'gibbs_core' ),
										'type'        => 'select',
										'name'       => "application[$count][level]",
										/* 'tooltip'	  => __( 'Noe relevant og informativt', 'gibbs_core' ), */
										'required'    => true,
										'multiple'   => false,
										'type_select'   => "with_search",
										'placeholder' => '',
										'class'		  => 'col-md-6',
										'options'     => get_levels($group_id),
										'selected'     => (isset($app_data['level'])) ? $app_data['level'] : ""
									),
									'sports' => array(
										'label'       => __( 'Aktivitet', 'gibbs_core' ),
										'type'        => 'select',
										'name'       => "application[$count][sports]",
										/* 'tooltip'	  => __( 'Sports', 'gibbs_core' ), */
										'required'    => true,
										'multiple'   => false,
										'select_and_hide'        => true,
										'type_select'   => "with_search",
										'placeholder' => '',
										'class'		  => 'col-md-6',
										'options'     => get_sports($group_id),
										'selected'     => (isset($app_data['sports'])) ? $app_data['sports'] : ""
									),
									'member_count' => array(
										'label'       => __( 'Antall personer', 'gibbs_core' ),
										'type'        => 'number',
										'name'       => "application[$count][member_count]",
										'tooltip'	  => __( 'Legg inn estimert antall personer som skal bruke disse reservasjonene', 'gibbs_core' ),
										'required'    => true,
										'placeholder' => '',
										'class'		  => 'col-md-6',
										'value'  => (isset($app_data['member_count'])) ? $app_data['member_count'] : ""
									),

								    'team-name' => array(
										'label'       => __( 'Faglærer/Dagsansvarlig', 'gibbs_core' ),
										'type'        => 'text',
										'name'       => "application[$count][team-name]",
										'tooltip'	  => __( 'Navnet på personen som skal være tilstede på alle reservasjonene til denne søknaden' ),
										'required'    => true,
										'placeholder' => '',
										'class'		  => 'col-md-6',
										'value'  => (isset($app_data['team-name'])) ? $app_data['team-name'] : ""
									),
								) ,array(
									'age' => array(
										'label'       => __( 'Age', 'gibbs_core' ),
										'type'        => 'select',
										'name'       => "application[$count][age]",
										'select_and_hide'        => true,
										'tooltip'	  => __( 'Age', 'gibbs_core' ),
										'required'    => true,
										'multiple'   => false,
										'type_select'   => "with_search",
										'placeholder' => '',
										'class'		  => 'col-md-4',
										'options'     => get_age($group_id),
										'selected'     => (isset($app_data['age'])) ? $app_data['age'] : ""
										),
								) ,array(
									   "aditional_rows" => additional_application_fields($group_id, $count, $app_data),	
								),
								array(
									    'custom_text' => array(
											'label'       => __( '', 'gibbs_core' ),
											'type'        => 'custom_text',
											'class'		  => 'col-md-12',
										),
								),array(
									    'get_day' => array(
											'type'        => 'get_day',
											'application_id' => $count,
											'values' => get_days($count,$group_id,$season_id,$app_data,1),
											'class'		  => 'col-md-12',
										)
								),array(
									    'add_new_day' => array(
											'label'       => __( 'Add new day', 'gibbs_core' ),
											'type'        => 'add_new_day',
											'class'		  => 'col-md-12',
										),
								),array(
									    'add_reservation' => array(
												'label'       => __( 'Legg til ny reservasjon', 'gibbs_core' ),
												'type'        => 'add_reservation',
												'name'        => 'add_reservation',
												"attribute"   => "onClick='add_reservation(this,".$count.")' application_id='".$count."'",
												"class"       =>  "col-md-12 add_reservation_cls",
										),
								)/* ,array(
									    'custom_text' => array(
											'label'       => __( 'Prioritize locations', 'gibbs_core' ),
											'type'        => 'custom_text',
											'class'		  => 'col-md-12',
											
										),
								),array(
									    'pri-1' => array(
											'label'       => __( 'Priority 1', 'gibbs_core' ),
											'type'        => 'select',
											'name'       => "application[$count][pri-1]",
											'tooltip'	  => __( 'Priority 1', 'gibbs_core' ),
											'required'    => true,
											'multiple'   => false,
				                            'type_select'   => "with_search",
											'placeholder' => '',
											'class'		  => 'col-md-4 priority_listing',
											"attribute"   => "application_id='".$count."'",
											'options'     => get_locations_data($group_id),
											'selected'     => (isset($app_data['pri-1'])) ? $app_data['pri-1'] : ""
											
										),
										'pri-2' => array(
											'label'       => __( 'Priority 2', 'gibbs_core' ),
											'type'        => 'select',
											'name'       => "application[$count][pri-2]",
											'tooltip'	  => __( 'Priority 2', 'gibbs_core' ),
											'required'    => true,
											'multiple'   => false,
				                            'type_select'   => "with_search",
											'placeholder' => '',
											'class'		  => 'col-md-4 priority_listing',
											"attribute"   => "application_id='".$count."'",
											'options'     => get_locations_data($group_id),
											'selected'     => (isset($app_data['pri-2'])) ? $app_data['pri-2'] : ""
											
										),
										'pri-3' => array(
											'label'       => __( 'Priority 3', 'gibbs_core' ),
											'type'        => 'select',
											'name'       => "application[$count][pri-3]",
											'tooltip'	  => __( 'Priority 3', 'gibbs_core' ),
											'required'    => true,
											'multiple'   => false,
				                            'type_select'   => "with_search",
											'placeholder' => '',
											'class'		  => 'col-md-4 priority_listing',
											"attribute"   => "application_id='".$count."'",
											'options'     => get_locations_data($group_id),
											'selected'     => (isset($app_data['pri-3'])) ? $app_data['pri-3'] : ""
											
										),
								) *//* ,array(
									    'comments' => array(
											'label'       => __( 'Kommentar til søknad (ikke obligtorisk)', 'gibbs_core' ),
											'type'        => 'textarea',
											'name'       => "application[$count][comments]", */
											/* 'tooltip'	  => __( 'Comment to your application', 'gibbs_core' ), */
										/* 	'required'    => false,
											'placeholder' => '',
											'class'		  => 'col-md-12',
											'value'       => (isset($app_data['comments'])) ? $app_data['comments'] : ""
											
										),
								) */
							)					
					/*row end */	

				),
				/*end fields*/
			),
		);

	return $top_section;
}

function get_days($count,$group_id,$season_id,$data = array(),$index=""){

	$reservations_section = array();

	$get_app_fields_data = get_app_fields($group_id);

	$get_app_fields_exist = 0;

	if(empty($get_app_fields_data )){
		$get_app_fields_exist  = 1;
	}

	$exist_res_field = 0;

	foreach ($get_app_fields_data as $key => $get_app_fie) {
		if($get_app_fie["field_position"] == "reservation"){
				$exist_res_field = 1;
		}
	}
	if($exist_res_field == 0){
		$get_app_fields_exist  = 1;
	}



	if(isset($data["reservations"]) && !empty($data["reservations"])){



		foreach ($data["reservations"] as $key => $reservation) {


			$get_days = array(
			   	        'from_date'.$count => array(
							'label'       => __( 'Fra dato', 'gibbs_core' ),
							'type'        => 'date',
							'id'        => "from_date_".$count."_".$key,
							'name'       => "application[$count][reservations][from_date][]",
							/* 'tooltip'	  => __( 'Select From date', 'gibbs_core' ), */
							'required'    => true,
							/* 'placeholder' => '', */
							'class'		  => 'from_date col-md-2',
							'attribute'   => "application_id='".$count."'",
							'value'     => (isset($reservation['from_date'])) ? $reservation['from_date'] : "",
							
						),
						'to_date' . $count => array(
							'label'       => __( 'Til dato', 'gibbs_core' ),
							'type'        => 'date',
							'id'          => "to_date_" . $count . "_" . $index,
							'name'        => "application[$count][reservations][to_date][]",
							'hide'        => false,
							'required'    => true,
							'placeholder' => '',
							'class'       => 'to_date col-md-2',
							'attribute'   => "application_id='".$count."'",
							'value'     => (isset($reservation['to_date'])) ? $reservation['to_date'] : "",
							'style'       => 'display: none; !important', // Add this line to hide the element
						)
						,
						'location'.$count => array(
							'label'       => __( 'Rom', 'gibbs_core' ),
							'type'        => 'select',
							'id'        => "location_".$count."_".$key,
							'name'       => "application[$count][reservations][location][]",
							'hide'       => false,
							/* 'tooltip'	  => __( 'Location', 'gibbs_core' ), */
							'important'	  => __( 'Bestill kun den tid du skal benytte rommet. Overbooking vil bli dobbelt fakturert PRIVAT :)', 'gibbs_core' ),
							'required'    => true,
							'multiple'   => true,
				            'type_select'   => "with_search",
							'placeholder' => '',
							'class'		  => 'location col-md-4',
							'options'     => get_locations_data($group_id),
							'selected'     => (isset($reservation['location'])) ? $reservation['location'] : "",
							
						),
						'from-time'.$count => array(
							'label'       => __( 'Fra klokka', 'gibbs_core' ),
							'type'        => 'select',
							'id'        => "from-time_".$count."_".$key,
							'name'       => "application[$count][reservations][from-time][]",
							'hide'       => false,
							/* 'tooltip'	  => __( 'From time', 'gibbs_core' ), */
							'required'    => true,
							'multiple'   => false,
				            'type_select'   => "without_search",
							'placeholder' => '',
							'class'		  => 'from-time-loc col-md-2',
							'options'     => get_times_form_2(),
							'selected'     => (isset($reservation['from-time'])) ? $reservation['from-time'] : "",
							
						),
						'to-time'.$count => array(
							'label'       => __( 'Til klokka', 'gibbs_core' ),
							'type'        => 'select',
							'id'        => "to-time_".$count."_".$key,
							'name'       => "application[$count][reservations][to-time][]",
							'hide'       => false,
							/* 'tooltip'	  => __( 'To time', 'gibbs_core' ), */
							'required'    => true,
							'multiple'   => false,
				            'type_select'   => "without_search",
							'placeholder' => '',
							'class'		  => 'to-time-loc col-md-2',
							'options'     => get_times_form_2(),
							'selected'     => (isset($reservation['to-time'])) ? $reservation['to-time'] : "",
							
						)
			    );

			$get_advanced_fields = array();

			foreach ($reservation["custom_fields"] as $key_index => $fieldss) {
				$get_advanced_fields[] = advanced_fields($count,$group_id,$key,$fieldss,$key_index);
			}
			$inc_index = $key + 1;


			$reservations_section[] = array(
										'reservations'.$key => array(
												'title' 	=> __('Reservasjon #<span class="res_count">'.$inc_index .'</span>','gibbs_core'),
												'class' 	=> '',
												'icon' 		=> 'sl sl-icon-doc',
												/*fields*/
												'fields' 	=> $get_days,
												'advanced_fields' 	=> $get_advanced_fields,
												'add_fields_button' =>  array(array(
														'label'       => __( 'Legg til ressurs', 'gibbs_core' ),
														'type'        => 'custom_button',
														'name'        => 'add_fields',
														'hide'        => $get_app_fields_exist,
														"attribute"   => "onClick='add_fields(this,".$count.")' application_id='".$count."'",
														"class"       =>  "col-md-12 add_fields_cls",
												)),
												/*end fields*/
											),
									);
			
		}
		
	}else{
   
	    $get_days = array(
	   	    'from_date'.$count => array(
				'label'       => __( 'Dato', 'gibbs_core' ),
				'type'        => 'date',
				'id'        => "from_date_".$count."_".$index,
				'name'       => "application[$count][reservations][from_date][]",
				/* 'tooltip'	  => __( 'Select From date', 'gibbs_core' ), */
				'required'    => true,
				'placeholder' => '',
				'class'		  => 'from_date col-md-2',
				'attribute'   => "application_id='".$count."'",
				
			),
			'to_date' . $count => array(
				'label'       => __( 'Til dato', 'gibbs_core' ),
				'type'        => 'date',
				'id'          => "to_date_" . $count . "_" . $index,
				'name'        => "application[$count][reservations][to_date][]",
				'hide'        => false,
				'required'    => true,
				'placeholder' => '',
				'class'       => 'to_date col-md-2',
				'style'       => 'display: none;', // Add this line to hide the element
			)
			,
			'location'.$count => array(
				'label'       => __( 'Rom', 'gibbs_core' ),
				'type'        => 'select',
				'id'        => "location_".$count."_".$index,
				'name'       => "application[$count][reservations][location][]",
				'hide'       => false,
				/* 'tooltip'	  => __( 'Location', 'gibbs_core' ), */
				'important'	  => __( 'Bestill kun den tid du skal benytte rommet. Overbooking vil bli dobbelt fakturert PRIVAT :)', 'gibbs_core' ),
				'required'    => true,
				'multiple'   => true,
				'type_select'   => "with_search",
				'placeholder' => '',
				'class'		  => 'location col-md-4',
				'options'     => get_locations_data($group_id)
					
			),
			'from-time'.$count => array(
				'label'       => __( 'Fra klokka', 'gibbs_core' ),
				'type'        => 'select',
				'id'        => "from-time_".$count."_".$index,
				'name'       => "application[$count][reservations][from-time][]",
				'hide'       => true,
				/* 'tooltip'	  => __( 'From time', 'gibbs_core' ), */
				'required'    => true,
				'multiple'   => false,
				'type_select'   => "without_search",
				'placeholder' => '',
				'class'		  => 'from-time-loc col-md-2',
				'options'     => get_times_form_2()
				
			),
			'to-time'.$count => array(
				'label'       => __( 'Til klokka', 'gibbs_core' ),
				'type'        => 'select',
				'id'        => "to-time_".$count."_".$index,
				'name'       => "application[$count][reservations][to-time][]",
				'hide'       => true,
				/* 'tooltip'	  => __( 'To time', 'gibbs_core' ), */
				'required'    => true,
				'multiple'   => false,
				'type_select'   => "without_search",
				'placeholder' => '',
				'class'		  => 'to-time-loc col-md-2',
				'options'     => get_times_form_2()
				
			)

		);	

		$reservations_section[] = array(
										'reservations'.$index => array(
												'title' 	=> __('Reservasjon #<span class="res_count">'.$index.'</span>','gibbs_core'),
												'class' 	=> '',
												'icon' 		=> 'sl sl-icon-doc',
												/*fields*/
												'fields' 	=> $get_days,
												'custom_fields' => array(), 
												'advanced_fields' 	=> array(), 
												'add_fields_button' =>  array(array(
														'label'       => __( 'Legg til ressurs', 'gibbs_core' ),
														'type'        => 'custom_button',
														'name'        => 'add_fields',
														'hide'        => $get_app_fields_exist,
														"attribute"   => "onClick='add_fields(this,".$count.")' application_id='".$count."'",
														"class"       =>  "col-md-12 add_fields_cls",
												)),
												/*end fields*/
											),
									);
	}    

	
	
    return $reservations_section;
}



function success_section(){
	global $wpdb;

	$application_data_table = 'application_data';  // table name

	$deadline = "";

	if(isset($_GET["application_id"])){
		$current_user_id = get_current_user_ID();

		$sql_application_data = "select *  from `$application_data_table` where user_id = $current_user_id AND id=".$_GET['application_id'];
		$application_data = $wpdb->get_row($sql_application_data);
		if(isset($application_data->id)){
           $deadline = $application_data->deadline;
		}
	}


	$login_section = array(
			'top_text' => array(
				'title' 	=> __('Takk for din søknad! :)','gibbs_core'),
				'class' 	=> '',
				/*fields*/
				'fields' 	=> array(
					/*row start */
						'rows'  => array(
							        array(
									    'custom_text' => array(
											/* 'label'       => __( 'Skjema er sendt og mottatt. Husk at du kan gjøre endringer helt frem til '.$deadline.".", 'gibbs_core' ), */
											'type'        => 'custom_text',
											'class'		  => 'col-md-12 mb-10',
											
										),
									),array(
										'custom_button' => array(
											'label'       => __( 'Se dine søknader', 'gibbs_core' ),
											'type'        => 'custom_button',
											'class'		  => 'col-md-12',
											'link'		  => home_url()."/mine-soknader",
										),
									)
							)					
					/*row end */	

				)
				/*end fields*/
			),
		);

	return $login_section;
}
function seasonnotfound(){
	$season_not_found = array(
			'top_text' => array(
				'title' 	=> __('Send søknad','gibbs_core'),
				'class' 	=> '',
				/*fields*/
				'fields' 	=> array(
					/*row start */
						'rows'  => array(
							        array(
									    'custom_text' => array(
											'label'       => __( 'Du har allerede sendt en søknad for denne fordelingen', 'gibbs_core' ),
											'type'        => 'custom_text_info',
											'class'		  => 'col-md-12',
											
										),
									),array(
										'custom_button' => array(
											'label'       => __( 'Se dine søknader', 'gibbs_core' ),
											'type'        => 'custom_button',
											'class'		  => 'col-md-12',
											'link'		  => home_url()."/mine-soknader",
										),
									)
							)					
					/*row end */	

				)
				/*end fields*/
			),
		);

	return $season_not_found;
}
function pdfText(){
	$pdftext = array(
			'title' 	=> __('Kvittering på søknad','gibbs_core'),
			'class' 	=> '',
			/* 'description' 	=> 'Takk for din søknad. Denne skal behandles etter predefinerte regler. Når du ankommer, så må du gå inn ved resepsjonen eller..', */
		);

	return $pdftext;
}


function get_times_form_2($match=''){

	/*$start = '00:00';
	$end = '24:00';
	$date_end = date_create($end);*/

	$times = array();


	$times["07:00"] = "07:00";
	$times["07:15"] = "07:15";
	$times["07:30"] = "07:30";
	$times["07:45"] = "07:45";
	$times["08:00"] = "08:00";
	$times["08:15"] = "08:15";
	$times["08:30"] = "08:30";
	$times["08:45"] = "08:45";
	$times["09:00"] = "09:00";
	$times["09:15"] = "09:15";
	$times["09:30"] = "09:30";
	$times["09:45"] = "09:45";
	$times["10:00"] = "10:00";
	$times["10:15"] = "10:15";
	$times["10:30"] = "10:30";
	$times["10:45"] = "10:45";
	$times["11:00"] = "11:00";
	$times["11:15"] = "11:15";
	$times["11:30"] = "11:30";
	$times["11:45"] = "11:45";
	$times["12:00"] = "12:00";
	$times["12:15"] = "12:15";
	$times["12:30"] = "12:30";
	$times["12:45"] = "12:45";
	$times["13:00"] = "13:00";
	$times["13:15"] = "13:15";
	$times["13:30"] = "13:30";
	$times["13:45"] = "13:45";
	$times["14:00"] = "14:00";
	$times["14:15"] = "14:15";
	$times["14:30"] = "14:30";
	$times["14:45"] = "14:45";
	$times["15:00"] = "15:00";
	$times["15:15"] = "15:15";
	$times["15:30"] = "15:30";
	$times["15:45"] = "15:45";
	$times["16:00"] = "16:00";
	$times["16:15"] = "16:15";
	$times["16:30"] = "16:30";
	$times["16:45"] = "16:45";
	$times["17:00"] = "16:00";
	$times["17:15"] = "17:15";
	$times["17:30"] = "17:30";
	$times["17:45"] = "17:45";
	$times["18:00"] = "18:00";
	$times["18:15"] = "18:15";
	$times["18:30"] = "18:30";
	$times["18:45"] = "18:45";
	$times["19:00"] = "19:00";
	$times["19:15"] = "19:15";
	$times["19:30"] = "19:30";
	$times["19:45"] = "19:45";
	$times["20:00"] = "20:00";
	$times["20:15"] = "20:15";
	$times["20:30"] = "20:30";
	$times["20:45"] = "20:45";
	$times["21:00"] = "21:00";
	$times["21:15"] = "21:15";
	$times["21:30"] = "21:30";
	$times["21:45"] = "21:45";
	$times["22:00"] = "22:00";

	/*for($date = date_create($start);$date <= $date_end; $date->modify('+30 Minutes')){

	    $times[$date->format('H:i')] = $date->format('H:i');
	}*/

	return $times;

	
}