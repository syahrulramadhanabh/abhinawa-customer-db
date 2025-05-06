<div class="container-fluid">
    <h2>Create New Supplier</h2>
    <form action="<?= site_url('supplier/store') ?>" method="post">
        <div class="form-group">
            <label for="kdsupplier">Kode Supplier</label>
            <input type="text" name="kdsupplier" id="kdsupplier" class="form-control" required>
            <?= form_error('kdsupplier') ?>
        </div>
        <div class="form-group">
            <label for="nama_supplier">Nama Supplier</label>
            <input type="text" name="nama_supplier" id="nama_supplier" class="form-control" required>
            <?= form_error('nama_supplier') ?>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Save Supplier</button>
    </form>
</div>
