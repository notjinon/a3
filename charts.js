document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM loaded, trying to fetch test.php');
  
  // Test with a simple PHP file first
  fetch('../includes/test.php')
    .then(res => {
      console.log('Got response from test.php:', res);
      return res.json();
    })
    .then(data => {
      console.log('Data from test.php:', data);
      
      // Now try the actual data.php
      return fetch('../includes/data.php?type=test-connection');
    })
    .then(res => {
      console.log('Got response from data.php:', res);
      return res.json();
    })
    .then(data => {
      console.log('Data from data.php:', data);
      
      // If we get here, we can load the rest
      fetchData('all-employees', renderTable);
      fetchData('revenue', renderTable);
      fetchData('top_products', renderTopProductsChart);
      fetchData('order_status', renderOrderStatusChart);
      fetchData('customer_types', renderCustomerTypesChart);
      fetchData('payment_status', renderPaymentStatusChart);
    })
    .catch(err => {
      console.error('Error in test sequence:', err);
    });
});

function fetchData(type, callback) {
  const element = document.getElementById(type);
  if (!element) return;
  
  element.innerHTML = '<div class="loading">Loading data...</div>';
  
  // Add a timestamp to prevent caching
  const timestamp = new Date().getTime();
  console.log(`Fetching from: ../includes/data.php?type=${type}&_=${timestamp}`);
  
  fetch(`../includes/data.php?type=${type}&_=${timestamp}`)
    .then(res => {
      if (!res.ok) {
        return res.json().then(err => {
          throw new Error(err.error || `HTTP error! Status: ${res.status}`);
        }).catch(e => {
          throw new Error(`HTTP error! Status: ${res.status}`);
        });
      }
      return res.json();
    })
    .then(data => {
      console.log(`Data received for ${type}:`, data);
      callback(type, data);
    })
    .catch(err => {
      console.error(`Error fetching ${type}:`, err);
      const element = document.getElementById(type);
      if (element) {
        element.innerHTML = `<div class="error">Error loading data: ${err.message}</div>`;
      }
    });
}

function renderTable(type, data) {
  const container = document.getElementById(type);
  if (!container) return;
  
  if (!data || !data.length) {
    container.innerHTML = '<div class="no-data">No data available</div>';
    return;
  }

  const headers = Object.keys(data[0]);
  let html = `<table><thead><tr>${headers.map(h => `<th>${formatHeader(h)}</th>`).join('')}</tr></thead><tbody>`;
  
  data.forEach(row => {
    html += '<tr>';
    headers.forEach(h => {
      // Format currency values
      if (h.includes('price') || h.includes('revenue') || h.includes('amount')) {
        html += `<td>$${parseFloat(row[h]).toFixed(2)}</td>`;
      } 
      // Format dates
      else if (h.includes('date') || h.includes('Date')) {
        const date = row[h] ? new Date(row[h]) : null;
        html += `<td>${date ? date.toLocaleDateString() : ''}</td>`;
      }
      // Regular values
      else {
        html += `<td>${row[h]}</td>`;
      }
    });
    html += '</tr>';
  });
  
  html += '</tbody></table>';
  container.innerHTML = html;
}

function formatHeader(header) {
  // Convert snake_case or camelCase to Title Case with spaces
  return header
    .replace(/_/g, ' ')
    .replace(/([A-Z])/g, ' $1')
    .replace(/^./, str => str.toUpperCase())
    .trim();
}

function renderTopProductsChart(type, data) {
  const container = document.getElementById(type);
  if (!container) return;
  
  if (!data || !data.length) {
    container.innerHTML = '<div class="no-data">No data available</div>';
    return;
  }
  
  const ctx = document.getElementById('topProductsChart').getContext('2d');
  
  // Clear any existing chart
  if (window.topProductsChart) {
    window.topProductsChart.destroy();
  }
  
  // Prepare data
  const productNames = data.map(d => d.product_name);
  const totalSold = data.map(d => d.total_sold);
  const totalRevenue = data.map(d => parseFloat(d.total_revenue));
  
  window.topProductsChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: productNames,
      datasets: [
        {
          label: 'Units Sold',
          data: totalSold,
          backgroundColor: '#4caf50',
          order: 2,
          yAxisID: 'y'
        },
        {
          label: 'Revenue ($)',
          data: totalRevenue,
          backgroundColor: '#2196f3',
          type: 'line',
          order: 1,
          borderColor: '#2196f3',
          yAxisID: 'y1'
        }
      ]
    },
    options: { 
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          position: 'left',
          title: {
            display: true,
            text: 'Units Sold'
          }
        },
        y1: {
          beginAtZero: true,
          position: 'right',
          grid: {
            drawOnChartArea: false
          },
          title: {
            display: true,
            text: 'Revenue ($)'
          }
        }
      },
      plugins: {
        title: {
          display: true,
          text: 'Top 10 Products by Sales Volume'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.dataset.label || '';
              const value = context.raw;
              if (label === 'Revenue ($)') {
                return `${label}: $${value.toFixed(2)}`;
              }
              return `${label}: ${value}`;
            }
          }
        }
      }
    }
  });
}

function renderOrderStatusChart(type, data) {
  const container = document.getElementById(type);
  if (!container) return;
  
  if (!data || !data.length) {
    container.innerHTML = '<div class="no-data">No data available</div>';
    return;
  }
  
  const ctx = document.getElementById('orderStatusChart').getContext('2d');
  
  // Clear any existing chart
  if (window.orderStatusChart) {
    window.orderStatusChart.destroy();
  }
  
  const colors = ['#2196f3', '#f44336', '#ffc107', '#8bc34a', '#9c27b0', '#ff9800'];
  
  window.orderStatusChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: data.map(d => d.status),
      datasets: [{
        label: 'Order Status',
        data: data.map(d => d.count),
        backgroundColor: colors.slice(0, data.length)
      }]
    },
    options: { 
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Order Status Distribution'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw;
              const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}

function renderCustomerTypesChart(type, data) {
  const container = document.getElementById(type);
  if (!container) return;
  
  if (!data || !data.length) {
    container.innerHTML = '<div class="no-data">No data available</div>';
    return;
  }
  
  const ctx = document.getElementById('customerTypesChart').getContext('2d');
  
  // Clear any existing chart
  if (window.customerTypesChart) {
    window.customerTypesChart.destroy();
  }
  
  window.customerTypesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: data.map(d => d.type),
      datasets: [{
        label: 'Customer Types',
        data: data.map(d => d.count),
        backgroundColor: ['#ff9800', '#2196f3']
      }]
    },
    options: { 
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Customer Types Distribution'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw;
              const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}

function renderPaymentStatusChart(type, data) {
  const container = document.getElementById(type);
  if (!container) return;
  
  if (!data || !data.length) {
    container.innerHTML = '<div class="no-data">No data available</div>';
    return;
  }
  
  const ctx = document.getElementById('paymentStatusChart').getContext('2d');
  
  // Clear any existing chart
  if (window.paymentStatusChart) {
    window.paymentStatusChart.destroy();
  }
  
  const colors = {
    'Paid': '#4caf50',
    'Pending': '#ffc107',
    'Overdue': '#f44336'
  };
  
  window.paymentStatusChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(d => d.status),
      datasets: [{
        data: data.map(d => d.count),
        backgroundColor: data.map(d => colors[d.status] || '#9e9e9e')
      }]
    },
    options: { 
      responsive: true,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Payment Status Distribution'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Orders'
          }
        }
      }
    }
  });
}