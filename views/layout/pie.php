</main>
<footer class="mt-auto py-3 gradient-footer text-white">
	<div class="container text-center small">
		&copy; <?php echo date('Y'); ?> Restanet · Gestión de Restaurante
	</div>
	<!-- views/layout/pie.php, añade contenedor para aviso -->
	<div id="timeoutWarning" class="toast align-items-center text-bg-warning border-0 position-fixed bottom-0 end-0 m-3" role="status" hidden>
  		<div class="d-flex">
    		<div class="toast-body">
      			Tu sesión caducará pronto por inactividad. Mueve el cursor o toca la pantalla para continuar.
    		</div>
    		<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
  		</div>
	</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo BASE_PATH; ?>js/app.js"></script>
</body>
</html>