<div class="container-fluid">
    <h2>Add Supplier Detail for <?= $supplier->nama_supplier ?></h2>
    <?= validation_errors(); ?>
    <?= form_open_multipart('index.php/supplier/add_supplier_detail/' . $supplier->kdsupplier); ?>

    <div class="mb-3">
        <label for="cid_supplier" class="form-label">CID Supplier</label>
        <input type="text" class="form-control" id="cid_supplier" name="cid_supplier" required>
    </div>

    <div class="mb-3">
        <label for="start_service" class="form-label">Start Service</label>
        <input type="date" class="form-control" id="start_service" name="start_service" required>
    </div>

    <div class="mb-3">
        <label for="end_service" class="form-label">End Service</label>
        <input type="date" class="form-control" id="end_service" name="end_service" required>
    </div>

    <div class="mb-3">
        <label for="service_type_supplier" class="form-label">Service Type</label>
        <input type="text" class="form-control" id="service_type_supplier" name="service_type_supplier" required>
    </div>

    <div class="mb-3">
        <label for="sdn" class="form-label">SDN File</label>
        <input type="file" class="form-control" id="sdn" name="sdn">
    </div>

    <div class="mb-3">
        <label for="topology" class="form-label">Topology</label>
        <input type="file" class="form-control" id="topology" name="topology">
    </div>

    <div class="mb-3">
        <label for="eskalasi_matrix" class="form-label">Matrix Escalation</label>
        <input type="file" class="form-control" id="eskalasi_matrix" name="eskalasi_matrix">
    </div>

    <div class="mb-3">
        <label for="contact" class="form-label">Contact</label>
        <input type="text" class="form-control" id="contact" name="contact">
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
    <?= form_close(); ?>
</div>
