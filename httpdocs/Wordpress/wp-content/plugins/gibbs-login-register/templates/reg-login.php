<div class="tab-gib">
  <button class="tablinks-gib active" onclick="openTab(event, 'login-gib')">Login</button>
  <button class="tablinks-gib" onclick="openTab(event, 'register-gib')">Register</button>
</div>
  <?php 
    $gibbs_login_shortcode = "[gibbs_login]";
    $gibbs_reg_shortcode = "[gibbs_register]";
    if($redirect != ""){
      $gibbs_login_shortcode = "[gibbs_login redirect='".$redirect."']";
      $gibbs_reg_shortcode = "[gibbs_register redirect='".$redirect."']";
    }
  ?>

<div id="login-gib" class="login-gib tabcontent-gib" style="display:block">
  <?php echo do_shortcode($gibbs_login_shortcode);?>
</div>

<div id="register-gib" class="register-gib tabcontent-gib">
  <?php echo do_shortcode($gibbs_reg_shortcode);?>
</div>

<script>
function openTab(evt, cityName) {

  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent-gib");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

/*   debugger; */
  tablinks = document.getElementsByClassName("tablinks-gib");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  tabcontent = document.getElementsByClassName("tabcontent-gib");
  tabcity = document.getElementsByClassName(cityName);
  for (i = 0; i < tabcity.length; i++) {
    tabcity[i].style.display = "block";
  }
  evt.currentTarget.className += " active";
}
</script>