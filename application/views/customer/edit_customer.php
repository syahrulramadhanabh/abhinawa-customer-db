<div class="container-fluid">
    <h2>Edit Customer</h2>
    <form action="<?= base_url('customer/update_customer/' . $customer->id); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="group_id" value="<?= $customer->customer_group_id; ?>">

        <div class="mb-3">
            <label for="customer" class="form-label">Customer Name</label>
            <input type="text" class="form-control" name="customer" value="<?= $customer->customer; ?>" required>
        </div>

        <div class="mb-3">
            <label for="kdsupplier" class="form-label">Supplier</label>
            <select class="form-control" name="kdsupplier" required>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier->kdsupplier; ?>" <?= ($supplier->kdsupplier == $customer->kdsupplier) ? 'selected' : ''; ?>>
                        <?= $supplier->nama_supplier; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="cid_supp" class="form-label">SID Supplier</label>
            <select class="form-control" name="cid_supp" required>
                <?php if (!empty($unused_cid_suppliers)): ?>
                    <?php foreach ($unused_cid_suppliers as $cid_supplier): ?>
                        <option value="<?= $cid_supplier->cid_supplier; ?>"><?= $cid_supplier->cid_supplier; ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No available SID suppliers</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="service_type_id" class="form-label">Service Type</label>
            <select class="form-control" name="service_type_id" required>
                <?php foreach ($service_types as $service_type): ?>
                    <option value="<?= $service_type->id; ?>" <?= ($service_type->id == $customer->service_type_id) ? 'selected' : ''; ?>>
                        <?= $service_type->service_name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="sla" class="form-label">SLA Customer</label>
            <input type="text" class="form-control" name="customer" value="<?= $customer->sla; ?>" required>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" name="start_date" value="<?= $customer->start_date; ?>" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" name="end_date" value="<?= $customer->end_date; ?>" required>
        </div>

        <!-- New fields for file uploads -->
        <div class="mb-3">
            <label for="no_so" class="form-label">Sales Order (SO)</label>
            <input type="file" class="form-control" name="no_so">
            <?php if (!empty($customer->no_so)): ?>
                <small>Current file: <?= $customer->no_so; ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="no_sdn" class="form-label">Service Delivery Note (SDN)</label>
            <input type="file" class="form-control" name="no_sdn">
            <?php if (!empty($customer->no_sdn)): ?>
                <small>Current file: <?= $customer->no_sdn; ?></small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="topology" class="form-label">Topology Document</label>
            <input type="file" class="form-control" name="topology">
            <?php if (!empty($customer->topology)): ?>
                <small>Current file: <?= $customer->topology; ?></small>
            <?php endif; ?>
        </div>

        <!-- Status Field -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" name="status" required>
                <option value="1" <?= ($customer->status == 1) ? 'selected' : ''; ?>>Active</option>
                <option value="2" <?= ($customer->status == 2) ? 'selected' : ''; ?>>Suspend</option>
                <option value="3" <?= ($customer->status == 3) ? 'selected' : ''; ?>>Inactive</option>
                <option value="4" <?= ($customer->status == 4) ? 'selected' : ''; ?>>Terminate</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Customer</button>
        <a href="<?= base_url('customer/group_details/' . $customer->customer_group_id); ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
