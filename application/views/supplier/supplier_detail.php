<?php 
$role_id = $this->session->userdata('role_id'); // Get role_id from session
?>
<div class="container-fluid">
    <h2>Supplier Details</h2>
    <div class="card">
        <div class="card-header">
            <h5>Details for Supplier: <?= $supplier->nama_supplier ?></h5>
        </div>
        <?php if (in_array($role_id, [1, 2])): ?>
            <a href="<?= base_url('index.php/supplier/add_supplier_detail/'. $supplier->kdsupplier); ?>" class="btn btn-primary mb-3">Add New Detail Supplier</a>
        <?php endif; ?>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>CID Supplier</th>
                            <th>Start Service</th>
                            <th>End Service</th>
                            <th>Service Type</th>
                            <th>SDN</th>
                            <th>Contact</th>
                            <th>Topology Supplier</th>
                            <th>Eskalasi Matrix</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($supplier_details as $detail): ?>
                            <tr>
                                <td><?= $detail->cid_supplier ?></td>
                                <td><?= $detail->start_service ?></td>
                                <td><?= $detail->end_service ?></td>
                                <td><?= $detail->service_type_supplier ?></td>
                                <td>
                                    <?php if (!empty($detail->sdn)): ?>
                                        <!-- Button to trigger SDN modal -->
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalSDN<?= $detail->cid_supplier; ?>">View</button>
                                    <?php else: ?>
                                        No File
                                    <?php endif; ?>
                                </td>
                                <td><?= $detail->contact ?></td>
                                <td>
                                    <?php if (!empty($detail->topology_supplier)): ?>
                                        <!-- Button to trigger Topology Supplier modal -->
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalTopology<?= $detail->cid_supplier; ?>">View</button>
                                    <?php else: ?>
                                        No File
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($detail->eskalasi_matrix)): ?>
                                        <!-- Button to trigger Eskalasi Matrix modal -->
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalEskalasi<?= $detail->cid_supplier; ?>">View</button>
                                    <?php else: ?>
                                        No File
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Modal SDN -->
                            <!-- Modal SDN -->
<div class="modal fade" id="modalSDN<?= $detail->cid_supplier; ?>" tabindex="-1" aria-labelledby="modalSDNLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSDNLabel">SDN File: <?= $supplier->nama_supplier; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php if (!empty($detail->sdn)): ?>
                    <!-- Download Button -->
                    <a href="<?= base_url('uploads/' . $detail->sdn) ?>" class="btn btn-primary mb-3" download>Download SDN</a>
                    <!-- Iframe for SDN preview with zoom functionality -->
                    <iframe id="sdnFrame<?= $detail->cid_supplier; ?>" src="<?= base_url('uploads/' . $detail->sdn) ?>" width="100%" height="400px" style="transform: scale(1); transform-origin: center;"></iframe>
                <?php else: ?>
                    <p>No SDN file uploaded.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Topology Supplier -->
<div class="modal fade" id="modalTopology<?= $detail->cid_supplier; ?>" tabindex="-1" aria-labelledby="modalTopologyLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTopologyLabel">Topology Supplier File: <?= $supplier->nama_supplier; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php if (!empty($detail->topology_supplier)): ?>
                    <!-- Download Button -->
                    <a href="<?= base_url('uploads/' . $detail->topology_supplier) ?>" class="btn btn-primary mb-3" download>Download Supplier Topology</a>

                    <!-- Iframe for Topology Supplier preview with zoom functionality -->
                    <iframe id="topologyFrame<?= $detail->cid_supplier; ?>" src="<?= base_url('uploads/' . $detail->topology_supplier) ?>" width="100%" height="400px" style="transform: scale(1); transform-origin: center;"></iframe>
                <?php else: ?>
                    <p>No Topology Supplier file uploaded.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eskalasi Matrix -->
<div class="modal fade" id="modalEskalasi<?= $detail->cid_supplier; ?>" tabindex="-1" aria-labelledby="modalEskalasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEskalasiLabel">Escalation Matrix File: <?= $supplier->nama_supplier; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php if (!empty($detail->eskalasi_matrix)): ?>
                    <!-- Download Button -->
                    <a href="<?= base_url('uploads/' . $detail->eskalasi_matrix) ?>" class="btn btn-primary mb-3" download>Download Matrix Escalation</a>
                    <!-- Iframe for Eskalasi Matrix preview with zoom functionality -->
                    <iframe id="eskalasiFrame<?= $detail->cid_supplier; ?>" src="<?= base_url('uploads/' . $detail->eskalasi_matrix) ?>" width="100%" height="400px" style="transform: scale(1); transform-origin: center;"></iframe>
                <?php else: ?>
                    <p>No Eskalasi Matrix file uploaded.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>