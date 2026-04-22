<?php
session_start();
require_once '../controllers/ObjectiveController.php';
require_once '../controllers/PlanningController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$objectiveController = new ObjectiveController();
$planningController = new PlanningController();
$objectives = $objectiveController->getAllForUser($_SESSION['user_id']);
$plans = $planningController->getAllForUser($_SESSION['user_id']);

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = htmlspecialchars($_GET['delete']);
    if ($objectiveController->delete($deleteId)) {
        $_SESSION['success_message'] = "Objectif supprimé avec succès!";
        header('Location: objectif_list.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de l'objectif!";
    }
}
?>
<?php include 'header.php'; ?>

	<!-- objective section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">
						<h3><span class="orange-text">Mes</span> Objectifs</h3>
						<p>Gérez vos objectifs nutritionnels et de remise en forme</p>
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
					<a href="objectif_create.php" class="boxed-btn"><i class="fas fa-plus"></i> Ajouter un objectif</a>
				</div>
			</div>

			<!-- Objectives Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead class="table-dark">
								<tr>
									<th>ID</th>
									<th>Type</th>
									<th>Valeur Cible</th>
									<th>Poids Initial</th>
									<th>Date Limite</th>
									<th>Statut</th>
									<th>Priorité</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($objectives)): ?>
									<tr>
										<td colspan="8" class="text-center"><em>Aucun objectif trouvé</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($objectives as $objectif): ?>
										<tr>
											<td><?php echo htmlspecialchars($objectif['id_objectif']); ?></td>
											<td><?php echo htmlspecialchars($objectif['type_objectif']); ?></td>
											<td><?php echo htmlspecialchars($objectif['valeur_cible']); ?></td>
											<td><?php echo htmlspecialchars($objectif['poids_initial'] ?? '-'); ?></td>
											<td><?php echo htmlspecialchars($objectif['date_limite'] ?? '-'); ?></td>
											<td>
												<span class="badge badge-<?php
													switch($objectif['statut']) {
														case 'en_attente': echo 'secondary'; break;
														case 'en_cours': echo 'primary'; break;
														case 'termine': echo 'success'; break;
														case 'annule': echo 'danger'; break;
														default: echo 'light';
													}
												?>">
													<?php echo htmlspecialchars($objectif['statut']); ?>
												</span>
											</td>
											<td>
												<span class="badge badge-<?php
													switch($objectif['niveau_priorite']) {
														case 'faible': echo 'info'; break;
														case 'moyen': echo 'warning'; break;
														case 'eleve': echo 'danger'; break;
														default: echo 'light';
													}
												?>">
													<?php echo htmlspecialchars($objectif['niveau_priorite']); ?>
												</span>
											</td>
											<td>
												<a href="objectif_edit.php?id=<?php echo htmlspecialchars($objectif['id_objectif']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modifier</a>
												<a href="#" class="btn btn-sm btn-danger delete-objective" data-id="<?php echo htmlspecialchars($objectif['id_objectif']); ?>"><i class="fas fa-trash"></i> Supprimer</a>
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

	<!-- Plans Section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">
						<h3><span class="orange-text">Mes</span> Plans Nutritionnels</h3>
						<p>Vos plans personnalisés créés par nos experts</p>
					</div>
				</div>
			</div>

			<!-- Plans Table -->
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead class="table-dark">
								<tr>
									<th>ID</th>
									<th>Titre</th>
									<th>Calories/Jour</th>
									<th>Protéines (g)</th>
									<th>Glucides (g)</th>
									<th>Lipides (g)</th>
									<th>Repas/Jour</th>
									<th>Sommeil (h)</th>
									<th>Entraînement (h)</th>
									<th>Date Début</th>
									<th>Date Fin</th>
									<th>Statut</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($plans)): ?>
									<tr>
										<td colspan="12" class="text-center"><em>Aucun plan assigné pour le moment</em></td>
									</tr>
								<?php else: ?>
									<?php foreach ($plans as $plan): ?>
										<tr>
											<td><?php echo htmlspecialchars($plan['id_planning']); ?></td>
											<td><?php echo htmlspecialchars($plan['titre'] ?? 'Sans titre'); ?></td>
											<td><?php echo htmlspecialchars($plan['calories_par_jour'] ?? '-'); ?> kcal</td>
											<td><?php echo htmlspecialchars($plan['objectif_proteines'] ?? '-'); ?>g</td>
											<td><?php echo htmlspecialchars($plan['objectif_glucides'] ?? '-'); ?>g</td>
											<td><?php echo htmlspecialchars($plan['objectif_lipides'] ?? '-'); ?>g</td>
											<td><?php echo htmlspecialchars($plan['nombre_repas_par_jour'] ?? '-'); ?></td>
											<td><?php echo htmlspecialchars($plan['heures_sommeil_par_jour'] ?? '-'); ?>h</td>
											<td><?php echo htmlspecialchars($plan['heures_entrainement_par_jour'] ?? '-'); ?>h</td>
											<td><?php echo htmlspecialchars($plan['date_debut'] ?? '-'); ?></td>
											<td><?php echo htmlspecialchars($plan['date_fin'] ?? '-'); ?></td>
											<td>
												<span class="badge badge-<?php
													switch($plan['statut']) {
														case 'actif': echo 'success'; break;
														case 'inactif': echo 'secondary'; break;
														case 'termine': echo 'info'; break;
														default: echo 'light';
													}
												?>">
													<?php echo htmlspecialchars($plan['statut'] ?? 'inconnu'); ?>
												</span>
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

	<!-- Delete Confirmation Modal -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Êtes-vous sûr de vouloir supprimer cet objectif ? Cette action est irréversible.
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
					<a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Delete confirmation
		document.querySelectorAll('.delete-objective').forEach(button => {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				const objectiveId = this.getAttribute('data-id');
				document.getElementById('confirmDelete').href = 'objectif_list.php?delete=' + objectiveId;
				$('#deleteModal').modal('show');
			});
		});
	</script>

<?php include 'footer.php'; ?>