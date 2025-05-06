<!-- supplier_list.php -->
<div class="container-fluid">
    <h2>Supplier List</h2>
    <a href="<?= base_url('supplier/create'); ?>" class="btn btn-primary mb-3">Add New Supplier</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Kode Supplier</th>
                    <th>Nama Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?= $supplier->kdsupplier ?></td>
                        <td><?= $supplier->nama_supplier ?></td>
                        <td>
                            <a href="<?= site_url('supplier/details/' . $supplier->kdsupplier) ?>" class="btn btn-info btn-sm">View Details</a>
                            <a href="<?= site_url('supplier/edit/' . $supplier->kdsupplier) ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="<?= site_url('supplier/delete/' . $supplier->kdsupplier) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
