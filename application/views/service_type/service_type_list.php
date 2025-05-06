<div class="container-fluid">
  <h2>Service Types</h2>

  <!-- Success Alert -->
  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> <?= $this->session->flashdata('success'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  <!-- Button to Open Add Service Type Modal -->
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceTypeModal">
    Add New Service Type
  </button>
  <br>
    <table class="table table-hover align-middle text-center">
        <thead class="bg-primary text-white">
      <tr>
        <th>ID</th>
        <th>Service Name</th>
        <th>Description</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($service_types as $service_type): ?>
        <tr>
          <td><?= $service_type->id; ?></td>
          <td><?= $service_type->service_name; ?></td>
          <td><?= $service_type->description; ?></td>
          <td>
            <a href="<?= base_url('service_type/edit/'.$service_type->id); ?>" class="btn btn-warning btn-sm">Edit</a>
            <button onclick="confirmDelete(<?= $service_type->id; ?>)" class="btn btn-danger btn-sm">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Add Service Type Modal -->
  <div class="modal fade" id="addServiceTypeModal" tabindex="-1" aria-labelledby="addServiceTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addServiceTypeModalLabel">Add New Service Type</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= base_url('service_type/add'); ?>" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label for="service_name" class="form-label">Service Name</label>
              <input type="text" class="form-control" name="service_name" placeholder="Enter service name" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" name="description" rows="3" placeholder="Enter description"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Service Type</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this service type?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  let deleteId = null;

  function confirmDelete(id) {
    deleteId = id;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    deleteModal.show();
  }

  // Handle the actual deletion
  document.getElementById('confirmDeleteButton').addEventListener('click', function() {
    if (deleteId) {
      // Redirect to delete action or make an AJAX call here
      window.location.href = '<?= base_url('service_type/delete/'); ?>' + deleteId;
    }
  });
</script>
