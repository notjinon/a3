// charts.js

// 0) Default horizon = 6 months
let currentHorizon = '6M';

document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM loaded, setting up horizon picker and fetching charts');

  // A) Wire up the time-horizon picker buttons
  document.querySelectorAll('#time-horizon-picker button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelector('#time-horizon-picker .active')?.classList.remove('active');
      btn.classList.add('active');
      currentHorizon = btn.dataset.horizon;
      console.log('Horizon →', currentHorizon);

      // Re-fetch all charts & tables
      [
        ['all-employees',       renderTable],
        ['revenue',             renderTable],
        ['inventory',           renderTable],
        ['top_products',        renderTopProductsChart],
        ['bottom_products',     renderBottomProductsChart],
        ['order_status',        renderOrderStatusChart],
        ['customer_types',      renderCustomerTypesChart],
        ['payment_status',      renderPaymentStatusChart],
        ['salesperson_summary', renderSalespersonSummary],
        ['sales_by_category',   renderTable],
        ['canceled_revenue',    renderCanceledRevenue],
        ['avg_days_to_pickup',  renderAvgDaysToPickup],
        ['current_orders',      renderTable] // ⬅ Add this line
      ].forEach(([type, fn]) => fetchData(type, fn));
      
    });
  });

  // B) Initial fetch (6M default)
  fetchData('all-employees',      renderTable);
  fetchData('revenue',            renderTable);
  fetchData('inventory',          renderTable);
  fetchData('top_products',       renderTopProductsChart);
  fetchData('bottom_products',    renderBottomProductsChart);
  fetchData('order_status',       renderOrderStatusChart);
  fetchData('customer_types',     renderCustomerTypesChart);
  fetchData('payment_status',     renderPaymentStatusChart);
  fetchData('salesperson_summary',renderSalespersonSummary);
  fetchData('sales_by_category', renderTable);   
  fetchData('canceled_revenue', renderCanceledRevenue);
  fetchData('avg_days_to_pickup', renderAvgDaysToPickup);
  fetchData('current_orders',     renderTable); // Fetch current orders data

});

// Generic data fetcher
function fetchData(type, callback) {
  const el = document.getElementById(type);
  if (!el) return;

  const url = `../includes/data.php?type=${type}`
            + `&horizon=${currentHorizon}`
            + `&_=${Date.now()}`;
  console.log('FETCH', url);

  fetch(url)
    .then(r => r.ok
      ? r.json()
      : r.json().then(e => { throw new Error(e.error||`HTTP ${r.status}`); })
    )
    .then(data => callback(type, data))
    .catch(err => {
      el.innerHTML = `<div class="error">Error: ${err.message}</div>`;
      console.error(type, err);
    });
}

// Utility to handle high-DPR screens
function fixCanvasResolution(canvas) {
  const dpr = window.devicePixelRatio||1, r = canvas.getBoundingClientRect();
  canvas.width  = r.width  * dpr;
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

// Render tables that automatically adjust to the data
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
      if (/price|revenue|amount/i.test(col))       return `<td>$${(+val).toFixed(2)}</td>`;
      if (/date/i.test(col))                       return `<td>${val?new Date(val).toLocaleDateString():''}</td>`;
      return `<td>${val}</td>`;
    }).join('') + '</tr>';
  });
  c.innerHTML = html + '</tbody></table>';
}

/* ---------------- CHART RENDERS ---------------- */

// Top Products: bar + line
function renderTopProductsChart(_, data) {
  if (!data?.length) return;
  const canvas = document.getElementById('topProductsChart');
  fixCanvasResolution(canvas);
  Chart.getChart(canvas)?.destroy();  // destroy previous
  const ctx = canvas.getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(d => d.product_name),
      datasets: [
        { label: 'Units Sold',  data: data.map(d => d.total_sold), yAxisID: 'y'   },
        { label: 'Revenue ($)', data: data.map(d => +d.total_revenue), type: 'line', borderColor: '#2196f3', yAxisID: 'y1' }
      ]
    },
    options: {
      responsive: true,
      plugins: { title: { display: true, text: 'Top 10 Products by Sales Volume' } },
      scales: {
        y:  { beginAtZero: true, title: { display: true, text: 'Units Sold' } },
        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue ($)' } }
      }
    }
  });
}

function renderBottomProductsChart(_, data) {
  if (!data?.length) return;
  const canvas = document.getElementById('bottomProductsChart');
  fixCanvasResolution(canvas);
  Chart.getChart(canvas)?.destroy();  // destroy previous
  const ctx = canvas.getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(d => d.product_name),
      datasets: [
        { label: 'Units Sold',  data: data.map(d => d.total_sold), yAxisID: 'y' },
        { label: 'Revenue ($)', data: data.map(d => +d.total_revenue), type: 'line', borderColor: '#f44336', yAxisID: 'y1' }
      ]
    },
    options: {
      responsive: true,
      plugins: { title: { display: true, text: 'Bottom 10 Products by Sales Volume' } },
      scales: {
        y:  { beginAtZero: true, title: { display: true, text: 'Units Sold' } },
        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue ($)' } }
      }
    }
  });
}


// Order Status: pie chart
function renderOrderStatusChart(_, data) {
  if (!data?.length) return;
  const canvas = document.getElementById('orderStatusChart');
  fixCanvasResolution(canvas);
  Chart.getChart(canvas)?.destroy();
  const ctx = canvas.getContext('2d');

  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: data.map(d => d.status),
      datasets: [{ data: data.map(d => d.count), backgroundColor: ['#2196f3','#f44336','#ffc107','#8bc34a','#9c27b0','#ff9800'] }]
    },
    options: { plugins: { title: { display: true, text: 'Order Status Distribution' } } }
  });
}

// Customer Types: doughnut
function renderCustomerTypesChart(_, data) {
  if (!data?.length) return;
  const canvas = document.getElementById('customerTypesChart');
  fixCanvasResolution(canvas);
  Chart.getChart(canvas)?.destroy();
  const ctx = canvas.getContext('2d');

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: data.map(d => d.type),
      datasets: [{ data: data.map(d => d.count), backgroundColor: ['#ff9800','#2196f3'] }]
    },
    options: { plugins: { title: { display: true, text: 'Customer Types Distribution' } } }
  });
}

// Payment Status: bar
function renderPaymentStatusChart(_, data) {
  if (!data?.length) return;
  const canvas = document.getElementById('paymentStatusChart');
  fixCanvasResolution(canvas);
  Chart.getChart(canvas)?.destroy();
  const ctx = canvas.getContext('2d');

  const colors = { 'Paid':'#4caf50','Pending':'#ffc107','Overdue':'#f44336' };
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(d => d.status),
      datasets: [{ data: data.map(d => d.count), backgroundColor: data.map(d => colors[d.status]||'#999') }]
    },
    options: {
      responsive: true,
      plugins: { title: { display: true, text: 'Payment Status Distribution' } },
      scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Orders' } } }
    }
  });
}

// Salesperson Summary: bar + table
function renderSalespersonSummary(_, data) {
  const container = document.getElementById('salesperson_summary');
  if (!container) {
    console.error('Container element not found');
    return;
  }
  
  if (!data?.length) {
    return void (container.innerHTML = '<div class="no-data">No data available</div>');
  }

  // Check if canvas exists, if not create it
  let canvas = document.getElementById('salespersonSummaryChart');
  if (!canvas) {
    canvas = document.createElement('canvas');
    canvas.id = 'salespersonSummaryChart';
    container.appendChild(canvas);
  }
  
  // Now continue with the rest of your function
  fixCanvasResolution(canvas);
  Chart.getChart(canvas)?.destroy();
  const ctx = canvas.getContext('2d');

  const labels = data.map(d => d.salesperson_name);
  const revenues = data.map(d => +d.total_revenue);

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(d => d.salesperson_name),
      datasets: [{
        label: 'Revenue ($)',
        data: data.map(d => +d.total_revenue),
        backgroundColor: '#4caf50'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: true,
          text: 'Salesperson Performance Summary'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Revenue ($)'
          }
        }
      }
    }
  });

  // append summary table
  let html = '<h3>Salesperson Revenue Details</h3><table><thead><tr>'
           + '<th>Salesperson</th><th>Orders</th><th>Total Revenue</th></tr></thead><tbody>';
  let totRev = 0, totOrd = 0;
  data.forEach(r => {
    html += `<tr><td>${r.salesperson_name}</td><td>${r.total_orders}</td>`
         + `<td>$${(+r.total_revenue).toFixed(2)}</td></tr>`;
    totOrd += +r.total_orders; totRev += +r.total_revenue;
  });
  html += `<tr class="total-row"><td><strong>Total</strong></td>`
        + `<td><strong>${totOrd}</strong></td><td><strong>$${totRev.toFixed(2)}</strong></td></tr>`
        + '</tbody></table>';
  container.insertAdjacentHTML('beforeend', html);

}
  
  // Render total revenue lost from canceled orders
  function renderCanceledRevenue(type, data) {
    const el = document.getElementById(type);
    if (!el) return;
    
    const amt = data?.[0]?.lost_revenue ?? null;
    if (amt === null) {
      el.innerHTML = '<div class="no-data">No canceled revenue.</div>';
      return;
    }
  
    const value = (+amt).toFixed(2);
    const isZero = (value == 0 || value === "0.00");
    const color = isZero ? '#4caf50' : '#f44336';
    
    // Always slap a "-" manually if it's not zero
    const formattedValue = isZero ? `$${value}` : `-$${value}`;
  
    el.innerHTML = `<h2 style="color:${color}; text-align:center;">${formattedValue}</h2>`;
  }
  
  
  function renderAvgDaysToPickup(type, data) {
    const el = document.getElementById(type);
    if (!el) return;
  
    if (!Array.isArray(data) || !data.length) {
      el.innerHTML = '<div class="no-data">No pickup data available.</div>';
      return;
    }
  
    const avg = Number(data[0]?.avg_days);
    if (isNaN(avg)) {
      el.innerHTML = '<div class="no-data">Invalid pickup data.</div>';
      return;
    }
    
    el.innerHTML = `<h2 style="text-align:center;">${avg.toFixed(2)} days</h2>`;
   
    
    
  }  