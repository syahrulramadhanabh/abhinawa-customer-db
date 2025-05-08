<div class="container-fluid mt-4">
    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <h2 class="mb-4 text-center">Customers in Group</h2>

    <?php if ($role_id == 1): ?>
        <div class="mb-3">
            <a href="<?= base_url('customer/add_customer/' . $group_id) ?>" class="btn btn-success">
                <i class="fa fa-plus"></i> Add New Customer
            </a>
            <a href="<?= base_url('customer/check_service_end_dates') ?>"
               class="btn btn-primary"
               onclick="return confirm('Send end-of-service notifications for all due customers?');">
               <i class="fa fa-envelope"></i> Send All End Notifications
            </a>
        </div>
    <?php endif; ?>

    <?php if (!empty($customers)): ?>
        <?php
        // Group by customer|supplier to avoid duplicate cid_supp
        $grouped = [];
        foreach ($customers as $c) {
            $key = $c->customer . '|' . $c->nama_supplier;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            if (!in_array($c->cid_supp, array_column($grouped[$key], 'cid_supp'))) {
                $grouped[$key][] = $c;
            }
        }
        ?>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Customer</th>
                            <th>Supplier</th>
                            <th>CID Supplier</th>
                            <th>CID Customer</th>
                            <th>Service Type</th>
                            <th>SO</th>
                            <th>SDN</th>
                            <th>Topology</th>
                            <th>Status</th>
                            <?php if ($role_id == 1 || $role_id == 2): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-light">
                    <?php foreach ($grouped as $key => $items):
                        list($custName, $suppName) = explode('|', $key);
                        $first = true;
                        $rowCount = count($items);
                    ?>
                        <?php foreach ($items as $cust):
                            // Determine status
                            $today = new DateTime();
                            $status_label = 'Unknown';
                            $status_class = 'btn-secondary';
                            switch ($cust->status) {
                                case 1:
                                    $status_label = 'Active';
                                    $status_class = 'btn-success';
                                    break;
                                case 2:
                                    $status_label = 'Suspend';
                                    $status_class = 'btn-warning';
                                    break;
                                case 3:
                                    $end = (new DateTime($cust->end_date))->modify('+1 day');
                                    if ($today > $end) {
                                        $status_label = 'Terminated';
                                        $status_class = 'btn-dark';
                                    } else {
                                        $status_label = 'Inactive';
                                        $status_class = 'btn-danger';
                                    }
                                    break;
                                case 4:
                                    $status_label = 'Terminated';
                                    $status_class = 'btn-dark';
                                    break;
                            }
                        ?>
                        <tr>
                            <?php if ($first): ?>
                                <td rowspan="<?= $rowCount ?>"><?= htmlspecialchars($custName) ?></td>
                                <td rowspan="<?= $rowCount ?>"><?= htmlspecialchars($suppName) ?></td>
                                <?php $first = false; ?>
                            <?php endif; ?>

                            <td><?= htmlspecialchars($cust->cid_supp) ?></td>
                            <td><?= htmlspecialchars($cust->cid_abh) ?></td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#fileModal"
                                        onclick="loadServiceTypeDescription('<?= $cust->service_type_id ?>', '<?= htmlspecialchars($cust->service_type_name) ?>')">
                                    <?= htmlspecialchars($cust->service_type_name) ?>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#fileModal"
                                        onclick="loadFile('<?= base_url('uploads/' . $cust->no_so) ?>', 'Sales Order (SO)')">
                                    View SO
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#fileModal"
                                        onclick="loadFile('<?= base_url('uploads/' . $cust->no_sdn) ?>', 'Service Delivery Note (SDN)')">
                                    View SDN
                                </button>
                            </td>
                            <td>
                                <!-- Topology column re-added -->
                                <button class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#fileModal"
                                        onclick="loadFile('<?= base_url('uploads/' . $cust->topology) ?>', 'Topology Document')">
                                    View Topology
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm <?= $status_class ?>">
                                    <?= $status_label ?>
                                </button>
                            </td>

                            <?php if ($role_id == 1 || $role_id == 2): ?>
                                <td>
                                    <a href="<?= base_url('customer/notify_termination/' . $cust->id) ?>"
                                       class="btn btn-warning btn-sm"
                                       onclick="return confirm('Notify termination for <?= htmlspecialchars($cust->customer) ?>?');">
                                        <i class="fa fa-envelope"></i>
                                    </a>
                                    <a href="<?= base_url('customer/edit_customer/' . $cust->id) ?>"
                                       class="btn btn-secondary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('customer/delete_customer/' . $cust->id) ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete <?= htmlspecialchars($cust->customer) ?>?');">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <p class="text-muted">No customers found in this group.</p>
    <?php endif; ?>
</div>

<!-- File View Modal -->
<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="fileModalLabel">Document Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="fileContent">
                <p class="text-muted">Loading content…</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadFile(url, title) {
    document.getElementById('fileModalLabel').innerText = title;
    document.getElementById('fileContent').innerHTML =
        `<iframe src="${url}" style="width:100%;height:500px;border:0;"></iframe>`;
}

function loadServiceTypeDescription(id, name) {
    document.getElementById('fileModalLabel').innerText = 'Service Type: ' + name;
    fetch(`<?= base_url('customer/get_service_type_description') ?>/${id}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('fileContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('fileContent').innerHTML = '<p class="text-danger">Failed to load description.</p>';
        });
}
</script>
