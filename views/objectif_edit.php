<?php
session_start();
require_once '../controllers/ObjectiveController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$objectiveController = new ObjectiveController();
$errors = array();

// Get objective ID from URL
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$id) {
    header('Location: objectif_list.php');
    exit;
}

// Get objective data
$objectif = $objectiveController->getById($id);
if (!$objectif || $objectif['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error_message'] = "Objectif non trouvé ou accès non autorisé.";
    header('Location: objectif_list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $objectiveController->update($id, $_POST);

    if ($result['success']) {
        $_SESSION['success_message'] = "Objectif mis à jour avec succès!";
        header('Location: objectif_list.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'header.php'; ?>

	<!-- edit objective section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="section-title text-center mb-5">
						<h3><span class="orange-text">Modifier</span> l'Objectif</h3>
						<p>Modifiez les détails de votre objectif</p>
					</div>

					<!-- Error Messages -->
					<?php if (isset($errors['general'])): ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<?php echo $errors['general']; ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php endif; ?>

					<!-- Edit Form -->
					<form method="POST" class="form">
						<div class="form-group mb-4">
							<label for="type_objectif" class="form-label font-weight-bold">Type d'objectif <span class="text-danger">*</span></label>
							<select class="form-control <?php echo isset($errors['type_objectif']) ? 'is-invalid' : ''; ?>"
								id="type_objectif" name="type_objectif" required>
								<option value="">Sélectionnez un type</option>
								<option value="perte_poids" <?php echo ($objectif['type_objectif'] == 'perte_poids' || (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'perte_poids')) ? 'selected' : ''; ?>>Perte de poids</option>
								<option value="prise_poids" <?php echo ($objectif['type_objectif'] == 'prise_poids' || (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'prise_poids')) ? 'selected' : ''; ?>>Prise de poids</option>
								<option value="maintien_poids" <?php echo ($objectif['type_objectif'] == 'maintien_poids' || (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'maintien_poids')) ? 'selected' : ''; ?>>Maintien du poids</option>
								<option value="augmentation_muscle" <?php echo ($objectif['type_objectif'] == 'augmentation_muscle' || (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'augmentation_muscle')) ? 'selected' : ''; ?>>Augmentation musculaire</option>
								<option value="amelioration_endurance" <?php echo ($objectif['type_objectif'] == 'amelioration_endurance' || (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'amelioration_endurance')) ? 'selected' : ''; ?>>Amélioration de l'endurance</option>
								<option value="reduction_gras" <?php echo ($objectif['type_objectif'] == 'reduction_gras' || (isset($_POST['type_objectif']) && $_POST['type_objectif'] == 'reduction_gras')) ? 'selected' : ''; ?>>Réduction du taux de graisse</option>
							</select>
							<?php if (isset($errors['type_objectif'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['type_objectif']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="valeur_cible" class="form-label font-weight-bold">Valeur cible <span class="text-danger">*</span></label>
							<input type="number" step="0.01" min="0.01" max="999.99" class="form-control <?php echo isset($errors['valeur_cible']) ? 'is-invalid' : ''; ?>"
								id="valeur_cible" name="valeur_cible" placeholder="Ex: 70.5 (kg)"
								value="<?php echo isset($_POST['valeur_cible']) ? htmlspecialchars($_POST['valeur_cible']) : htmlspecialchars($objectif['valeur_cible']); ?>" required>
							<small class="form-text text-muted">Entrez la valeur cible (poids en kg, etc.) - Entre 0.01 et 999.99</small>
							<?php if (isset($errors['valeur_cible'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['valeur_cible']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="poids_initial" class="form-label font-weight-bold">Poids initial (optionnel)</label>
							<input type="number" step="0.01" min="0.01" max="500" class="form-control"
								id="poids_initial" name="poids_initial" placeholder="Ex: 75.0"
								value="<?php echo isset($_POST['poids_initial']) ? htmlspecialchars($_POST['poids_initial']) : htmlspecialchars($objectif['poids_initial'] ?? ''); ?>">
							<small class="form-text text-muted">Votre poids actuel en kg (entre 0.01 et 500)</small>
						</div>

						<div class="form-group mb-4">
							<label for="date_limite" class="form-label font-weight-bold">Date limite (optionnel)</label>
							<input type="date" class="form-control"
								id="date_limite" name="date_limite" min="<?php echo date('Y-m-d'); ?>"
								value="<?php echo isset($_POST['date_limite']) ? htmlspecialchars($_POST['date_limite']) : htmlspecialchars($objectif['date_limite'] ?? ''); ?>">
							<small class="form-text text-muted">Date à laquelle vous souhaitez atteindre l'objectif (doit être dans le futur)</small>
						</div>

						<div class="form-group mb-4">
							<label for="description" class="form-label font-weight-bold">Description (optionnel)</label>
							<textarea class="form-control" id="description" name="description" rows="4"
								placeholder="Décrivez votre objectif en détail..." maxlength="1000"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($objectif['description'] ?? ''); ?></textarea>
							<small class="form-text text-muted">
								<span id="charCount">0</span>/1000 caractères
							</small>
						</div>

						<div class="form-group mb-4">
							<label for="statut" class="form-label font-weight-bold">Statut</label>
							<select class="form-control" id="statut" name="statut">
								<option value="en_attente" <?php echo ($objectif['statut'] == 'en_attente' || (!isset($_POST['statut']) && $objectif['statut'] == 'en_attente')) ? 'selected' : ''; ?>>En attente</option>
								<option value="en_cours" <?php echo ($objectif['statut'] == 'en_cours' || (isset($_POST['statut']) && $_POST['statut'] == 'en_cours')) ? 'selected' : ''; ?>>En cours</option>
								<option value="termine" <?php echo ($objectif['statut'] == 'termine' || (isset($_POST['statut']) && $_POST['statut'] == 'termine')) ? 'selected' : ''; ?>>Terminé</option>
								<option value="annule" <?php echo ($objectif['statut'] == 'annule' || (isset($_POST['statut']) && $_POST['statut'] == 'annule')) ? 'selected' : ''; ?>>Annulé</option>
							</select>
						</div>

						<div class="form-group mb-4">
							<label for="niveau_priorite" class="form-label font-weight-bold">Niveau de priorité</label>
							<select class="form-control" id="niveau_priorite" name="niveau_priorite">
								<option value="faible" <?php echo ($objectif['niveau_priorite'] == 'faible' || (isset($_POST['niveau_priorite']) && $_POST['niveau_priorite'] == 'faible')) ? 'selected' : ''; ?>>Faible</option>
								<option value="moyen" <?php echo ($objectif['niveau_priorite'] == 'moyen' || (!isset($_POST['niveau_priorite']) && $objectif['niveau_priorite'] == 'moyen')) ? 'selected' : ''; ?>>Moyen</option>
								<option value="eleve" <?php echo ($objectif['niveau_priorite'] == 'eleve' || (isset($_POST['niveau_priorite']) && $_POST['niveau_priorite'] == 'eleve')) ? 'selected' : ''; ?>>Élevé</option>
							</select>
						</div>

						<div class="form-group text-center">
							<button type="submit" class="boxed-btn">Mettre à jour l'objectif</button>
							<a href="objectif_list.php" class="boxed-btn ml-3" style="background-color: #6c757d;">Annuler</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.getElementById('valeur_cible').addEventListener('input', function() {
			const value = parseFloat(this.value);
			if (value <= 0 || value > 999.99) {
				this.setCustomValidity('La valeur doit être entre 0.01 et 999.99');
			} else {
				this.setCustomValidity('');
			}
		});

		document.getElementById('poids_initial').addEventListener('input', function() {
			const value = parseFloat(this.value);
			if (this.value && (value <= 0 || value > 500)) {
				this.setCustomValidity('Le poids doit être entre 0.01 et 500 kg');
			} else {
				this.setCustomValidity('');
			}
		});

		document.getElementById('date_limite').addEventListener('input', function() {
			const selectedDate = new Date(this.value);
			const today = new Date();
			today.setHours(0, 0, 0, 0);

			if (this.value && selectedDate < today) {
				this.setCustomValidity('La date doit être dans le futur');
			} else {
				this.setCustomValidity('');
			}
		});

		document.getElementById('description').addEventListener('input', function() {
			const length = this.value.length;
			document.getElementById('charCount').textContent = length;

			if (length > 1000) {
				this.setCustomValidity('La description ne peut pas dépasser 1000 caractères');
			} else {
				this.setCustomValidity('');
			}
		});

		// Initialize character count on page load
		document.addEventListener('DOMContentLoaded', function() {
			const description = document.getElementById('description');
			document.getElementById('charCount').textContent = description.value.length;
		});
	</script>

<?php include 'footer.php'; ?>