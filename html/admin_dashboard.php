<?php
  // global $wpdb;

  // Load WordPress core.
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
  // Load components.
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/translation.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/header/header.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/sidebar/sidebar.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/capacity_data_manager.php';

  // If the user is not logged in as an administrator, redirect to the login page with HTTP status code 401.
  $access_token = User::verify_is_admin();

  // Get translated texts.
  $text = new Translation('', 'storage', '');

  // Read capacity information.
  $capacity_data = new Capacity_Data_Manager($access_token);
  $capacities = $capacity_data->read();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= Utility::get_page_title() ?></title>
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/fontawesome.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/solid.css?v=<?= Utility::BUILD_NO ?>" />
    <link rel="stylesheet" type="text/css" href="/subscription/resources/css/common.css?v=<?= Utility::BUILD_NO ?>" />
    <script type="text/javascript" src="/subscription/js/common.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript" src="/subscription/js/admin_dashboard.js?v=<?= Utility::BUILD_NO ?>"></script>
    <script type="text/javascript">

<?= $text->get_js_strings() ?>

var capacities = <?= $capacities ?>;

    </script>



    <style>

.dashboard
{
  max-width: 1200px;
  margin: auto;
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.filters
{
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 30px;
}

.filters button
{
  color: #fff;
  background-color: #008474;
}

.metrics
{
  display: flex;
  justify-content: space-around;
  margin-bottom: 30px;
}

.metric
{
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 200px;
}

.metric h3
{
  margin: 10px 0;
  font-size: 1.5em;
}

.charts
{
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding-bottom: 100px;
}

.chart-container
{
  width: 30%; /* Ensures three charts per row */
  height: 300px;
  padding: 15px;
  text-align: center;
  box-sizing: border-box;
}

    </style>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



  </head>
  <body onload="initialise();">
    <?= Sidebar::get_admin_sidebar() ?>
    <?= Header::get_header_with_user_info($access_token, $text->get(3, 'Dashbord'), 'fa-gauge-simple-high') ?>



    <div class="content">
      <!-- Filters -->
      <div class="filters">
        <button type="button" class="wide-button">Lokasjon</button>
        <button type="button" class="wide-button">Dato</button>
        <button type="button" class="wide-button">Innflytt/utflytt</button>
      </div>

      <!-- Metrics -->
      <div class="metrics">
        <div class="metric">
          <h3>59034kr</h3>
          <p>Planlagt omsetning neste trekk</p>
        </div>
        <div class="metric">
          <h3>12</h3>
          <p>Nye abonnement</p>
          <p>Sagt opp: 8</p>
        </div>
        <div class="metric">
          <h3>4</h3>
          <p>Utestående betalinger</p>
        </div>
      </div>

      <!-- Charts -->
      <div class="charts">
        <!-- Omsetning per mnd (Line Chart) -->
        <div class="chart-container">
          <h3>Omsetning per mnd</h3>
          <canvas id="revenueChart"></canvas>
        </div>

        <!-- Omsetning per mnd - Per type (Bar Chart) -->
        <div class="chart-container">
          <h3>Omsetning per mnd - Per type</h3>
          <canvas id="revenueByTypeChart"></canvas>
        </div>

        <!-- Omsetning per mnd - Per avdeling (Bar Chart) -->
        <div class="chart-container">
          <h3>Omsetning per mnd - Per avdeling</h3>
          <canvas id="revenueByDepartmentChart"></canvas>
        </div>

        <!-- Utnyttet kapasitet % per mnd (Line Chart) -->
        <div class="chart-container">
          <h3>Utnyttet kapasitet % per mnd</h3>
          <canvas id="capacityUsageChart"></canvas>
        </div>

        <!-- Utnyttet kapasitet - Per avdeling (Bar Chart) -->
        <div class="chart-container">
          <h3>Utnyttet kapasitet - Per avdeling</h3>
          <canvas id="capacityByDepartmentChart"></canvas>
        </div>

        <!-- Utnyttet kapasitet - Per type (Bar Chart) -->
        <div class="chart-container">
          <h3>Utnyttet kapasitet - Per type</h3>
          <canvas id="capacityByTypeChart"></canvas>
        </div>

        <!-- Betalingsmetode (Doughnut Chart) -->
        <div class="chart-container">
          <h3>Betalingsmetode</h3>
          <canvas id="paymentMethodChart"></canvas>
        </div>

        <!-- Type kunde (Pie Chart) -->
        <div class="chart-container">
          <h3>Type kunde</h3>
          <canvas id="customerTypeChart"></canvas>
        </div>
      </div>
    </div>

    <script type="text/javascript">

const brandColor = '#008474';

// Omsetning per mnd - Line Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
            label: 'Inntekt (NOK)',
            data: [12000, 15000, 13000, 17000, 16000, 18000, 19000, 22000, 21000, 23000, 24000, 25000],
            borderColor: brandColor,
            backgroundColor: 'rgba(0, 132, 116, 0.1)',
            fill: true
        }]
    }
});

// Omsetning per mnd - Per type - Bar Chart
const revenueByTypeCtx = document.getElementById('revenueByTypeChart').getContext('2d');
new Chart(revenueByTypeCtx, {
    type: 'bar',
    data: {
        labels: ['Type A', 'Type B', 'Type C', 'Type D', 'Type E'],
        datasets: [{
            label: 'Inntekt (NOK)',
            data: [4000, 5000, 6000, 7000, 8000],
            backgroundColor: ['#008474', '#00A58C', '#33B19A', '#66BDAD', '#99C9BF']
        }]
    }
});

// Omsetning per mnd - Per avdeling - Bar Chart
const revenueByDepartmentCtx = document.getElementById('revenueByDepartmentChart').getContext('2d');
new Chart(revenueByDepartmentCtx, {
    type: 'bar',
    data: {
        labels: ['A', 'B', 'C', 'D', 'E'],
        datasets: [{
            label: 'Inntekt (NOK)',
            data: [5000, 7000, 8000, 6000, 9000],
            backgroundColor: ['#008474', '#00A58C', '#33B19A', '#66BDAD', '#99C9BF']
        }]
    }
});

// Utnyttet kapasitet % per mnd - Line Chart
const capacityUsageCtx = document.getElementById('capacityUsageChart').getContext('2d');
new Chart(capacityUsageCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [
            {
                label: 'Innflytt (%)',
                data: [30, 40, 35, 45, 50, 55, 60, 65, 70, 75, 80, 85],
                borderColor: brandColor,
                fill: false
            },
            {
                label: 'Utflytt (%)',
                data: [20, 25, 30, 35, 30, 25, 20, 30, 25, 20, 15, 10],
                borderColor: 'lightgray',
                fill: false
            }
        ]
    }
});

// Utnyttet kapasitet - Per avdeling - Bar Chart
const capacityByDepartmentCtx = document.getElementById('capacityByDepartmentChart').getContext('2d');
new Chart(capacityByDepartmentCtx, {
    type: 'bar',
    data: {
        labels: ['A', 'B', 'C', 'D', 'E'],
        datasets: [{
            label: 'Kapasitet (%)',
            data: [70, 60, 80, 75, 85],
            backgroundColor: ['#008474', '#00A58C', '#33B19A', '#66BDAD', '#99C9BF']
        }]
    }
});

// Utnyttet kapasitet - Per type - Bar Chart
const capacityByTypeCtx = document.getElementById('capacityByTypeChart').getContext('2d');
new Chart(capacityByTypeCtx, {
    type: 'bar',
    data: {
        labels: ['Type A', 'Type B', 'Type C', 'Type D', 'Type E'],
        datasets: [{
            label: 'Kapasitet (%)',
            data: [65, 70, 60, 80, 75],
            backgroundColor: ['#008474', '#00A58C', '#33B19A', '#66BDAD', '#99C9BF']
        }]
    }
});

// Betalingsmetode - Doughnut Chart
const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(paymentMethodCtx, {
    type: 'doughnut',
    data: {
        labels: ['Visa/Mastercard/Vipps', 'Faktura'],
        datasets: [{
            data: [70, 30],
            backgroundColor: [brandColor, 'lightgray']
        }]
    }
});

// Type kunde - Pie Chart
const customerTypeCtx = document.getElementById('customerTypeChart').getContext('2d');
new Chart(customerTypeCtx, {
    type: 'pie',
    data: {
        labels: ['Privatperson', 'Bedrift'],
        datasets: [{
            data: [90, 10],
            backgroundColor: [brandColor, 'lightgray']
        }]
    }
});

    </script>



    <div class="content">
      <div class="form-element">
        <div class="search-box-container">
          <input type="text" placeholder="<?= $text->get(0, 'Finn lagerbod') ?>" class="search-box long-text" />
          <button type="button" class="search-button" onclick="window.location.href = '/subscription/html/admin_products.php';"><i class="fa-solid fa-search"></i></button>
        </div>
        <div class="search-box-container">
          <input type="text" placeholder="<?= $text->get(1, 'Finn kunde') ?>" class="search-box long-text" />
          <button type="button" class="search-button" onclick="window.location.href = '/subscription/html/admin_edit_user.php?user_id=1001';"><i class="fa-solid fa-search"></i></button>
        </div>
      </div>
    </div>
    <div class="content">
      <div id="capacitiesBox">
        &nbsp;
      </div>
      <!--img src="/subscription/resources/statistics.png" alt="<?= $text->get(2, 'Statistikk') ?>" /-->
    </div>
    <div class="content">
      <div class="form-element">
        <h3><?= $text->get(4, 'Integrasjon') ?></h3>
        <p class="help-text"><?= $text->get(5, 'For &aring; legge til Gibbs minilager p&aring; din webside, bruk f&oslash;lgende URL-er:') ?></p>
      </div>
      <div class="form-element">
        <label for="bookingUrlEdit" class="wide-label"><?= $text->get(6, 'Link til bestillingsside:') ?></label>
        <input id="bookingUrlEdit" type="text" readonly="readonly" class="url-text" value="<?= Utility::get_booking_url() ?>" />
        <button type="button" class="icon-button" onclick="Utility.copyToClipboard('bookingUrlEdit');"><i class="fa-solid fa-copy"></i></button>
      </div>
      <div class="form-element">
        <label for="loginUrlEdit" class="wide-label"><?= $text->get(7, 'Link til innlogging:') ?></label>
        <input id="loginUrlEdit" type="text" readonly="readonly" class="url-text" value="<?= Utility::get_login_url() ?>" />
        <button type="button" class="icon-button" onclick="Utility.copyToClipboard('loginUrlEdit');"><i class="fa-solid fa-copy"></i></button>
      </div>
    </div>

    <?= Utility::get_spinner(false) ?>
  </body>
</html>
