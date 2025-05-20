<!-- supplier_list.php -->
<div class="container-fluid">
    <h2>Supplier List</h2>
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
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
