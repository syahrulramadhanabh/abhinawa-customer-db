<div class="container">
  <h2>Add Correction for Version <?= $update->version; ?></h2>
  <form action="<?= base_url('update_history/save_correction'); ?>" method="post">
    <input type="hidden" name="id" value="<?= $update->id; ?>">
    <div class="mb-3">
      <label for="koreksi" class="form-label">Correction</label>
      <textarea class="form-control" name="koreksi" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save Correction</button>
  </form>
</div>
