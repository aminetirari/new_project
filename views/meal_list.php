<?php
session_start();
require_once '../controllers/MealController.php';

$mealController = new MealController();
$meals = $mealController->getAll();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($mealController->delete($deleteId)) {
        $_SESSION['success_message'] = "Repas supprimé avec succès!";
        header('Location: meal_list.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du repas!";
    }
}
?>
<?php include 'header.php'; ?>

	<!-- meal section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">	
						<h3><span class="orange-text">Nos</span> Repas</h3>
						<p>Gérez votre liste de repas avec ingrédients</p>
					</div>
				</div>
			</div>

			<!-- Success/Error Messages -->
			<?php if (isset($_SESSION['success_message'])): ?>
				<div class="row mb-4">
					<div class="col-lg-12">
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (isset($_SESSION['error_message'])): ?>
				<div class="row mb-4">
					<div class="col-lg-12">
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- Add New Button -->
			<div class="row mb-4">
				<div class="col-lg-12 text-center">
					<a href="meal_create.php" class="boxed-btn"><i class="fas fa-plus"></i> Ajouter un repas</a>
				</div>
			</div>

			<!-- Meals Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead class="table-dark">
								<tr>
									<th>ID</th>
									<th>Nom</th>
									<th>Date</th>
									<th>Notes</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($meals)): ?>
									<tr>
										<td colspan="5" class="text-center"><em>Aucun repas trouvé</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($meals as $meal): ?>
										<tr>
											<td><?php echo htmlspecialchars($meal['id']); ?></td>
											<td><?php echo htmlspecialchars($meal['name']); ?></td>
											<td><?php echo htmlspecialchars($meal['date']); ?></td>
											<td><?php echo htmlspecialchars(substr($meal['notes'], 0, 50)) . (strlen($meal['notes']) > 50 ? '...' : ''); ?></td>
											<td>
												<a href="meal_edit.php?id=<?php echo htmlspecialchars($meal['id']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modifier</a>
											<a href="#" class="btn btn-sm btn-danger delete-meal" data-id="<?php echo htmlspecialchars($meal['id']); ?>"><i class="fas fa-trash"></i> Supprimer</a>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end meal section -->

<?php include 'footer.php'; ?>

<script>
	// Delete confirmation modal
	function showDeleteModal(id) {
		const modal = document.createElement('div');
		modal.className = 'delete-modal-overlay';
		modal.innerHTML = `
			<div class="delete-modal">
				<div class="delete-modal-content">
					<h3>Confirmer la Suppression</h3>
					<p>Êtes-vous sûr de vouloir supprimer ce repas? Cette action est irréversible.</p>
					<div class="delete-modal-buttons">
						<button class="delete-btn-cancel">Annuler</button>
						<button class="delete-btn-confirm">Supprimer</button>
					</div>
				</div>
			</div>
		`;
		
		document.body.appendChild(modal);
		
		const cancelBtn = modal.querySelector('.delete-btn-cancel');
		const confirmBtn = modal.querySelector('.delete-btn-confirm');
		
		const closeModal = () => modal.remove();
		
		cancelBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', (e) => {
			if (e.target === modal) closeModal();
		});
		
		confirmBtn.addEventListener('click', () => {
			window.location.href = 'meal_list.php?delete=' + id;
		});
	}

	// Attach click handlers to delete buttons
	document.querySelectorAll('.delete-meal').forEach(btn => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			const id = btn.getAttribute('data-id');
			showDeleteModal(id);
		});
	});
</script>

<style>
	.delete-modal-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.5);
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 10000;
	}
	
	.delete-modal {
		background: white;
		border-radius: 8px;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
		animation: slideIn 0.3s ease-out;
	}
	
	.delete-modal-content {
		padding: 30px;
		min-width: 400px;
		text-align: center;
	}
	
	.delete-modal-content h3 {
		margin: 0 0 15px 0;
		color: #333;
		font-size: 22px;
	}
	
	.delete-modal-content p {
		margin: 0 0 30px 0;
		color: #666;
		font-size: 16px;
	}
	
	.delete-modal-buttons {
		display: flex;
		gap: 10px;
		justify-content: center;
	}
	
	.delete-btn-cancel, .delete-btn-confirm {
		padding: 12px 30px;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		font-weight: 600;
		font-size: 14px;
		transition: all 0.3s;
	}
	
	.delete-btn-cancel {
		background-color: #e0e0e0;
		color: #333;
	}
	
	.delete-btn-cancel:hover {
		background-color: #d0d0d0;
	}
	
	.delete-btn-confirm {
		background-color: #dc3545;
		color: white;
	}
	
	.delete-btn-confirm:hover {
		background-color: #c82333;
	}
	
	@keyframes slideIn {
		from {
			transform: translateY(-50px);
			opacity: 0;
		}
		to {
			transform: translateY(0);
			opacity: 1;
		}
	}
	
	@media (max-width: 480px) {
		.delete-modal-content {
			min-width: 300px;
		}
		
		.delete-modal-buttons {
			flex-direction: column;
		}
	}
</style>
