<div class="container-fluid">
    <h2 class="mb-4 text-center">Customers in Group</h2>
    <?php if ($role_id == 1): ?>
        <a href="<?= base_url('customer/add_customer/' . $group_id); ?>" class="btn btn-success mb-3">Add New Customer</a>
    <?php endif; ?>
    <?php if (!empty($customers)): ?>
        <?php 
                                    $groupedData = [];
                                    foreach ($customers as $customer) {
                                        $groupKey = $customer->group_name . '|' . $customer->nama_supplier; // Updated grouping key to prevent duplicates
                                        if (!isset($groupedData[$groupKey])) {
                                            $groupedData[$groupKey] = [];
                                        }
                                        // Avoid duplicate CID Supplier within each group
                                        if (!in_array($customer->cid_supp, array_column($groupedData[$groupKey], 'cid_supp'))) {
                                            $groupedData[$groupKey][] = $customer;
                                        }
                                    }
                                    ?>
                                    
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
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
    <?php foreach ($groupedData as $groupKey => $customerGroup): 
        list($customer_name, $supplier_name) = explode('|', $groupKey);
        $firstRow = true;  // Reset for each new group
    ?>
        <?php foreach ($customerGroup as $customer): 
            $today = new DateTime();
            $status = "Unknown"; 
            $status_class = "btn-secondary"; 

            // Determine the status and class based on customer status
            switch ($customer->status) {
                case 1:
                    $status = "Active";
                    $status_class = "btn-success";
                    break;
                case 2:
                    $status = "Suspend";
                    $status_class = "btn-warning";
                    break;
                case 3:
                    $status = "Inactive";
                    $status_class = "btn-danger";
                    $end_date = new DateTime($customer->end_date);
                    if ($today > $end_date->modify('+1 day')) {
                        $status = "Terminated";
                        $status_class = "btn-dark";
                    }
                    break;
                case 4:
                    $status = "Terminated";
                    $status_class = "btn-dark";
                    break;
            }
        ?>
        <tr>
            <?php if ($firstRow): ?>
                <td rowspan="<?= count($customerGroup); ?>"><?= $customer_name; ?></td>
                <td rowspan="<?= count($customerGroup); ?>"><?= $supplier_name; ?></td>
                <?php $firstRow = false; // Only display once per group ?>
            <?php endif; ?>

            <td><?= $customer->cid_supp; ?></td>
            <td><?= $customer->cid_abh; ?></td>
            <td>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fileModal" onclick="loadServiceTypeDescription('<?= $customer->service_type_id; ?>', '<?= $customer->service_type_name; ?>')">
                    <?= $customer->service_type_name; ?>
                </button>
            </td>
            <td>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fileModal" onclick="loadFile('<?= base_url('uploads/' . $customer->no_so); ?>', 'Sales Order (SO)')">
                    View SO
                </button>
            </td>
            <td>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fileModal" onclick="loadFile('<?= base_url('uploads/' . $customer->no_sdn); ?>', 'Service Delivery Note (SDN)')">
                    View SDN
                </button>
            </td>
            <td>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fileModal" onclick="loadFile('<?= base_url('uploads/' . $customer->topology); ?>', 'Topology Document')">
                    View Topology
                </button>
            </td>
            <td>
                <button type="button" class="btn btn-sm <?= $status_class; ?>">
                    <?= $status; ?>
                </button>
            </td> 
            <?php if ($role_id == 1 || $role_id == 2): ?>
                <td>
                    <a href="<?= base_url('customer/edit_customer/' . $customer->id); ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="<?= base_url('customer/delete_customer/' . $customer->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="fileModalLabel">Document Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="fileContent" class="text-center">
                    <!-- File content will be loaded here -->
                    <p class="text-muted">Loading content, please wait...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadFile(fileUrl, title) {
    // Set the modal title
    document.getElementById('fileModalLabel').innerText = title;
    
    // Set the file content dynamically
    const fileContent = document.getElementById("fileContent");
    fileContent.innerHTML = `<iframe src="${fileUrl}" style="width:100%; height:500px;" frameborder="0"></iframe>`;
}

function loadServiceTypeDescription(serviceTypeId, serviceTypeName) {
    document.getElementById('fileModalLabel').innerText = 'Service Type: ' + serviceTypeName;
    fetch("<?= base_url('customer/get_service_type_description'); ?>/" + serviceTypeId)
        .then(response => response.text())
        .then(data => {
            document.getElementById("fileContent").innerHTML = data;
        })
        .catch(error => {
            document.getElementById("fileContent").innerHTML = "Failed to load description.";
        });
}
</script>
