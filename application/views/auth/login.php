<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Abhinawa Customer Databases System</title>
  <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/images/logos/logo-square.jpg'); ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/styles.min.css'); ?>" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="<?= site_url(); ?>" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="<?= base_url('assets/images/logos/abh-red.png'); ?>" width="180" alt="">
                </a>
                <p class="text-center">Connect & Secure</p>

                <!-- Display alert if login error -->
                <?php if ($this->session->flashdata('login_error')): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('login_error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php endif; ?>

                <form action="<?= site_url('auth/login'); ?>" method="POST" onsubmit="return validateCaptcha()">
                  <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" required>
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                  </div>

                  <!-- CAPTCHA Section -->
                  <div class="mb-3">
                    <label for="captcha" class="form-label">CAPTCHA: <span id="captchaQuestion"></span></label>
                    <input type="text" id="captchaAnswer" class="form-control" placeholder="Enter the answer" required>
                  </div>

                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" value="" id="rememberMe" checked>
                      <label class="form-check-label text-dark" for="rememberMe">
                        Remember this Device
                      </label>
                    </div>
                    <a class="text-primary fw-bold" href="<?= site_url('auth/forgot_password'); ?>">Forgot Password?</a>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-2 fs-4 mb-4 rounded-2">Sign In</button>
                  <div class="d-flex align-items-center justify-content-center">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CAPTCHA Error Modal -->
  <div class="modal fade" id="captchaErrorModal" tabindex="-1" aria-labelledby="captchaErrorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="captchaErrorLabel">CAPTCHA Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Incorrect CAPTCHA answer. Please try again.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/libs/jquery/dist/jquery.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js'); ?>"></script>

  <!-- CAPTCHA JavaScript -->
  <script>
    let captchaResult;

    function generateCaptcha() {
      const num1 = Math.floor(Math.random() * 10) + 1;
      const num2 = Math.floor(Math.random() * 10) + 1;
      captchaResult = num1 + num2;
      document.getElementById('captchaQuestion').innerText = `${num1} + ${num2} = ?`;
    }

    function validateCaptcha() {
      const userAnswer = document.getElementById('captchaAnswer').value;
      if (parseInt(userAnswer) !== captchaResult) {
        // Show the CAPTCHA error modal if the answer is incorrect
        new bootstrap.Modal(document.getElementById('captchaErrorModal')).show();
        generateCaptcha(); // Reset the CAPTCHA
        return false; // Prevent form submission
      }
      return true; // Allow form submission
    }

    // Initialize CAPTCHA on page load
    window.onload = generateCaptcha;
  </script>
</body>
</html>
