<!-- Page Footer -->
<footer class="footer footer-light d-print-none">
  <div class="container-xl">
    <div class="row text-center align-items-center flex-row-reverse">
      <div class="col-lg-auto ms-lg-auto">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item"><a href="./docs/" class="link-secondary">Documentation</a></li>
          <li class="list-inline-item"><a href="./license.html" class="link-secondary">License</a></li>
        </ul>
      </div>
      <div class="col-12 col-lg-auto mt-3 mt-lg-0">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item">
            Copyright &copy; 2023
            <a href="." class="link-secondary">BPR Bangunarta</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>

</div>
</div>

<!-- Libs JS -->
<script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js?1674944402') }}" defer></script>
<script src="{{ asset('tabler/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1674944402') }}" defer></script>
<script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world.js?1674944402') }}" defer></script>
<script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world-merc.js?1674944402') }}" defer></script>
<script src="{{ asset('tabler/dist/libs/tom-select/dist/js/tom-select.base.min.js?1674944402') }}" defer></script>

<!-- Tabler Core -->
<script src="{{ asset('tabler/dist/js/tabler.min.js?1674944402') }}" defer></script>
<script src="{{ asset('tabler/dist/js/demo.min.js?1674944402') }}" defer></script>


@stack('myscript')