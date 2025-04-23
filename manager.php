
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body 
    {
        background-color: #FAF9F6;
        color: #0d1b2a;
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
        text-align: center;
        align-items: center;           /* Center vertically */
        margin: 0 auto; /* Added margin property to center the content */
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    
    h1 
    {
        text-align: center;
        color: #0d1b2a;
        font-size: 64px;
        text-align: center;
    }

    header h1
    {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        text-align: center;
        margin: auto;
        padding: 5px 5px;
        background-color: #afdcec;
        border-radius: 10px;
        max-width: 1000px; /* Set a maximum width for the header */
        border: 5px solid #0d1b2a;
        margin-bottom: 40px;    /* Adds space below the header */
        margin-top: 60px;

    }
    
    .dashboard-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
      margin-top: 20px;
    }
    
    @media (min-width: 768px) {
      .dashboard-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    
    .card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      overflow: hidden;
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
      padding: 20px;
      max-height: 500px;
      overflow-y: auto;
    }
    
    .full-width {
      grid-column: 1 / -1;
    }
    
    table { 
      border-collapse: collapse; 
      width: 100%; 
      margin-top: 1em;
      font-size: 0.9em;
    }
    
    th { 
      background-color: #f2f2f2;
      text-align: left;
      position: sticky;
      top: 0;
      z-index: 10;
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
    
    canvas { 
      max-width: 100%;
      margin: 10px 0;
    }
    
    .loading {
      text-align: center;
      color: #666;
      padding: 20px;
    }
    
    .error {
      color: #f44336;
      text-align: center;
      padding: 20px;
    }
    
    .no-data {
      color: #666;
      text-align: center;
      padding: 20px;
      font-style: italic;
    }
    
    .chart-container {
      position: relative;
      height: 300px;
    }
  </style>
</head>

<body>
  <header>
    <h1>Manager Dashboard</h1>
  </header>
  
  <div class="container">
    <div class="dashboard-grid">
      <!-- Order Status Chart -->
      <div class="card">
        <div class="card-header">Order Status Distribution</div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="orderStatusChart"></canvas>
          </div>
          <div id="order_status"></div>
        </div>
      </div>
      
      <!-- Payment Status Chart -->
      <div class="card">
        <div class="card-header">Payment Status</div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="paymentStatusChart"></canvas>
          </div>
          <div id="payment_status"></div>
        </div>
      </div>
      
      <!-- Customer Types Chart -->
      <div class="card">
        <div class="card-header">Customer Types</div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="customerTypesChart"></canvas>
          </div>
          <div id="customer_types"></div>
        </div>
      </div>
      
      <!-- Top Products Chart -->
      <div class="card full-width">
        <div class="card-header">Top 10 Products</div>
        <div class="card-body">
          <div class="chart-container" style="height: 400px;">
            <canvas id="topProductsChart"></canvas>
          </div>
          <div id="top_products"></div>
        </div>
      </div>
      
      <!-- Revenue by Salesperson -->
      <div class="card full-width">
        <div class="card-header">Revenue by Salesperson</div>
        <div class="card-body">
          <div id="revenue" class="loading">Loading revenue data...</div>
        </div>
      </div>
      
      <!-- All Employees -->
      <div class="card full-width">
        <div class="card-header">All Employees</div>
        <div class="card-body">
          <div id="all-employees" class="loading">Loading employee data...</div>
        </div>
      </div>

  <script src="charts.js"></script>
</body>
</html>