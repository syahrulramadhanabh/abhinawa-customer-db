<div class="container-fluid">
    <div class="card">
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= html_escape($this->session->flashdata('error')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Dashboard</h5>
            <p class="mb-0">
                Hi, <strong><?= html_escape($this->session->userdata('username')); ?></strong>! Welcome to Abhinawa Customer Database System.
            </p>

            <div class="mt-4">
                <h6 class="fw-semibold">Customer and Supplier Overview</h6>
                <canvas id="customerChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Pass PHP data to JavaScript
const data = {
    labels: ['Total Customers', 'Total Suppliers', 'Active Customers', 'Suspend Customers', 'Nonaktif Customers'],
    datasets: [{
        label: 'Counts',
        data: [
            <?= json_encode($total_customers) ?>,
            <?= json_encode($total_suppliers) ?>,
            <?= json_encode($active_customers) ?>,
            <?= json_encode($suspend_customers) ?>,
            <?= json_encode($nonaktif_customers) ?>
        ],
        backgroundColor: [
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(54, 235, 162, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(255, 99, 132, 0.2)'
        ],
        borderColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(54, 235, 162, 1)',
            'rgba(255, 205, 86, 1)',
            'rgba(255, 99, 132, 1)'
        ],
        borderWidth: 1
    }]
};

const config = {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
};

const customerChart = new Chart(
    document.getElementById('customerChart'),
    config
);
</script>
