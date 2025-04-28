<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap');
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #FAF9F6;
      color: #0d1b2a;
    }
    header {
      background-color: #afdcec;
      padding: 20px 40px;
      border-bottom: 5px solid #0d1b2a;
    }
    header h1 {
      margin: 0;
      font-size: 48px;
      text-align: center;
      font-weight: 700;
    }
    .horizon-picker {
      text-align: center;
      margin: 20px 0;
    }
    .horizon-picker button {
      margin: 0 .25rem;
      padding: .5rem 1rem;
      border: none;
      border-radius: 4px;
      background: #e0e0e0;
      cursor: pointer;
      font-weight: 500;
    }
    .horizon-picker button.active {
      background: #4361ee;
      color: white;
    }
    .horizon-picker button:hover {
      background: #3f37c9;
      color: white;
    }
    .container {
      display: flex;
      padding: 0 40px;
      gap: 20px;
      margin-top: 20px;
    }
    .left-column {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .right-column {
      flex: 2;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }
    .card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .card-header {
      background-color: #f0f0f0;
      padding: 15px 20px;
      font-weight: bold;
      border-bottom: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-body {
      padding: 1rem;
      overflow-y: auto;
      box-sizing: border-box;
    }

    .chart-container {
      width: 100%;
      height: 400px;
      position: relative;
      padding: 1rem; /* Optional: extra inner space around the chart */
      box-sizing: border-box; /* Important to prevent overflow */
      background: #fff; /* Optional for visual clarity */
      border-radius: 8px; /* Optional: rounded chart area */
    }

        .chart-wrapper {
      width: 100%;
      height: 300px;
      position: relative;
      margin-bottom: 1rem;
    }

        #salesperson_summary .scrollable-table {
      max-height: 200px; /* You can adjust this */
      overflow-y: auto;
      margin-top: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.05);
    }

    #salesperson_summary .scrollable-table table {
      width: 100%;
      border-collapse: collapse;
    }

    #salesperson_summary .scrollable-table th,
    #salesperson_summary .scrollable-table td {
      padding: 0.5rem 0.75rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    #salesperson_summary .scrollable-table thead {
      position: sticky;
      top: 0;
      background-color: #f9f9f9;
      z-index: 1;
    }

    table { 
      border-collapse: collapse; 
      width: 100%; 
      margin-top: 1em;
      font-size: 0.9em;
    }
    th, td { 
      border: 1px solid #ddd; 
      padding: 8px;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .loading, .no-data, .error {
      text-align: center;
      padding: 20px;
    }
    .error { color: #f44336; }
    .no-data { color: #666; font-style: italic; }
  </style>
</head>

<body>
  <header>
    <h1>MANAGER DASHBOARD</h1>
  </header>

  <!-- 1) Time‐Horizon Picker -->
  <div class="horizon-picker" id="time-horizon-picker">
    <button data-horizon="1M">1 M</button>
    <button data-horizon="6M" class="active">6 M</button>
    <button data-horizon="1Y">1 Y</button>
    <button data-horizon="5Y">5 Y</button>
    <button data-horizon="MAX">MAX</button>
  </div>
  <div class="card">
  <div class="card-header">Revenue Lost from Canceled Orders</div>
  <div class="card-body">
    <div id="canceled_revenue"></div>
  </div>
</div>

<div class="card">
  <div class="card-header">Avg. Days Between Order & Pickup</div>
  <div class="card-body">
    <div id="avg_days_to_pickup"></div>
  </div>
</div>

  <div class="container">

    <!-- Left Column: Employee Tables & Charts -->
    <div class="left-column">
      <div class="card">
        <div class="card-header">EMPLOYEE DETAILS</div>
        <div class="card-body">
          <div id="all-employees" class="loading">Loading...</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Salesperson Performance</div>
        <div class="card-body">
          <div id="salesperson_summary" class="chart-container"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Sales by Category</div>
        <div class="card-body">
          <div id="sales_by_category"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">INVENTORY</div>
        <div class="card-body">
          <div id="inventory" class="loading">Loading...</div>
        </div>
      </div>

    </div>

    <!-- Right Column: Charts -->
    <div class="right-column">
      <div class="card">
        <div class="card-header">Order Status Distribution</div>
        <div class="card-body">
          <canvas id="orderStatusChart"></canvas>
          <div id="order_status"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Payment Status</div>
        <div class="card-body">
          <canvas id="paymentStatusChart"></canvas>
          <div id="payment_status"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Customer Types</div>
        <div class="card-body">
          <canvas id="customerTypesChart"></canvas>
          <div id="customer_types"></div>
        </div>
      </div>

      <div class="card" style="grid-column: span 2;">
        <div class="card-header">Top 10 Products</div>
        <div class="card-body">
          <canvas id="topProductsChart"></canvas>
          <div id="top_products"></div>
        </div>
      </div>

      <div class="card" style="grid-column: span 2;">
        <div class="card-header">BOTTOM 10 PRODUCTS</div>
        <div class="card-body">
          <canvas id="bottomProductsChart"></canvas>
          <div id ="bottom_products"></div>
        </div>
      </div>


      <div class="card" style="grid-column: span 2;">
        <div class="card-header">Current Pending Orders & Complaints</div>
        <div class="card-body">
          <div id ="current_orders"></div>
        </div>
      </div>


    </div>
  </div>

  <script src="manager_charts.js"></script>
</body>
</html>
