var prisText = "", position = 0, prisAktivert = false, topKategoriForSøk = null, subTopCat = [], totalFilters = 0, searchWordCounted;

/* override and disable listeo-core func */
function mmenuInit() { };

/* Start of slide-show funcs */
function plusSlides(n, querySelector) {
    var dots = document.querySelectorAll("#dots-" + querySelector + " .dot");
    var numActive = 0;
    for(var i = 0; i < dots.length; i++){
        if(dots[i].classList.contains("active")) {
            numActive = i+1;
            break;
        }
    }
  showSlides(numActive += n, querySelector);
}

function currentSlide(n, querySelector) {
  showSlides(n, querySelector);
}

function showSlides(n, querySelector) {
  var i;
  var slides = document.querySelectorAll("#id-" + querySelector + " img");
  var dots = document.querySelectorAll("#dots-" + querySelector + " .dot");

  if (n > slides.length) {n = 1}
  if (n < 1) {n = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[n-1].style.display = "block";
  dots[n-1].className += " active";
}

/* set mobile swipe functionality */
function setSwipeFunctionality(){

  var touchstartX = 0, touchendX = 0;

  document.querySelectorAll('.listing-item-image .search_slide a').forEach(item => { 
    if(item.children.length > 1){
      item.querySelectorAll('img').forEach(img => {
          img.addEventListener('touchstart', event => {
          touchstartX = event.changedTouches[0].screenX;
        }, false);
          img.addEventListener('touchend', event => {
          touchendX = event.changedTouches[0].screenX;
            // true = next, false = prev
            if((touchendX - touchstartX) > 50 || (touchendX - touchstartX) < -50){
              var dir = (touchendX < touchstartX) ? 1 : -1;
              plusSlides(dir,img.parentNode.parentNode.id.slice(3));
            }
        }, false);
      });
    }
  });
}
/* re-calls the func when ajax call is complete (triggered when loading in new search results) */
jQuery(document).ready(function(){
  setSwipeFunctionality();
  document.querySelectorAll('.listings-container nav.pagination ul.pagination li').forEach(li => {
    li.addEventListener('click', e => {
      jQuery(document).ajaxComplete(function(){
        setSwipeFunctionality();
      })
    });
  });

  // if archive-listing page
  if(jQuery('#mainFilters').length > 0){
    onloadArchiveListingPage();
  } else {
    featuresInEditOrAddListingFix();
  }
  
});
/* End of slide-show funcs */


jQuery( function( $ ) {
    var $datepickers = $( '#_event_date-panel' ).find( '[data-type="date"]' );

    $datepickers.daterangepicker({
        timePicker: false,
        singleDatePicker: true,
        startDate: moment.now(),
        locale: {
            format: wordpress_date_format.date,
            'firstDay'    : parseInt(wordpress_date_format.day),
            'applyLabel'  : listeo_core.applyLabel,
            'cancelLabel' : listeo_core.clearLabel,
            'fromLabel'   : listeo_core.fromLabel,
            'toLabel'   : listeo_core.toLabel,
            'customRangeLabel': listeo_core.customRangeLabel,
            'daysOfWeek': [
                listeo_core.day_short_su,
                listeo_core.day_short_mo,
                listeo_core.day_short_tu,
                listeo_core.day_short_we,
                listeo_core.day_short_th,
                listeo_core.day_short_fr,
                listeo_core.day_short_sa
            ],
            'monthNames': [
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
            ]
        }
    } );

    $( '#_event_date-apply' ).click( function( e ) { 
        var target   = $('div#listeo-listings-container' );
        target.triggerHandler( 'update_results', [ 1, false ] );
    } );

    $( '#_event_date-clear' ).click( function( e ) { 
        $('#_event_date-content').find('input[type="text"]').each( function( index, input ) {
            $(input).val('');
        } );
    } ); 
} );

/* archive-listings functions */
function onloadArchiveListingPage(){

  searchWordCounted = jQuery('#keyword_search').val() != "";

  // onload change 'område' text to 'leverer til' if top category not 'lokaler & områder'
  displayRegionLabelText();

  jQuery( document ).ajaxComplete(function( event, xhr, settings ) {
    if ( settings.url === "/wp-admin/admin-ajax.php" ) {


      if(xhr.responseText.slice(0, '{"found_listings"'.length) == '{"found_listings"') {
        var ress = jQuery.parseJSON(xhr.responseText);
        if(ress.found_posts != undefined){
          jQuery('#totalLoadedListings').text(ress.found_posts);
        }
        /*var startSubstring = xhr.responseText.indexOf('"total_hits":') +'"total_hits":'.length;
        var stopSubstring = xhr.responseText.indexOf(',', startSubstring);
        var count = xhr.responseText.substring(startSubstring, stopSubstring);
        count = count.replace(/"/g, '');*/
        //jQuery.parseJSON(xhr.responseText);
        
      }
    }
  });

  // Hide features taxonomy on load
    jQuery('#tax-listing_feature-panel').css({'order' : '10', 'visibility' : 'hidden'});

    // Hide all checkboxes for subcategories
    if(jQuery('#tax-listing_category input:checked').parent().attr('class') != null){
      var fromChosenCat = jQuery('#tax-listing_category input:checked').parent().attr('class').slice("panel-checkbox-wrap category-".length);
      if(fromChosenCat != null && fromChosenCat > 0){
        jQuery('#tax-listing_category-panel .panel-checkbox-wrap:not(.parent-' + fromChosenCat + ')').each(function(){
          jQuery(this).hide();
         });
        jQuery('#tax-listing_category-panel .panel-checkbox-wrap.parent-' + fromChosenCat).each(function(){
          jQuery(this).css('display', 'flex');
         });
        jQuery('#tax-listing_category-panel .category-depth-0 .alle:first-child').css('display', 'flex');
      } else {
        jQuery('#tax-listing_category-panel .panel-checkbox-wrap').each(function(){
          jQuery(this).hide();
        });
      }
    } else {
      jQuery('#tax-listing_category-panel .panel-checkbox-wrap').each(function(){
        jQuery(this).hide();
      });
    }

    // If top category is chosen onload display the text
    if(jQuery('#tax-listing_category a span.greenThenWhite').length <= 0)
          jQuery('<span class="greenThenWhite"></span>').insertBefore('#tax-listing_category i.fa-times[onclick]');
    jQuery('#tax-listing_category a span.greenThenWhite').text(jQuery('#tax-listing_category .panel-checkbox-wrap').find('input:checked').next().text());

    // if top category is not chosen then hide sub categores
    if(jQuery('#tax-listing_category .panel-checkbox-wrap input:checked').length <= 0)
      jQuery('#tax-listing_category-panel').parent().css({'visibility' : 'hidden', 'order' : '10'});

    // Show correct sub categories based on parent category chosen
    jQuery('#tax-listing_category .panel-checkbox-wrap').click(function(){

      // Hide sub-subcategories
      jQuery('.cat-depth:not(.category-depth-0)').css('margin-left', '100%');
      jQuery('.category-depth-0').show();
      jQuery('.cat-depth:not(.category-depth-0)').hide();
      jQuery('#backtrackSubcategory').hide();

      // Add display text
        if(jQuery('#tax-listing_category a span.greenThenWhite').length <= 0)
          jQuery('<span class="greenThenWhite"></span>').insertBefore('#tax-listing_category i.fa-times[onclick]');

        // Clear filters for sub kategories
      clearFiltersFor('#tax-listing_category-panel', false);

        var fromUncheckedToChecked = jQuery(this).find('input').prop('checked');

        // Hide all subcategories
        jQuery('#tax-listing_category-panel .category-depth-0 .panel-checkbox-wrap').each(function(){
          jQuery(this).hide();
          jQuery(this).find('input').prop('checked', false);
        });

        // Show "all" btn
        jQuery('#tax-listing_category-panel .alle:first-child').css('display', 'flex');

        // Remove previous 'checked' appearence
        jQuery('#tax-listing_category .panel-checkbox-wrap input:checked').each(function(){
            jQuery(this).prop('checked', false);
        });

        jQuery('#topLvCategories input:checked').each(function(){ jQuery(this).prop('checked', false); });

        if(!fromUncheckedToChecked){
          // Show correct sub categories
            jQuery('#tax-listing_category-panel .panel-checkbox-wrap.parent-' + jQuery(this).attr('class').slice('panel-checkbox-wrap category-'.length)).each(function(){
                jQuery(this).css('display', 'flex');
            });
            jQuery('#topKategoriDisplayer p').text(jQuery(this).find('label').text());
        } else
        jQuery('#topKategoriDisplayer p').text('Alle');

        jQuery(this).find('input').prop('checked', !fromUncheckedToChecked);
        jQuery(this).closest('.panel-dropdown').removeClass('active');

        jQuery('.fs-inner-container').removeClass('faded-out');

        jQuery('#tax-listing_category a span.greenThenWhite').text(jQuery('#tax-listing_category .panel-checkbox-wrap').find('input:checked').next().text());

        if(jQuery('#tax-listing_category .panel-checkbox-wrap input:checked').length <= 0)
        jQuery('#tax-listing_category-panel').parent().css({'visibility' : 'hidden', 'order' : '10'});
      else
        jQuery('#tax-listing_category-panel').parent().css({'visibility' : 'visible', 'order' : '1'});


        jQuery('#topLvCategories input[value="' + jQuery(this).find('input').val() + '"]').prop('checked', fromUncheckedToChecked).trigger('click');

        displayRegionLabelText();

      });

    if(jQuery('#keyword_search').val() != null || jQuery('#keyword_search').val() == "")
      displaySearchedWord();

    // Scroll to top on 'show map' btn press
    jQuery('#show-map-button').click(function(){ jQuery("html, body").animate({ scrollTop: 0 }, "slow"); });

    jQuery('.skjulMobilFilters').click(function(){jQuery('#mainFiltersToggler').trigger('click')});

    // mobile toggling functionality
    jQuery('#mainFiltersToggler').click(function(){
      if(jQuery('#mainFilters').css('display') != 'none'){
        jQuery('#overlay').css({'visibility':'hidden', 'opacity' : 0});
        jQuery('.main-nav-small').css('visibility', 'visible');
        jQuery('body').css('overflow', 'auto');
      }
      else{
        jQuery('#overlay').css({'visibility':'visible', 'opacity' : 1});
        jQuery('.main-nav-small').css('visibility', 'hidden');
        jQuery('body').css('overflow', 'hidden');
      }
      jQuery('#mainFilters').slideToggle();
    });

    jQuery('.listing_container_with_overlay #overlay').click(function(){
       jQuery('#mainFiltersToggler').trigger('click');
    });

    // Slider UI
    prisText = jQuery('#_price-panel a').html() + " ";

    jQuery("#_price-panel .bootstrap-range-slider").slider("enable");
    //jQuery('<span class="greenThenWhite"></span>').insertBefore('#_price-panel i.fa-times[onclick]');

    jQuery("#_price-panel #_price_range").change(function() {
        if(jQuery(this).prop("disabled") != true){
          jQuery('#_price-panel > a .greenThenWhite').html("(1) "+jQuery("#_price-panel .tooltip.tooltip-main.top").text());
          jQuery('#_price-panel i.fa-times[onclick]').show().css({"float":"right"})
        }
        
        

        if(!prisAktivert){
          var to = jQuery('#_price-panel .range-slider-container');
          jQuery(to).find("input").prop('disabled', false);
          prisAktivert = true;
          totalFilters++;
          updateTotalFiltersNr();
        }
    });

    

    // Multiselect counter and displayer
    jQuery('#tax-listing_category-panel .cat-depth input').click(function(e){

      var catDepth = jQuery(this).closest('.cat-depth').attr('class').slice('cat-depth '.length);
      var isFirstCat = (catDepth == 'category-depth-0');
      var thisParentsCat = jQuery(this).parent().attr('class').slice('panel-checkbox-wrap parent-'.length);

      var elem = (isFirstCat) ? jQuery('#tax-listing_category-panel a span.greenThenWhite') : jQuery('#cat-' + thisParentsCat + ' .subDisplayText');
      var elem2 = (isFirstCat) ? jQuery('#tax-listing_category-panel a span.counterForFilter') : jQuery('#cat-' + thisParentsCat + ' .counterForSubFilter');
      filterTekstDisplayCommaSeperated(elem, elem2, '#tax-listing_category-panel .' + catDepth);
      if(jQuery('#tax-listing_category-panel input:checked').length <= 0){ jQuery('#tax-listing_category-panel i.fa-times[onclick]').hide(); } else { jQuery('#tax-listing_category-panel i.fa-times[onclick]').show(); }

      jQuery('#topLvCategories input').each(function(){ jQuery(this).prop('checked', false); });

        if(jQuery('.cat-depth input:checked').length > 0)
          jQuery('#topLvCategories input:last').prop('checked', false);
        else
          jQuery('#topLvCategories input[value="'+ jQuery('#tax-listing_category input:checked').val() +'"]').prop('checked', true);

        jQuery('#keyword_search').change();           

    });
    // Backtrack subcategory
    jQuery('#backtrackSubcategoryBtn').click(function(){

        jQuery('.cat-depth[style*="margin-left: 0%"]:last-child').css('margin-left', '100%');

        jQuery('#backtrackSubcategory').hide();

        jQuery('.category-depth-' + '0').show();
        jQuery('.category-depth-' + '0' + ' .panel-checkbox-wrap').each(function(){ 
          if(jQuery(this).hasClass('alle') || jQuery(this).hasClass('parent-' + jQuery('#tax-listing_category input:checked').parent().attr('class').slice('panel-checkbox-wrap category-'.length)))
            jQuery(this).css('display', 'flex') 
          else
            jQuery(this).hide();
        });
        jQuery('.category-depth-' + '1' + ' .panel-checkbox-wrap').hide();
    });

      // Region
      // If a kommune is chosen, show subcat display
    if(jQuery('#tax-region-panel .kommune input:checked').length > 0){
      jQuery('.fylkeWrapper').hide();
    }
    // else if a fylke was chosen, show the 'fylker'
    else if(jQuery('#tax-region-panel .fylke input:checked').length > 0) {
      var fylkeChosen = jQuery('#tax-region-panel .fylke input:checked').attr('class').slice('f-'.length);
      jQuery('#tax-region-panel .kommune:not(.fylke-'+ fylkeChosen +')').each(function(){ jQuery(this).hide(); });
    } else
        jQuery('#tax-region-panel .kommuneWrapper div').each(function(){ jQuery(this).hide(); });

    /*jQuery('#tax-region-panel input').click(function(e){
        filterTekstDisplayCommaSeperated(jQuery('#tax-region-panel a span.greenThenWhite'), jQuery('#tax-region-panel a span:first-child'), '#tax-region-panel');
        if(jQuery('#tax-region-panel input:checked').length <= 0){ jQuery('#tax-region-panel i.fa-times[onclick]').hide(); } else { jQuery('#tax-region-panel i.fa-times[onclick]').show(); }
    });*/
    // Backtrack subregion
    jQuery('#backtrackSubregion').click(function(){

        jQuery('.kommuneWrapper').css('margin-left', '100%');
        jQuery('#backtrackSubregion').hide();
        jQuery('.kommuneWrapper div').each(function(){ jQuery(this).hide(); });

        jQuery('.fylkeWrapper').show();
        jQuery('.fylkeWrapper div').each(function(){ jQuery(this).show(); });
        
    });

    // total Filters display
    jQuery(".counterForFilter").bind("DOMSubtreeModified", function() { updateTotalFiltersNr(); });

    // Text display
    jQuery('#keyword_search').keyup(function(e){
      if(e.keyCode == 13)
        jQuery(this).closest('.panel-dropdown').removeClass('active');            
        displaySearchedWord();
    });
    jQuery('#keyword_search').change(function(){ displaySearchedWord(); });

    // Only show 'features' option if there are available options
    jQuery("#tax-listing_feature-panel .mygibb").bind("DOMSubtreeModified", function() {
      if(jQuery(this).find('.panel-checkboxes-container').length > 0){

        jQuery('#tax-listing_feature-panel input').click(function(){
            
            if(jQuery('#tax-listing_feature-panel a span.counterForFilter').length <= 0)
              jQuery('<span class="counterForFilter" style="padding:0px 5px 0px 5px;"></span>').insertBefore('#tax-listing_feature-panel i.fa-times[onclick]');

            if(jQuery('#tax-listing_feature-panel a span.greenThenWhite').length <= 0)
              jQuery('<span class="greenThenWhite"></span>').insertBefore('#tax-listing_feature-panel i.fa-times[onclick]');

            filterTekstDisplayCommaSeperated(jQuery('#tax-listing_feature-panel a span.greenThenWhite'), jQuery('#tax-listing_feature-panel a span:first-child'), '#tax-listing_feature-panel');

            if(jQuery('#tax-listing_feature-panel a span.greenThenWhite').text() == ""){ jQuery('#tax-listing_feature-panel i.fa-times[onclick]').hide(); } else { jQuery('#tax-listing_feature-panel i.fa-times[onclick]').show(); }

            updateTotalFiltersNr();
          });

        if(jQuery(this).find('.panel-checkboxes-container').children().length > 0)
          jQuery('#tax-listing_feature-panel').css({'display' : 'flex', 'visibility' : 'visible', 'order' : '2'});
        else 
          jQuery('#tax-listing_feature-panel').css({'visibility' : 'hidden', 'order' : '10'});

        if(jQuery('#tax-listing_feature-panel a span.greenThenWhite').text() == ""){ jQuery('#tax-listing_feature-panel i.fa-times[onclick]').hide(); } else { jQuery('#tax-listing_feature-panel i.fa-times[onclick]').show(); }
      } 
      else {
        jQuery('#tax-listing_feature-panel').css({'visibility' : 'hidden', 'order' : '10'});
        jQuery('#tax-listing_feature-panel > a:first-child').html('Tilbyr: <i class="fa fa-times" onclick="clearFiltersFor(\"#tax-listing_feature-panel\")" style="display: none;"></i>');
      }
    });

    // Hide extra checkboxes for listing feature
    jQuery('#tax-listing_feature-panel .row:not(.mygibb)').append('<a href="#" style="all:unset;"><i class="fa fa-times"></i><span class="greenThenWhite"></span></a><p>Tilbyr: </p>');
    

    // Null still filtre
    jQuery('#mainFilters .panel-dropdown > a > i.fa-times').click(function(e){
      e.preventDefault();
      e.stopPropagation();
      clearFiltersFor(jQuery(this).closest('.panel-dropdown').attr('id'));
    })

    // Choose all in list
    jQuery('.alle').click(function(){ chooseAllInputs(jQuery(this).parent()); });

    // Show sub categories
    jQuery('.showSubCategories').click(function(e){ showSubCategoriesOrRegions(jQuery(this).prev().clone().children().remove().end().text(), jQuery(this).parent().attr('id').slice('cat-'.length), jQuery(this).parent().parent()) ; });

    // Show sub regions
    jQuery('.showSubRegions').click(function(e){ showSubCategoriesOrRegions(jQuery(this).prev().text(), jQuery(this).parent().find('input').attr('class').slice('f-'.length), jQuery('.fylkeWrapper'), true) ; });

    featuresInSearch();

}
function filterTekstDisplayCommaSeperated(elem, counterElem, taxonomy){

  var total = jQuery(taxonomy + ' input:checked').length > 0 ? jQuery(taxonomy + ' input:checked').length : "";

    var wordList = "";
    jQuery(taxonomy + ' input:checked').each(function(){
      wordList += jQuery(this).next().clone().children().remove().end().text() + ", ";
    });

    elem.text(wordList);

    if(counterElem != null)
      counterElem.text(total);

}
function displayRegionLabelText(){
  var oldTxt = jQuery('#tax-region-panel > a').html();
  var newTxt = "";

  var topCatVal = jQuery('#tax-listing_category :checked').val();
  if(topCatVal != null){
    if(topCatVal != "lokaler-uteomrader")
      newTxt = oldTxt.replace(/Område: /g, 'Leverer til: ');
    else
      newTxt = oldTxt.replace(/Leverer til: /g, 'Område: ');  
  } 
  else
    newTxt = oldTxt.replace(/Leverer til: /g, 'Område: ');  
  
  jQuery('#tax-region-panel > a').html(newTxt);
}
function scrollUpShowMap(){

  position = jQuery(window).scrollTop();

  jQuery(window).scroll(function() {
      var scroll = jQuery(window).scrollTop();
      if(scroll > position)
          jQuery('#scrollUpShowMap').fadeOut();
      else 
          jQuery('#scrollUpShowMap').fadeIn();
      position = scroll;
  });

}
function displaySearchedWord(){
  if(jQuery('#keyword_search-panel a span.greenThenWhite').length <= 0)
    jQuery('<span class="greenThenWhite"></span>').insertBefore('#keyword_search-panel i.fa-times[onclick]');

  if(jQuery('#keyword_search-panel a span.greenThenWhite').text() != ""){
     var couuttt = "(1) ";
     var cross = '';
     jQuery("#keyword_search-panel").find(".fa-times").show();
      jQuery("#keyword_search-panel").find(".fa-times").css({"float":"right"});
  }else{
    var cross = "";
    var couuttt = "";
    jQuery("#keyword_search-panel").find(".fa-times").hide();
  }
  

  jQuery('#keyword_search-panel a span.greenThenWhite').html(couuttt+jQuery('#keyword_search').val()+cross);

  if(jQuery('#keyword_search-panel a span.greenThenWhite').text() == ""){
    jQuery('#keyword_search-panel i.fa-times[onclick]').hide(); 
  } else { 
   // jQuery('#keyword_search-panel i.fa-times[onclick]').hide(); 
    //jQuery('#keyword_search-panel .cross_a').show();
  }

  if(jQuery('#keyword_search').val() == "" && searchWordCounted){
    searchWordCounted = false;
    //totalFilters--;
  }
  else if(jQuery('#keyword_search').val() != "" && !searchWordCounted){
    searchWordCounted = true;
    //totalFilters++;
  }
  updateTotalFiltersNr();

}

function showChooseCategories(){
  jQuery('#mainFiltersToggler').trigger('click');
  jQuery('#tax-listing_category > a').trigger('click');
}

function clearFiltersFor(filter, update = true){
  if(filter == "#tax-listing_category-panel" || filter == "#tax-region-panel" || filter == "#tax-listing_feature-panel"){
    jQuery('#mainFilters ' + filter + ' input:checked:not(:last)').prop('checked', false);
    if(update)
      jQuery('#mainFilters ' + filter + ' input:checked').trigger('click');
    else 
      jQuery('#mainFilters ' + filter + ' input:checked:last').prop('checked', false);

    jQuery('#mainFilters ' + filter + ' i[onclick]').hide();
    if(filter == '#tax-listing_category-panel'){
      jQuery('#tax-listing_feature-panel .counterForFilter').text("");
      clearFiltersFor("#tax-listing_feature-panel", update);
    }
  } else if(filter == "#keyword_search-panel"){
    jQuery('#keyword_search').val('');
    if(update) { jQuery('#keyword_search').change(); }
  }
  else if(filter == "#_price-panel"){
    var to = jQuery('#_price-panel .range-slider-container');
    jQuery("#_price-panel .bootstrap-range-slider").slider("disable");
    jQuery(to).find("input").prop('disabled', true);
        prisAktivert = false;
        jQuery('#_price-panel i[onclick]').hide();
        totalFilters--;
        if(update) { jQuery('#keyword_search').change(); }
        jQuery("#_price-panel .bootstrap-range-slider").slider("enable");
  }else if(filter == "#_standing-panel"){
    var to = jQuery('#_standing-panel .range-slider-container');
    jQuery("#_standing-panel .bootstrap-range-slider").slider("disable");
    jQuery(to).find("input").prop('disabled', true);
        prisAktivert = false;
        jQuery('.capacityDisable').css('visibility','hidden');
        totalFilters--;
        if(update) { jQuery('#keyword_search').change(); }
        jQuery("#_standing-panel .bootstrap-range-slider").slider("enable");
  } else if(filter == "#tax-listing_category")
    jQuery('#mainFilters ' + filter + ' input:checked').prop('checked', false);
  
  // Update total filters
  jQuery('#mainFilters ' + filter + ' span.counterForFilter').text("");
  jQuery('#mainFilters ' + filter + ' span.greenThenWhite').text("");
  updateTotalFiltersNr();

}

function brukFilter(btn){

    jQuery(btn).closest('.panel-dropdown-content').find('a:first').click();
     //jQuery("#listeo_core-search-form input").change();
}

function chooseAllInputs(elem){

  var on = !elem.find('.alle input').prop('checked');
  elem.find('.panel-checkbox-wrap:not([style*="display: none"]) input').prop('checked', on);

  var nr = elem.find('input:checked').length;

  var parent = elem.closest('.cat-depth');
  if(parent.length > 0){
    if(!parent.hasClass('category-depth-0')){

      var parentCat = elem.find('.panel-checkbox-wrap:visible:not(.alle) input').parent().attr('class').slice('panel-checkbox-wrap parent-'.length);
      var parentLabel = jQuery('#cat-' + parentCat + ' label');
      parentLabel.find('span.counterForSubFilter').text(nr > 0 ? nr : "");
      parentLabel.find('span:nth-child(2)').text(nr > 0 ? "Alle" : "");
      var p = elem.closest('.panel-dropdown');
      if(nr <= 0){ p.find('i.fa-times[onclick]').hide(); } else { p.find('i.fa-times[onclick]').show(); }
      jQuery('#keyword_search').change();
      return;
    }
  }

  parent = elem.closest('.panel-dropdown');
  parent.find('span.counterForFilter').text(nr > 0 ? nr : "");
  parent.find('span.greenThenWhite').text(nr > 0 ? "Alle" : "");
  var nr = parent.find('input:checked').length;
  if(nr <= 0){ parent.find('i.fa-times[onclick]').hide(); } else { parent.find('i.fa-times[onclick]').show(); }
  updateTotalFiltersNr();
  jQuery('#keyword_search').change();

}
function updateTotalFiltersNr(){    

    var total = totalFilters;
    jQuery('.counterForFilter').each(function(){ 
        if(jQuery(this).text() != "") 
            total += parseInt(jQuery(this).text()); 
    });
        if(jQuery("#listeo_core-search-form").find("#keyword_search-panel").find("input").val() != ""){
          count_search = 1;
        }else{
          count_search = 0;
        }
        var lenn = jQuery("#listeo_core-search-form").find("input:checked").length + count_search;
        if(lenn > 0){
           jQuery("#totalFilters").html(lenn);
        }else{
           jQuery("#totalFilters").html("");
        }
    
   // jQuery('#totalFilters').text(total > 0 ? total : '');

}
function clearAllFilters(){

      
          jQuery("#listeo_core-search-form").find("input").val("");
           jQuery("#listeo_core-search-form").find(".greenThenWhite").html("");
      
       jQuery("#listeo_core-search-form").find("input").prop("checked",false);
       jQuery("#listeo_core-search-form").find("input:first").change();
      jQuery("#listeo_core-search-form").find(".fa-times").hide();

  // all taxonomies
  var taxonomies = ['#tax-listing_feature-panel', '#tax-listing_category', '#tax-listing_category-panel', '#tax-region-panel', '#keyword_search-panel', '#_price-panel'];

  // clear all of the taxonomies, but don't perform ajax call
  for(var i = 0; i < taxonomies.length; i++)
    clearFiltersFor(taxonomies[i], false);

  if(topCatOnLoad != null && topCatOnLoad != "")
    jQuery('#tax-listing_category input[value="' + topCatOnLoad + '"]').closest('.panel-checkbox-wrap').trigger('click');
  
  else{

    jQuery('#topKategoriDisplayer p').text('Alle');

    // finally ajax call when all filters are cleared
    jQuery('#keyword_search').change();
  }
  
  // Hide subcategories and 'tilbyr'
  jQuery('#tax-listing_feature-panel').css({'visibility' : 'hidden', 'order' : '10'});
  jQuery('#tax-listing_category-panel').parent().css({'visibility' : 'hidden', 'order' : '10'});

}

function showSubCategoriesOrRegions(selectedName, idOfSelected, prevContainer, isRegionSub = false){

  prevContainer.find('.panel-checkbox-wrap').each(function(){ jQuery(this).hide(); });

  var textDisplayWhichSub =  isRegionSub ? prevContainer.parent().parent().prev() : prevContainer.parent().prev();
  var subs = isRegionSub ? jQuery('.kommuneWrapper') : prevContainer.next();
  var parentClassNamePrefix = isRegionSub ? '.fylke-' : '.parent-';

  prevContainer.next().find(parentClassNamePrefix + idOfSelected).each(function(){ jQuery(this).css('display', 'flex') });          
  textDisplayWhichSub.find('span:last').text(selectedName);
  textDisplayWhichSub.show();
  subs.show();
  subs.css('margin-left', '0%');
  subs.find('.alle').css('display', 'flex');

}

/* fix for Listeo Core bug, fix for handling remembering selected features in adit/edit listing */
function featuresInEditOrAddListingFix(){

  var firstFeatureLoad = false;

  jQuery('.dynamic #tax-listing_category,.dynamic #tax-listing_category-panel input').on('change',function(e) {

    if(selectedFeaturesOnLoad != null){
      if(selectedFeaturesOnLoad.length > 0 && !firstFeatureLoad)
        var selected_listing_feature = selectedFeaturesOnLoad;      

    } else {

      if( jQuery('.add-listing-section').length ){

        var selected_listing_feature = [];

        jQuery.each(jQuery("input[name='tax_input[listing_feature][]']:checked"), function(){            
            selected_listing_feature.push(jQuery(this).val());
        });

      }

    }

    firstFeatureLoad = true;

      var cat_ids = []
      
      jQuery('#tax-listing_feature-panel .checkboxes').addClass('loading');
      jQuery('#tax-listing_feature-panel .panel-buttons').hide();
      var panel = false;
      if(jQuery('#tax-listing_category-panel').length>0){
          panel = true;
          
          jQuery("#tax-listing_category-panel input[type=checkbox]:checked").each(function(){
            
              cat_ids.push(jQuery(this).val());
          });
      } else {
          if(jQuery('#tax-listing_feature-panel').length>0){
          panel = true;  
          }
          if(jQuery(this).prop('multiple')){
              jQuery('#tax-listing_category :selected').each(function(i, sel){ 
                  cat_ids.push( jQuery(sel).val() ); 

              });
          } else {
            cat_ids.push(jQuery(this).val());  
          }
          
      }
      
      jQuery.ajax({
          type: 'POST', 
          dataType: 'json',
          url: listeo.ajaxurl,
          data: { 
              'action': 'listeo_get_features_from_category', 
              'cat_ids' : cat_ids,
              'panel' : panel,
              //'nonce': nonce
             },
          success: function(data){
            jQuery('#tax-listing_feature-panel .checkboxes').removeClass('loading');
            jQuery('#tax-listing_feature-panel .checkboxes .row').html(data['output']).removeClass('loading');
            jQuery('#tax-listing_feature').html(data['output']).removeClass('loading');
            if(data['success']){
              jQuery('#tax-listing_feature-panel .panel-buttons').show();
            }

          }            
      });

  });

  jQuery('.add-listing-section #listing_category,.add-listing-section #tax-listing_category').on('change',function(e) {

    if(selectedFeaturesOnLoad != null && selectedFeaturesOnLoad.length > 0 && !firstFeatureLoad){

      var selected_listing_feature = selectedFeaturesOnLoad;

    } else {

      if( jQuery('.add-listing-section').length ){

        var selected_listing_feature = [];

        jQuery.each(jQuery("input[name='tax_input[listing_feature][]']:checked"), function(){            
            selected_listing_feature.push(jQuery(this).val());
        });

      }

    }

    firstFeatureLoad = true;
    
    var listing_id = jQuery( "input[name='listing_id']" ).val();
    if(jQuery(this).prop('multiple')){
        var cat_ids;
        cat_ids = jQuery(this).val();
    } else {
        var cat_ids = [];
        cat_ids.push(jQuery(this).val());  
    }
    
     jQuery.ajax({
          type: 'POST', 
          dataType: 'json',
          url: listeo.ajaxurl,
          data: { 
              'action': 'listeo_get_features_ids_from_category', 
              'cat_ids' : cat_ids,
              'listing_id' : listing_id,
              'selected' :selected_listing_feature,
              'panel' : false,
              //'nonce': nonce
             },
          success: function(data){
            jQuery('.listeo_core-term-checklist-listing_feature,.listeo_core-term-checklist-tax-listing_feature').removeClass('loading');
            jQuery('.listeo_core-term-checklist-listing_feature,.listeo_core-term-checklist-tax-listing_feature').html(data['output']).removeClass('loading')
            
          }            
      });

  });

  jQuery('.add-listing-section #listing_category,.add-listing-section #tax-listing_category').trigger('change');
}

function featuresInSearch(){
  jQuery('.dynamic #tax-listing_category,.dynamic #tax-listing_category-panel input').on('change',function(e) {
      var cat_ids = []
      
      jQuery('#tax-listing_feature-panel .checkboxes').addClass('loading');
      jQuery('#tax-listing_feature-panel .panel-buttons').hide();
      var panel = false;
      if(jQuery('#tax-listing_category-panel').length>0){
          panel = true;
          
          jQuery("#tax-listing_category-panel input[type=checkbox]:checked").each(function(){
            
              cat_ids.push(jQuery(this).val());
          });
      } else {
          if(jQuery('#tax-listing_feature-panel').length>0){
          panel = true;  
          }
          if(jQuery(this).prop('multiple')){
              jQuery('#tax-listing_category :selected').each(function(i, sel){ 
                  cat_ids.push( jQuery(sel).val() ); 

              });
          } else {
            cat_ids.push(jQuery(this).val());  
          }
          
      }
      jQuery.ajax({
          type: 'POST', 
          dataType: 'json',
          url: listeo.ajaxurl,
          data: { 
              'action': 'listeo_get_features_from_category', 
              'cat_ids' : cat_ids,
              'panel' : panel,
              //'nonce': nonce
             },
          success: function(data){
            jQuery('#tax-listing_feature-panel .checkboxes').removeClass('loading');
            jQuery('#tax-listing_feature-panel .checkboxes .row').html(data['output']).removeClass('loading');
            jQuery('#tax-listing_feature').html(data['output']).removeClass('loading');
            if(data['success']){
              jQuery('#tax-listing_feature-panel .panel-buttons').show();
            }

          }            
      });
  });

  jQuery('.add-listing-section #listing_category,.add-listing-section #tax-listing_category').on('change',function(e) {
    
    var listing_id = jQuery( "input[name='listing_id']" ).val();
    if(jQuery(this).prop('multiple')){
        var cat_ids;
        cat_ids = jQuery(this).val();
    } else {
        var cat_ids = [];
        cat_ids.push(jQuery(this).val());  
    }
    
     jQuery.ajax({
          type: 'POST', 
          dataType: 'json',
          url: listeo.ajaxurl,
          data: { 
              'action': 'listeo_get_features_ids_from_category', 
              'cat_ids' : cat_ids,
              'listing_id' : listing_id,
              'selected' :selected_listing_feature,
              'panel' : false,
              //'nonce': nonce
             },
          success: function(data){
            jQuery('.listeo_core-term-checklist-listing_feature,.listeo_core-term-checklist-tax-listing_feature').removeClass('loading');
            jQuery('.listeo_core-term-checklist-listing_feature,.listeo_core-term-checklist-tax-listing_feature').html(data['output']).removeClass('loading')
            
          }            
      });
  
  });

  var selected_listing_feature = [];
  if( jQuery('.add-listing-section').length ){
    jQuery.each(jQuery("input[name='tax_input[listing_feature][]']:checked"), function(){            
        selected_listing_feature.push(jQuery(this).val());
    });    
    jQuery('select#listing_category').trigger('change');
  }
}

/* check if error on login then show login dialog box */
jQuery(document).ready(function(){
  if (window.location.href.indexOf("login=") > -1) {
    setTimeout(function() { 
      jQuery('#menu-new-main-menu .profile-icon .xoo-el-login-tgr').click();
    }, 1000);
  }
});
/* check if error on login then show login dialog box */

/* download email order template as pdf at woocommerce thank you page */
setTimeout(function(){
  // var jsPdfDoc = new jsPDF();
  // var specialElementHandlers = {
  //     '#editor': function (element, renderer) {
  //         return true;
  //     }
  // };
   
  // jQuery('#generatePDF').click(function () {
  //   jsPdfDoc.fromHTML(jQuery('#thankyou-pdf').html(), 15, 15, {
  //     'width': 700,
  //     'elementHandlers': specialElementHandlers
  //   });
  //   jsPdfDoc.save('sample_file.pdf');
  // });
  jQuery('#generatePDF').click(function () {
    html2canvas(document.getElementById("thankyou-pdf"), {
      onrendered: function(canvas) {
        var imgData = canvas.toDataURL('image/png');
        var doc = new jsPDF('p', 'px', [700, 700]);
        
        doc.addImage(imgData, 'PNG', 20, 20);
        doc.save('Ordre-kvittering.pdf');
      }
    });
  });
}, 4000);
/* download email order template as pdf at woocommerce thank you page */

/************* Filter Applications Button Click *************/
jQuery(document).ready(function(){
  jQuery('#applications-ajax-form').on('submit',function(e) {
    e.preventDefault();
    jQuery('#applications-ajax-form #applications-datatable-search').val('');
    var getSeasonVal = jQuery("#applications-ajax-form #season_number option:selected").val();
    var ajaxurl = jQuery("#admin_url").val();

    console.log(getSeasonVal);
    // jQuery(".add-listing-section #_listing_gym").hide();
    // var user_group_id = jQuery('.add-listing-section #_user_groups_id').val();
    
    // var listing_id = getUrlVars()["listing_id"];

    jQuery.ajax({
      type: 'POST', 
      dataType: 'json',
      url: ajaxurl,
      data: { 
          'action': 'get_applications_by_season', 
          'season_id' : getSeasonVal,
         },
      success: function(data){
        if(data.status == 200) {
          jQuery('#applications-table-block').html(data.tbody_data);
          jQuery('#applications-table').DataTable({
            "sDom":"ltipr",
            "bLengthChange": false,
            language: {
              'paginate': {
                'previous': 'Tidligere',
                'next': 'Neste'
              },
              "info": "Viser _START_ til _END_ av _TOTAL_ treff",
              "infoEmpty": "Viser 0 til 0 av 0 treff",
              "emptyTable": "Ingen data tilgjengelig i tabellen",
              "zeroRecords": "Ingen samsvarende poster funnet"
            }
          });
        }
      }
    });
  });
});
/************* Filter Applications Button Click *************/


