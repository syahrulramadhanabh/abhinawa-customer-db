<div class="container-fluid">
  <h2>Edit Supplier</h2>

  <form action="<?= base_url('supplier/update/' . $supplier->kdsupplier); ?>" method="post" enctype="multipart/form-data">
    <!-- Supplier Code -->
    <div class="mb-3">
      <label for="kdsupplier" class="form-label">Supplier Code</label>
      <input type="text" class="form-control" name="kdsupplier" id="kdsupplier" value="<?= $supplier->kdsupplier; ?>" required>
    </div>

    <!-- Supplier Name -->
    <div class="mb-3">
      <label for="nama_supplier" class="form-label">Supplier Name</label>
      <input type="text" class="form-control" name="nama_supplier" id="nama_supplier" value="<?= $supplier->nama_supplier; ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Supplier</button>
    <a href="<?= base_url('supplier'); ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
