<footer class="site-footer" id="contact">
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-md-4">
        <h5 class="fw-bold mb-3">
          <span style="display:inline-flex;width:32px;height:32px;background:#FF6B00;border-radius:8px;align-items:center;justify-content:center;margin-right:6px;vertical-align:middle"><i class="bi bi-briefcase-fill text-white" style="font-size:0.9rem"></i></span>
          ProJobs
        </h5>
        <p class="text-white-50 small">Ethiopia's modern job marketplace. Find opportunities, hire talent, and build your career with top companies.</p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" class="fs-5" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
          <a href="#" class="fs-5" title="Twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="fs-5" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="fs-5" title="Telegram"><i class="bi bi-telegram"></i></a>
        </div>
      </div>
      <div class="col-6 col-md-2">
        <h6 class="mb-3">For Job Seekers</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="<?= base_url('jobs.php') ?>">Browse Jobs</a></li>
          <li class="mb-2"><a href="<?= base_url('auth/seeker-register.php') ?>">Create Account</a></li>
          <li class="mb-2"><a href="<?= base_url('seeker/dashboard.php') ?>">Dashboard</a></li>
          <li class="mb-2"><a href="<?= base_url('jobs.php?featured=1') ?>">Featured Jobs</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-2">
        <h6 class="mb-3">Company</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="<?= base_url('index.php') ?>">Home</a></li>
          <li class="mb-2"><a href="<?= base_url('index.php') ?>#categories">Categories</a></li>
          <li class="mb-2"><a href="<?= base_url('auth/login.php') ?>">Admin Login</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="mb-3">Stay Updated</h6>
        <p class="text-white-50 small">New jobs posted daily. Create a free account to apply and track applications.</p>
        <a href="<?= base_url('auth/seeker-register.php') ?>" class="btn btn-orange btn-sm">Get Started Free</a>
      </div>
    </div>
    <hr class="border-secondary my-4 opacity-25">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
      <p class="text-white-50 small mb-0">&copy; <?= date('Y') ?> ProJobs Portal. All rights reserved.</p>
      <div class="d-flex gap-3 small">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.job-card').forEach((card, i) => {
  card.style.opacity = '0';
  card.style.transform = 'translateY(18px)';
  card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
  setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, 50 * i);
});
</script>
</body>
</html>
