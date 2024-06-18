      <script src="{{ frontend('plugins/global/plugins.bundle.js') }}"></script>
      <script src="{{ frontend('js/scripts.bundle.js') }}"></script>
      <?= swal_response() ?>
      @stack('scripts')
    </body>
</html>