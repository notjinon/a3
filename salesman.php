
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Salesman Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #FAF9F6;
      color: #0d1b2a;
      font-family: 'Roboto', sans-serif;
      font-size: 14px;
      text-align: center;
      margin: 0 auto;
    }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    header h1 {
      background-color: #afdcec;
      color: #0d1b2a;
      font-size: 48px;
      padding: 10px;
      border-radius: 10px;
      border: 5px solid #0d1b2a;
      margin: 40px auto;
      max-width: 800px;
    }
    .dashboard-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-top: 20px; }
    @media (min-width: 768px) { .dashboard-grid { grid-template-columns: repeat(2,1fr); } }
    .card { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
    .card-header { background-color: #f0f0f0; padding: 15px 20px; font-weight: bold; border-bottom: 1px solid #ddd; }
    .card-body { padding: 20px; }
    .full-width { grid-column: 1 / -1; }
    form { display: flex; flex-direction: column; gap: 10px; max-width: 600px; margin: 0 auto; }
    label { text-align: left; }
    input, select, textarea, button { padding: 8px; font-size: 1em; }
    table { width: 100%; border-collapse: collapse; margin-top: 1em; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background-color: #f2f2f2; position: sticky; top: 0; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .status-paid { color: green; }
    .status-unpaid { color: red; }
    .status-complaint { background-color: #ffe6e6; }
    .chart-container { position: relative; height: 300px; }
  </style>
</head>
<body>
  <header><h1>Salesman Dashboard</h1></header>
  <div class="container">
    <div class="dashboard-grid">
      <!-- Module: Create New -->
      <div class="card">
        <div class="card-header">Create New</div>
        <div class="card-body">
          <form id="createForm" method="post" action="salesman_actions.php">
            <select name="action_type" id="action_type" onchange="toggleCreateFields()">
              <option value="customer">Customer</option>
              <option value="order">Order</option>
              <option value="pickup">Pickup Request</option>
            </select>
            <div id="customerFields">
              <label for="cust_name">Name</label>
              <input type="text" name="cust_name" id="cust_name" required>
              <label for="cust_email">Email</label>
              <input type="email" name="cust_email" id="cust_email" required>
              <label for="cust_address">Address</label>
              <input type="text" name="cust_address" id="cust_address" required>
            </div>
            <div id="orderFields" style="display:none;">
              <label for="order_cust_id">Customer ID</label>
              <input type="text" name="order_cust_id" id="order_cust_id">
              <label for="order_total">Total Price</label>
              <input type="number" step="0.01" name="order_total" id="order_total">
            </div>
            <div id="pickupFields" style="display:none;">
              <label for="pickup_order_id">Order ID</label>
              <input type="text" name="pickup_order_id" id="pickup_order_id">
              <label for="pickup_date">Pickup Date</label>
              <input type="date" name="pickup_date" id="pickup_date">
            </div>
            <button type="submit">Create</button>
          </form>
        </div>
      </div>

      <!-- Module: Find Customer -->
      <div class="card">
        <div class="card-header">Find Customer</div>
        <div class="card-body">
          <input type="text" id="searchCustomer" placeholder="Enter customer name or ID" onkeyup="fetchCustomer()">
          <div id="customerInfo"></div>
          <div class="chart-container" id="lifetimeContainer" style="display:none;">
            <canvas id="lifetimeChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Module: Find Order -->
      <div class="card">
        <div class="card-header">Find Order</div>
        <div class="card-body">
          <input type="text" id="searchOrder" placeholder="Enter order ID" onkeyup="fetchOrder()">
          <div id="orderInfo"></div>
        </div>
      </div>

      <!-- Module: Complaints -->
      <div class="card full-width">
        <div class="card-header">Complaints</div>
        <div class="card-body">
          <div id="complaintsList">Loading complaints...</div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleCreateFields() {
      const type = document.getElementById('action_type').value;
      document.getElementById('customerFields').style.display = type==='customer'?'block':'none';
      document.getElementById('orderFields').style.display = type==='order'?'block':'none';
      document.getElementById('pickupFields').style.display = type==='pickup'?'block':'none';
    }

    // Placeholder JS: implement AJAX fetchCustomer(), fetchOrder(), render charts
    function fetchCustomer() {
      const q = document.getElementById('searchCustomer').value;
      if (!q) return;
      fetch(`api.php?action=getCustomer&query=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
          // render customer info, orders, highlight complaint status
          let html = `<h3>${data.name} (ID: ${data.id})</h3>`;
          html += '<table><tr><th>Order ID</th><th>Total</th><th>Status</th></tr>';
          data.orders.forEach(o=>{
            const cls = o.complaint? 'status-complaint':'';
            html += `<tr class="${cls}"><td>${o.id}</td><td>${o.total}</td><td>${o.paid?'<span class="status-paid">Paid</span>':'<span class="status-unpaid">Unpaid</span>'}</td></tr>`;
          });
          html += '</table>';
          document.getElementById('customerInfo').innerHTML = html;

          // show lifetime value chart
          document.getElementById('lifetimeContainer').style.display = 'block';
          const ctx = document.getElementById('lifetimeChart');
          new Chart(ctx, { type:'line', data: { labels:data.lifetm.labels, datasets:[{ label:'Lifetime Value', data:data.lifetm.values }] } });
        });
    }

    function fetchOrder() {
      const id = document.getElementById('searchOrder').value;
      if (!id) return;
      fetch(`api.php?action=getOrder&order_id=${encodeURIComponent(id)}`)
        .then(res=>res.json())
        .then(o => {
          let html = `<h3>Order ${o.id}</h3><p>Customer: ${o.customer.name} (ID: ${o.customer.id})</p>`;
          html += `<p>Total: ${o.total}</p>`;
          html += `<p>Status: ${o.paid?'<span class="status-paid">Paid</span>':'<span class="status-unpaid">Unpaid</span>'}</p>`;
          if (o.pickup) html += `<p>Pickup: ${o.pickup.date} (Status: ${o.pickup.status})</p>`;
          if (o.complaint) html += `<p class="status-complaint">Complaint: ${o.complaint.text}</p>`;
          document.getElementById('orderInfo').innerHTML = html;
        });
    }

    // Load complaints
    fetch('api.php?action=listComplaints')
      .then(res=>res.json())
      .then(list=>{
        let html='<table><tr><th>ID</th><th>Order</th><th>Text</th><th>Resolution</th><th>Action</th></tr>';
        list.forEach(c=>{
          html+=`<tr><td>${c.id}</td><td>${c.order_id}</td>
            <td>${c.text}</td>
            <td><textarea id="res_${c.id}">${c.resolution||''}</textarea></td>
            <td><button onclick="updateComplaint(${c.id})">Save</button></td></tr>`;
        });
        html+='</table>';
        document.getElementById('complaintsList').innerHTML = html;
      });

    function updateComplaint(id) {
      const res = document.getElementById(`res_${id}`).value;
      fetch('api.php?action=updateComplaint', { method:'POST', body: JSON.stringify({id,res}), headers:{'Content-Type':'application/json'} })
        .then(()=>alert('Saved'));
    }
  </script>
</body>
</html>