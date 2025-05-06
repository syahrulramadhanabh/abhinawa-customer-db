<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-warning text-white">
      <h5>Edit Service Type</h5>
    </div>
    <div class="card-body">
      <form action="<?= base_url('service_type/update/' . $service_type->id); ?>" method="post">
        <div class="mb-3">
          <label for="service_name" class="form-label">Service Name</label>
          <input type="text" class="form-control" name="service_name" value="<?= htmlspecialchars($service_type->service_name); ?>" required>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($service_type->description); ?></textarea>
        </div>
        <div class="d-flex justify-content-end">
          <a href="<?= base_url('service_type'); ?>" class="btn btn-secondary me-2">Cancel</a>
          <button type="submit" class="btn btn-primary">Update Service Type</button>
        </div>
      </form>
    </div>
  </div>
</div>
