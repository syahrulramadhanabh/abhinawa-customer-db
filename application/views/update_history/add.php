<div class="container-fluid">
  <h2>Add Update History</h2>
  
  <form action="<?= base_url('update_history/save_update'); ?>" method="post">
    <div class="mb-3">
      <label for="version" class="form-label">Version</label>
      <input type="text" class="form-control" name="version" id="version" required>
    </div>

    <div class="mb-3">
      <label for="changes" class="form-label">Changes</label>
      <textarea class="form-control" name="changes" id="changes" rows="4" required></textarea>
    </div>

    <div class="mb-3">
      <label for="author" class="form-label">Author</label>
      <input type="text" class="form-control" name="author" id="author" required>
    </div>

    <button type="submit" class="btn btn-primary">Save Update History</button>
    <a href="<?= base_url('update_history'); ?>" class="btn btn-secondary">Back to Update History</a>
  </form>
</div>
