</main>
<footer class="gradient-footer text-white py-4 mt-auto">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
				<div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
					<i class="fa-solid fa-utensils fs-4" style="color: #F4A900;"></i>
					<span class="fw-bold fs-5">Restanet</span>
				</div>
				<small class="text-white-50 d-block mt-1">Sistema de Gestión de Restaurante</small>
			</div>
			<div class="col-md-6 text-center text-md-end">
				<small class="text-white-50">
					&copy; <?php echo date('Y'); ?> Restanet · Todos los derechos reservados
				</small>
			</div>
		</div>
	</div>
	<!-- Toast de aviso de sesión -->
	<div id="timeoutWarning" class="toast align-items-center text-bg-warning border-0 position-fixed bottom-0 end-0 m-4" role="alert" aria-live="assertive" aria-atomic="true" hidden>
		<div class="d-flex">
			<div class="toast-body d-flex align-items-center gap-2">
				<i class="fa-solid fa-clock fa-lg"></i>
				<span>Tu sesión caducará pronto por inactividad. Mueve el cursor para continuar.</span>
			</div>
			<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
		</div>
	</div>
</footer>
<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.min.js"></script>
<!-- App JS -->
<script src="<?php echo BASE_PATH; ?>js/app.js"></script>
</body>
</html>