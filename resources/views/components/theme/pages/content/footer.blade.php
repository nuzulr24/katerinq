    </div>
        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="result"></div>
            </div>
        </div>
    </div>

    <script>
        var base, BASE_URL = "{{ url('/') }}";
    </script>
    <!-- page js -->
    <script src="<?= asset('admin_assets/libs/jquery/dist/jquery.min.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/libs/simplebar/dist/simplebar.min.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/js/app.min.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/js/app.horizontal.init.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/js/app-style-switcher.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/js/sidebarmenu.js') ?>?v=<?= time() ?>"></script>
    
    <script src="<?= asset('admin_assets/js/custom.js?') ?>v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/libs/owl.carousel/dist/owl.carousel.min.js') ?>?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/js/dashboard.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/js/app.min.js') ?>?v=<?= time() ?>"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/libs/select2/dist/js/select2.full.min.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= asset('admin_assets/libs/select2/dist/js/select2.min.js') ?>?v=<?= time() ?>"></script>

    @stack('scripts')
    <!--custom javascript-->
    {{ swal_response() }}
</body>
</html>