<!-- Footer Section with Update Version and Author Information -->
<footer class="footer bg-light text-center py-3 mt-5">
    <div class="container">
        <p class="mb-0">View Detail Update Version <a href="#" class="fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#updateModal">Here</a> | Author: <span class="text-muted">Syahrul Ramadhan</span></p>
    </div>
</footer>

<!-- Modal to show updates -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateModalLabel">Update History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Dynamically populate updates from database -->
        <?php if (!empty($updates)): ?>
          <div class="list-group">
            <?php foreach ($updates as $update): ?>
              <div class="list-group-item">
                <h5 class="mb-2 text-primary">Version <?= htmlspecialchars($update->version); ?></h5>
                <p class="mb-2"><?= nl2br(htmlspecialchars($update->changes)); ?></p>
                <small class="text-muted">Updated on: <?= htmlspecialchars($update->update_date); ?></small>
                
                <?php if (!empty($update->koreksi)): ?>
                  <hr>
                  <h6 class="text-secondary">Correction</h6>
                  <p class="mb-2"><?= nl2br(htmlspecialchars($update->koreksi)); ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="alert alert-info" role="alert">
            No updates available.
          </div>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Inisialisasi Modal & Toast (Bootstrap 5)
  const terminationModalEl = document.getElementById('terminationModal');
  const terminationModal   = new bootstrap.Modal(terminationModalEl);
  const toastEl            = document.getElementById('emailToast');
  const toast              = new bootstrap.Toast(toastEl);

  let currentCustomerId = null;

  // Buka modal saat tombol diklik
  document.querySelectorAll('.btn-notify-termination').forEach(btn => {
    btn.addEventListener('click', function(){
      currentCustomerId = this.dataset.id;
      const custName    = this.dataset.customer;
      document.getElementById('terminationModalLabel').innerText = 'Notify Termination: ' + custName;
      document.getElementById('terminationModalBody').innerText  =
        `Kirim email notifikasi terminasi untuk "${custName}"?`;
      terminationModal.show();
    });
  });

  // Konfirmasi di modal
  document.getElementById('confirmTerminateBtn').addEventListener('click', function(){
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengirim...`;

    fetch(`<?= base_url('customer/notify_termination/') ?>${currentCustomerId}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      terminationModal.hide();
      btn.disabled = false;
      btn.innerHTML = 'Kirim Notifikasi';

      // Toast sukses
      toastEl.classList.replace('bg-danger','bg-success');
      document.getElementById('emailToastBody').innerText = data.message;
      toast.show();
    })
    .catch(err => {
      console.error(err);
      terminationModal.hide();
      btn.disabled = false;
      btn.innerHTML = 'Kirim Notifikasi';

      // Toast error
      toastEl.classList.replace('bg-success','bg-danger');
      document.getElementById('emailToastBody').innerText = 'Error mengirim notifikasi';
      toast.show();
    });
  });
});
</script>

<script src="<?= base_url('assets/libs/jquery/dist/jquery.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/sidebarmenu.js'); ?>"></script>
<script src="<?= base_url('assets/js/app.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/simplebar/dist/simplebar.js'); ?>"></script>
</body>
</html>
