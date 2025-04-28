<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Salesman Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap');


    /* Base styles */
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #FAF9F6;
      color: #0d1b2a;
    }


    /* Header styles */
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


    /* Container and layout */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
      display: flex;
      gap: 20px;
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


    /* Dashboard Grid */
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


    .full-width {
      grid-column: 1 / -1;
    }


    /* Card styles */
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
      padding: 20px;
      flex-grow: 1;
    }


    /* Horizon picker */
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


    /* Form elements */
    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 600px;
      margin: 0 auto;
    }


    label {
      text-align: left;
    }


    input, select, textarea, button {
      padding: 8px;
      font-size: 1em;
    }


    /* Table styles */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1em;
      font-size: 0.9em;
    }


    th, td {
      border: 1px solid #ddd;
      padding: 8px;
    }


    th {
      background-color: #f2f2f2;
      position: sticky;
      top: 0;
    }


    tr:nth-child(even) {
      background-color: #f9f9f9;
    }


    tr:hover {
      background-color: #f1f1f1;
    }


    /* Status styles */
    .status-paid {
      color: green;
    }


    .status-unpaid {
      color: red;
    }


    .status-complaint {
      background-color: #ffe6e6;
    }


    /* Chart container */
    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
    }


    /* Utility classes */
    .loading, .no-data, .error {
      text-align: center;
      padding: 20px;
    }


    .error {
      color: #f44336;
    }


    .no-data {
      color: #666;
      font-style: italic;
    }


    .ui-autocomplete {
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .ui-autocomplete .ui-menu-item {
      padding: 8px 12px;
      border-bottom: 1px solid #eee;
  }

  .ui-autocomplete .ui-menu-item:last-child {
      border-bottom: none;
  }

  .ui-autocomplete .ui-menu-item div {
      line-height: 1.4;
  }

  .item-search {
      width: 200px;
      padding: 4px 8px;
  }

  .item-name {
      display: block;
      font-size: 0.85em;
      color: #666;
      margin-top: 4px;
  }

  .quantity {
      width: 60px;
      padding: 4px;
  }

  .removeRow {
      padding: 4px 8px;
      background: #ff4444;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
  }

  .removeRow:hover {
      background: #cc0000;
  }

  #addMoreItems {
      margin: 10px 0;
      padding: 8px 16px;
      background: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
  }

  #addMoreItems:hover {
      background: #45a049;
  }

  .total-section {
      margin-top: 20px;
      padding: 10px;
      background: #f8f8f8;
      border-radius: 4px;
  }
  .complaint-status {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
  }
  .complaint-none { background-color: #28a745; color: white; }
  .complaint-pending { background-color: #ffc107; }
  .complaint-resolved { background-color: #17a2b8; color: white; }
  .complaint-escalated { background-color: #dc3545; color: white; }
  </style>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>
  <header><h1>SALESMAN DASHBOARD</h1></header>
  <!-- Time horizon picker -->
  <div class="horizon-picker" id="time-horizon-picker">
    <button data-horizon="1M">1M</button>
    <button data-horizon="3M">3M</button>
    <button data-horizon="6M" class="active">6M</button>
    <button data-horizon="1Y">1Y</button>
    <button data-horizon="ALL">MAX</button>
  </div>
  <div class="container">


   
    <div class="dashboard-grid">
      <!-- Module: Create New -->
      <div class="card">
        <div class="card-header">Create New</div>
        <div class="card-body">
          <form id="createForm" method="POST" action="salesman_create_instance.php">
            <select name="action_type" id="action_type" onchange="toggleCreateFields()">
              <option value="indiv_customer">Individual Customer</option>
              <option value="company_customer">Company Customer</option>
              <option value="order">Order</option>
              <option value="pickup">Pickup Request</option>
            </select>
            <div id="indivCustomerFields">
              <label for="cust_fname">First Name</label>
              <input type="text" name="cust_fname" id="cust_fname">
              <label for="cust_lname">Last Name</label>
              <input type="text" name="cust_lname" id="cust_lname">
              <label for="cust_email">Email</label>
              <input type="email" name="cust_email" id="cust_email">
              <label for="cust_address">Address</label>
              <input type="text" name="cust_address" id="cust_address">
            </div>
            <div id="companyCustomerFields" style="display:none;">
              <label for="comp_fname">Contact First Name</label>
              <input type="text" name="comp_fname" id="comp_fname">
              <label for="comp_lname">Contact Last Name</label>
              <input type="text" name="comp_lname" id="comp_lname">
              <label for="comp_email">Contact Email</label>
              <input type="email" name="comp_email" id="comp_email">
              <label for="comp_address">Contact Address</label>
              <input type="text" name="comp_address" id="comp_address">
              <label for="company_name">Company Name</label>
              <input type="text" name="company_name" id="company_name">
              <label for="tax_id">Tax ID</label>
              <input type="text" name="tax_id" id="tax_id">
            </div>
            <div id="orderFields" style="display:none;">
              <label for="order_cust_id">Customer ID</label>
              <input type="text" name="order_cust_id" id="order_cust_id">


              <!-- Create Order Functionality -->
              <table>
                <thead>
                  <tr>
                    <th>ITEM ID</th>
                    <th>QUANTITY</th>
                    <th>SUBTOTAL</th>
                    <th>ACTION</th> <!-- Added column for the Remove button -->
                  </tr>
                </thead>
                <tbody id="orderItems">
                  <tr>
                    <td>
                      <input type="text" name="item_search[]" class="item-search" placeholder="Search item...">
                      <input type="hidden" name="item_id[]" class="item-id">
                      <span class="item-name"></span>
                    </td>
                    <td><input type="number" name="quantity[]" class="quantity" min="1" value="1" oninput="updateOrderTotal()"></td>
                    <td class="subtotal">$0.00</td>
                    <td><button type="button" class="removeRow" onclick="removeOrderRow(this)">Remove</button></td>
                  </tr>
                </tbody>
              </table>
              <button type="button" id="addMoreItems" onclick="addOrderRow()">+ Add more</button>
              <div class="total-section">
                <strong>TOTAL: </strong><span id="orderTotal">$0.00</span>
              </div>
            </div>
            <!-- Create Pickup Functionality -->
            <div id="pickupFields" style="display:none;">
              <label for="pickup_order_id">Order ID</label>
              <input type="text" name="pickup_order_id" id="pickup_order_id">
              <label for="pickup_date">Pickup Date</label>
              <input type="date" name="pickup_date" id="pickup_date">
              <label for="scheduled_by">Employee Responsible</label>
              <input type="text" name="scheduled_by" id="scheduled_by">
            </div>
            <button type="submit">Create</button>
          </form>
        </div>
      </div>

      <!-- Module: Find Customer -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Find Customer</span>
            <button id="editCustomerBtn" class="btn btn-secondary" style="display: none;" onclick="enableCustomerEdit()">
                <i class="fas fa-edit"></i> Edit Customer
            </button>
        </div>
        <div class="card-body">
            <div class="search-section mb-3">
                <div class="input-group">
                    <input type="text" 
                          id="searchCustomer" 
                          class="form-control" 
                          placeholder="Search by ID, name, email, company name, or tax ID">
                    <div class="input-group-append">
                        <button class="btn btn-primary" onclick="fetchCustomer()">Search</button>
                    </div>
                </div>
            </div>

            <div id="customerInfo" class="customer-details mb-3" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer Details</h5>
                        <form id="customerForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> <span id="customerId"></span></p>
                                    <p><strong>Type:</strong> <span id="customerType"></span></p>
                                    <div class="form-group">
                                        <label><strong>Name:</strong></label>
                                        <input type="text" class="form-control customer-field" id="customerName" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Email:</strong></label>
                                        <input type="email" class="form-control customer-field" id="customerEmail" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Phone:</strong></label>
                                        <input type="tel" class="form-control customer-field" id="customerPhone" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Street Address:</strong></label>
                                        <input type="text" class="form-control customer-field" id="customerStreet" disabled>
                                    </div>
                                    <div class="form-row">
                                        <div class="col">
                                            <label><strong>City:</strong></label>
                                            <input type="text" class="form-control customer-field" id="customerCity" disabled>
                                        </div>
                                        <div class="col">
                                            <label><strong>State:</strong></label>
                                            <select class="form-control customer-field" id="customerState" disabled>
                                                <!-- States will be populated via JS -->
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label><strong>ZIP:</strong></label>
                                            <input type="text" class="form-control customer-field" id="customerZip" disabled>
                                        </div>
                                    </div>
                                    <div id="companyDetails" style="display: none;">
                                        <div class="form-group">
                                            <label><strong>Company:</strong></label>
                                            <input type="text" class="form-control customer-field" id="companyName" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Tax ID:</strong></label>
                                            <input type="text" class="form-control customer-field" id="taxId" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-3" id="saveSection" style="display: none;">
                                <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Customer Value Section -->
            <div id="customerValue" class="mb-3" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer Lifetime Value</h5>
                        <h3 class="text-primary" id="totalValue">$0.00</h3>
                        <small class="text-muted">Past 6 months total revenue</small>
                    </div>
                </div>
            </div>

            <!-- Orders Section -->
            <div id="ordersSection" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Orders</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Complaints</th>
                                    </tr>
                                </thead>
                                <tbody id="ordersList">
                                    <!-- Orders will be populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-section">
                <div id="lifetimeContainer" style="display:none;">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Revenue Over Time</h5>
                            <canvas id="lifetimeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

      <!-- Module: Find Order -->
      <div class="card">
        <div class="card-header">Find Order or Pickup</div>
        <div class="card-body">
          <input type="text" id="searchOrder" placeholder="Enter order ID">
          <button onclick="fetchOrder()">Search</button>
          <div id="orderInfo"></div>
        </div>
      </div>

      <!-- Module: Edit Customer -->
      <div class="card">
        <div class="card-header">Edit Customer</div>
        <div class="card-body">
          <input type="text" id="editCustId" placeholder="Enter Customer ID">
          <button onclick="loadCustomerForEdit()">Load</button>

          <form id="editCustomerForm" style="display:none;" onsubmit="return submitCustomerEdit();">
            <input type="hidden" name="action_type" value="update_customer">
            <input type="hidden" id="edit_cust_id" name="cust_id">
            <label>First Name <input type="text" id="edit_fname" name="cust_fname"></label>
            <label>Last Name  <input type="text" id="edit_lname" name="cust_lname"></label>
            <label>Email      <input type="email" id="edit_email" name="cust_email"></label>
            <label>Address    <input type="text" id="edit_address" name="cust_address"></label>
            <button type="submit">Save Changes</button>
          </form>
        </div>
      </div>

      <!-- Module: Edit Pickup -->
      <div class="card">
        <div class="card-header">Edit Pickup</div>
        <div class="card-body">
          <input type="text" id="editPickupId" placeholder="Enter Pickup ID">
          <button onclick="loadPickupForEdit()">Load</button>

      <form id="editPickupForm" style="display:none;" onsubmit="return submitPickupEdit();">
        <input type="hidden" name="action_type" value="update_pickup">
        <input type="hidden" id="edit_pickup_id" name="pickup_id">
        <label>Date   <input type="date" id="edit_pickup_date" name="pickup_date"></label>
        <label>Status
          <select id="edit_pickup_status" name="status">
            <option>Scheduled</option>
            <option>Completed</option>
            <option>Cancelled</option>
          </select>
        </label>
        <button type="submit">Save Changes</button>
      </form>
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
  // Global variable for time horizon
  let currentHorizon = '6M';
 
  // Form field toggling
  function toggleCreateFields() {
  var action = document.getElementById('action_type').value;
  var indivFields = document.getElementById('indivCustomerFields');
  var compFields = document.getElementById('companyCustomerFields');
  var orderFields = document.getElementById('orderFields');
  var pickupFields = document.getElementById('pickupFields');


  // Hide all initially
  indivFields.style.display = 'none';
  compFields.style.display = 'none';
  orderFields.style.display = 'none';
  pickupFields.style.display = 'none';


  // Remove required from all first
  // Individual
  document.getElementById('cust_fname').required = false;
  document.getElementById('cust_lname').required = false;
  document.getElementById('cust_email').required = false;
  document.getElementById('cust_address').required = false;
  // Company
  document.getElementById('comp_fname').required = false;
  document.getElementById('comp_lname').required = false;
  document.getElementById('comp_email').required = false;
  document.getElementById('comp_address').required = false;
  document.getElementById('company_name').required = false;
  document.getElementById('tax_id').required = false;
  // Order
  document.getElementById('order_cust_id').required = false;
  // Pickup
  document.getElementById('pickup_order_id').required = false;
  document.getElementById('pickup_date').required = false;


  if (action === 'indiv_customer') {
    indivFields.style.display = '';
    document.getElementById('cust_fname').required = true;
    document.getElementById('cust_lname').required = true;
    document.getElementById('cust_email').required = true;
    document.getElementById('cust_address').required = true;
  } else if (action === 'company_customer') {
    compFields.style.display = '';
    document.getElementById('comp_fname').required = true;
    document.getElementById('comp_lname').required = true;
    document.getElementById('comp_email').required = true;
    document.getElementById('comp_address').required = true;
    document.getElementById('company_name').required = true;
    document.getElementById('tax_id').required = true;
  } else if (action === 'order') {
    orderFields.style.display = '';
    document.getElementById('order_cust_id').required = true;
  } else if (action === 'pickup') {
    pickupFields.style.display = '';
    document.getElementById('pickup_order_id').required = true;
    document.getElementById('pickup_date').required = true;
  }
}




// Call on page load to set initial state
window.onload = toggleCreateFields;


// Call once on page load for initial state
window.onload = toggleCreateFields;    
  // Utility to handle high-DPR screens
  function fixCanvasResolution(canvas) {
    const dpr = window.devicePixelRatio || 1, r = canvas.getBoundingClientRect();
    canvas.width = r.width * dpr;
    canvas.height = r.height * dpr;
    canvas.getContext('2d').scale(dpr, dpr);
  }
 
  // Nicely format table headers
  function formatHeader(h) {
    return h.replace(/_/g,' ')
            .replace(/([A-Z])/g,' $1')
            .replace(/^./, s => s.toUpperCase())
            .trim();
  }
 
  // Generic data fetcher
  function fetchData(type, callback) {
    const el = document.getElementById(type);
    if (!el) return;
    el.innerHTML = '<div class="loading">Loading data...</div>';


    const url = `../includes/data.php?type=${type}`
              + `&horizon=${currentHorizon}`
              + `&_=${Date.now()}`;
    console.log('FETCH', url);


    fetch(url)
      .then(r => r.ok
        ? r.json()
        : r.json().then(e => { throw new Error(e.error || `HTTP ${r.status}`); })
      )
      .then(data => callback(type, data))
      .catch(err => {
        el.innerHTML = `<div class="error">Error: ${err.message}</div>`;
        console.error(type, err);
      });
  }
 
  // Customer search functionality
  function fetchCustomer() {
    const q = document.getElementById('searchCustomer').value.trim();
    if (!q) {
      alert("Please enter a customer name or ID");
      return;
    }
   
    document.getElementById('customerInfo').innerHTML = '<p>Loading customer data...</p>';
    document.getElementById('lifetimeContainer').style.display = 'none';
   
    fetch(`../includes/data.php?type=find-customer&query=${encodeURIComponent(q)}`)
      .then(res => {
        if (!res.ok) {
          throw new Error('Network response was not ok');
        }
        return res.json();
      })
      .then(data => {
        if (!data || Object.keys(data).length === 0) {
          document.getElementById('customerInfo').innerHTML = '<p>No customer found with that name.</p>';
          return;
        }
       
        let html = `<h3>${data.name} (Customer ID: ${data.id})</h3>`;
        if (data.orders && data.orders.length > 0) {
          html += '<table><tr><th>Order ID</th><th>Total</th><th>Status</th></tr>';
          data.orders.forEach(o => {
            const cls = o.complaint ? 'status-complaint' : '';
            html += `<tr class="${cls}"><td>${o.id}</td><td>$${o.total.toFixed(2)}</td><td>${o.paid ? '<span class="status-paid">Paid</span>' : '<span class="status-unpaid">Unpaid</span>'}</td></tr>`;
          });
          html += '</table>';
        } else {
          html += '<p>No orders found for this customer.</p>';
        }
        document.getElementById('customerInfo').innerHTML = html;


      })
      .catch(error => {
        console.error('Error fetching customer:', error);
        document.getElementById('customerInfo').innerHTML = '<p>Error loading customer data. Please try again.</p>';
      });
}

  // Order search functionality
  function fetchOrder() {
    const id = document.getElementById('searchOrder').value;
    if (!id) {
      alert("Please enter an order ID");
      return;
    }
   
    // Show loading indicator
    document.getElementById('orderInfo').innerHTML = '<p>Loading order data...</p>';
   
    fetch(`api.php?action=getOrder&order_id=${encodeURIComponent(id)}`)
      .then(res => {
        if (!res.ok) {
          throw new Error('Network response was not ok');
        }
        return res.json();
      })
      .then(o => {
        if (!o || Object.keys(o).length === 0) {
          document.getElementById('orderInfo').innerHTML = '<p>No order found with that ID.</p>';
          return;
        }
       
        let html = `<h3>Order ${o.id}</h3>`;
        if (o.customer) {
          html += `<p>Customer: ${o.customer.name} (ID: ${o.customer.id})</p>`;
        }
        html += `<p>Total: $${o.total.toFixed(2)}</p>`;
        html += `<p>Status: ${o.paid ? '<span class="status-paid">Paid</span>' : '<span class="status-unpaid">Unpaid</span>'}</p>`;
        if (o.pickup) html += `<p>Pickup: ${o.pickup.date} (Status: ${o.pickup.status})</p>`;
        if (o.complaint) html += `<p class="status-complaint">Complaint: ${o.complaint.text}</p>`;
       
        document.getElementById('orderInfo').innerHTML = html;
      })
      .catch(error => {
        console.error('Error fetching order:', error);
        document.getElementById('orderInfo').innerHTML = '<p>Error loading order data. Please try again.</p>';
      });
  }

  // Complaints functionality
  function loadComplaints() {
  const url = `../includes/data.php?type=list-complaints&_=${Date.now()}`;
  fetch(url)
    .then(res => res.ok ? res.json() : Promise.reject(res.statusText))
    .then(list => {
      if (!list || list.length === 0) {
        return document.getElementById('complaintsList')
                       .innerHTML = '<p>No complaints found.</p>';
      }
      let html = '<table><tr><th>ID</th><th>Customer</th><th>Text</th><th>Status</th><th>Action</th></tr>';
      list.forEach(c => {
        html += `<tr>
          <td>${c.id}</td>
          <td>${c.customer_id}</td>
          <td>${c.text}</td>
          <td>${c.status}</td>
          <td>
            <textarea id="res_${c.id}"></textarea>
            <button onclick="updateComplaint(${c.id})">Save</button>
          </td>
        </tr>`;
      });
      html += '</table>';
      document.getElementById('complaintsList').innerHTML = html;
    })
    .catch(err => {
      console.error('Error loading complaints:', err);
      document.getElementById('complaintsList')
              .innerHTML = '<p>Error loading complaints. Please try again.</p>';
    });
}


  function updateComplaint(id) {
    const res = document.getElementById(`res_${id}`).value;
    fetch(`../includes/customer_api.php?action=update`, {
      method: 'POST',
      body: JSON.stringify({ id, resolution: res }),
      headers: { 'Content-Type': 'application/json' }
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        alert('Complaint resolution saved successfully');
      })
      .catch(error => {
        console.error('Error updating complaint:', error);
        alert('Error saving complaint resolution. Please try again.');
      });
  }
 
  // TABLE RENDERER
  function renderTable(type, data) {
    const c = document.getElementById(type);
    if (!c) return;
    if (!data || !data.length) {
      return void (c.innerHTML = '<div class="no-data">No data available</div>');
    }


    const cols = Object.keys(data[0]);
    let html = `<table><thead><tr>${cols.map(col=>`<th>${formatHeader(col)}</th>`).join('')}</tr></thead><tbody>`;
    data.forEach(row => {
      html += '<tr>' + cols.map(col => {
        const val = row[col];
        if (/price|revenue|amount/i.test(col))  return `<td>$${(+val).toFixed(2)}</td>`;
        if (/date/i.test(col))                  return `<td>${val?new Date(val).toLocaleDateString():''}</td>`;
        return `<td>${val}</td>`;
      }).join('') + '</tr>';
    });
    c.innerHTML = html + '</tbody></table>';
  }
 
  // Initialize the  page
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up horizon picker and fetching charts');
   
    // Wire up the time-horizon picker buttons
    document.querySelectorAll('#time-horizon-picker button').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelector('#time-horizon-picker .active')?.classList.remove('active');
        btn.classList.add('active');
        currentHorizon = btn.dataset.horizon;
        console.log('Horizon →', currentHorizon);


        // Re-fetch all charts & tables
        [
          ['all-employees',      renderTable],
          ['revenue',            renderTable],
          ['top_products',       renderTopProductsChart],
          ['order_status',       renderOrderStatusChart],
          ['customer_types',     renderCustomerTypesChart],
          ['payment_status',     renderPaymentStatusChart],
          ['salesperson_summary',renderSalespersonSummary]
        ].forEach(([type, fn]) => fetchData(type, fn));
      });
    });
   
    // Initial fetch (6M default)
    loadComplaints();
    fetchData('top_products',       renderTopProductsChart);
    fetchData('order_status',       renderOrderStatusChart);
    fetchData('customer_types',     renderCustomerTypesChart);
    fetchData('payment_status',     renderPaymentStatusChart);
    fetchData('salesperson_summary',renderSalespersonSummary);
  });


  function toggleCreateFields() {
    const actionType = document.getElementById("action_type").value;
    document.getElementById("indivCustomerFields").style.display = actionType === "indiv_customer" ? "block" : "none";
    document.getElementById("companyCustomerFields").style.display = actionType === "company_customer" ? "block" : "none";
    document.getElementById("orderFields").style.display = actionType === "order" ? "block" : "none";
    document.getElementById("pickupFields").style.display = actionType === "pickup" ? "block" : "none";
  }


  function initializeItemAutocomplete(element) {
    $(element).autocomplete({
        source: "../includes/get_items.php",
        minLength: 2,
        select: function(event, ui) {
            // Get the row elements
            const row = $(this).closest('tr');
            const hiddenInput = row.find('.item-id');
            const nameSpan = row.find('.item-name');
            const quantityInput = row.find('.quantity');
            
            // Update the hidden input and show the item details
            hiddenInput.val(ui.item.id);
            nameSpan.html(`
                <small>
                    ${ui.item.brand} - ${ui.item.category}
                    (${ui.item.size}${ui.item.sizeunit})
                    <br>Stock: ${ui.item.stock}
                </small>
            `);
            
            // Limit quantity input max value to stock
            quantityInput.attr('max', ui.item.stock);
            
            // If quantity would exceed stock, adjust it
            if (parseInt(quantityInput.val()) > ui.item.stock) {
                quantityInput.val(ui.item.stock);
            }
            
            // Update quantity to trigger subtotal calculation
            quantityInput.trigger('input');
            
            return false;
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append(`
                <div>
                    <strong>#${item.id}</strong> ${item.label}<br>
                    <small>${item.brand} | ${item.category} | Stock: ${item.stock}</small>
                </div>
            `)
            .appendTo(ul);
    };
}

function addOrderRow() {
    const tbody = document.getElementById('orderItems');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <input type="text" name="item_search[]" class="item-search" placeholder="Search item...">
            <input type="hidden" name="item_id[]" class="item-id">
            <span class="item-name"></span>
        </td>
        <td>
            <input type="number" name="quantity[]" class="quantity" min="1" value="1" oninput="updateOrderTotal()">
        </td>
        <td class="subtotal">$0.00</td>
        <td>
            <button type="button" class="removeRow" onclick="removeOrderRow(this)">Remove</button>
        </td>
    `;
    tbody.appendChild(newRow);
    
    // Initialize autocomplete for the new row
    initializeItemAutocomplete(newRow.querySelector('.item-search'));
}

function removeOrderRow(button) {
    button.closest('tr').remove();
    updateOrderTotal();
}

function updateOrderTotal() {
    let total = 0;
    document.querySelectorAll('#orderItems tr').forEach(row => {
        const subtotalText = row.querySelector('.subtotal').textContent;
        const subtotal = parseFloat(subtotalText.replace('$', '')) || 0;
        total += subtotal;
    });
    document.getElementById('orderTotal').textContent = '$' + total.toFixed(2);
}

// Initialize existing rows when the page loads
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.item-search').forEach(input => {
        initializeItemAutocomplete(input);
    });
});
 
</script>
<script>
  // Global variables for chart and customer data
let lifetimeChart = null;
let currentCustomer = null;

// Initialize on document load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select for states
    const stateSelect = document.getElementById('customerState');
    const states = ['AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];
    states.forEach(state => {
        const option = document.createElement('option');
        option.value = state;
        option.textContent = state;
        stateSelect.appendChild(option);
    });

    // Add form submit handler
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        handleCustomerUpdate();
    });

    // Add enter key handler for search
    document.getElementById('searchCustomer').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            fetchCustomer();
        }
    });
});

async function fetchCustomer() {
    const searchTerm = document.getElementById('searchCustomer').value.trim();
    if (!searchTerm) {
        showError('Please enter a search term');
        return;
    }

    try {
        showLoading();
        const response = await fetch(`customer_api.php?action=search&term=${encodeURIComponent(searchTerm)}`);
        const data = await response.json();

        if (data.success) {
            currentCustomer = data.data;
            displayCustomerInfo(data.data);
            loadOrders(data.data.PersonID);
        } else {
            showError('Customer not found');
        }
    } catch (error) {
        showError('Error fetching customer data');
        console.error(error);
    } finally {
        hideLoading();
    }
}

function displayCustomerInfo(customer) {
    // Show customer info section and edit button
    document.getElementById('customerInfo').style.display = 'block';
    document.getElementById('editCustomerBtn').style.display = 'block';

    // Basic info
    document.getElementById('customerId').textContent = customer.PersonID;
    document.getElementById('customerType').textContent = customer.CustomerType;
    document.getElementById('customerName').value = customer.FullName;
    document.getElementById('customerEmail').value = customer.Email;
    document.getElementById('customerPhone').value = customer.PhoneNumber;
    
    // Address fields
    document.getElementById('customerStreet').value = customer.StreetAddress;
    document.getElementById('customerCity').value = customer.City;
    document.getElementById('customerState').value = customer.State;
    document.getElementById('customerZip').value = customer.ZipCode;

    // Company specific info
    const companyDetails = document.getElementById('companyDetails');
    if (customer.CustomerType === 'Company') {
        companyDetails.style.display = 'block';
        document.getElementById('companyName').value = customer.CompanyName;
        document.getElementById('taxId').value = customer.TaxID;
    } else {
        companyDetails.style.display = 'none';
    }
}

async function loadOrders(personId) {
    try {
        const response = await fetch(`customer_api.php?action=orders&person_id=${personId}`);
        const data = await response.json();

        if (data.success) {
            displayOrders(data.data.orders);
            updateChart(data.data.orders);
            calculateLifetimeValue(data.data.orders);
        }
    } catch (error) {
        showError('Error loading orders');
        console.error(error);
    }
}

function displayOrders(orders) {
    const ordersList = document.getElementById('ordersList');
    ordersList.innerHTML = '';
    
    orders.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${order.OrderID}</td>
            <td>${formatDate(order.OrderDate)}</td>
            <td>$${parseFloat(order.Total).toFixed(2)}</td>
            <td><span class="badge ${getStatusClass(order.Status)}">${order.Status}</span></td>
            <td><span class="badge ${getComplaintClass(order.ComplaintStatus)}">${order.ComplaintStatus || 'None'}</span></td>
        `;
        ordersList.appendChild(row);
    });

    document.getElementById('ordersSection').style.display = 'block';
}

function updateChart(orders) {
    const ctx = document.getElementById('lifetimeChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (lifetimeChart) {
        lifetimeChart.destroy();
    }

    // Prepare data
    const labels = orders.map(order => order.month);
    const values = orders.map(order => order.total_amount);

    lifetimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Revenue',
                data: values,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    document.getElementById('lifetimeContainer').style.display = 'block';
}

function calculateLifetimeValue(orders) {
    const total = orders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
    document.getElementById('totalValue').textContent = `$${total.toFixed(2)}`;
    document.getElementById('customerValue').style.display = 'block';
}

function enableCustomerEdit() {
    // Enable all form fields
    document.querySelectorAll('.customer-field').forEach(field => {
        field.disabled = false;
    });

    // Show save section
    document.getElementById('saveSection').style.display = 'block';
    // Hide edit button
    document.getElementById('editCustomerBtn').style.display = 'none';
}

function cancelEdit() {
    // Disable all form fields
    document.querySelectorAll('.customer-field').forEach(field => {
        field.disabled = true;
    });

    // Hide save section
    document.getElementById('saveSection').style.display = 'none';
    // Show edit button
    document.getElementById('editCustomerBtn').style.display = 'block';

    // Reset form to current customer data
    if (currentCustomer) {
        displayCustomerInfo(currentCustomer);
    }
}

async function handleCustomerUpdate() {
    const formData = new FormData(document.getElementById('customerForm'));
    formData.append('person_id', currentCustomer.PersonID);
    
    try {
        showLoading();
        const response = await fetch('customer_api.php?action=update', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showSuccess('Customer information updated successfully');
            currentCustomer = data.data;
            cancelEdit(); // Reset form to view mode
        } else {
            showError(data.error || 'Error updating customer');
        }
    } catch (error) {
        showError('Error updating customer');
        console.error(error);
    } finally {
        hideLoading();
    }
}

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

function getStatusClass(status) {
    const statusClasses = {
        'Pending': 'bg-warning',
        'Processing': 'bg-info',
        'Completed': 'bg-success',
        'Cancelled': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}

function getComplaintClass(status) {
    const complaintClasses = {
        'None': 'complaint-none',
        'Pending': 'complaint-pending',
        'Resolved': 'complaint-resolved',
        'Escalated': 'complaint-escalated'
    };
    return complaintClasses[status] || 'complaint-none';
}

function showLoading() {
    // Add your loading indicator logic here
    document.body.style.cursor = 'wait';
}

function hideLoading() {
    document.body.style.cursor = 'default';
}

function showError(message) {
    // Implement your error display logic
    alert(message); // Replace with better UI feedback
}

function showSuccess(message) {
    // Implement your success display logic
    alert(message); // Replace with better UI feedback
}
</script>
</body>
</html>