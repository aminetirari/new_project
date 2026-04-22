<?php
session_start();
require_once '../controllers/MealController.php';
require_once '../models/Ingredient.php';

$mealController = new MealController();
$ingredientModel = new Ingredient();
$ingredients = $ingredientModel->getAll();
$errors = array();
$mealData = null;

// Get meal ID from URL
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "L'ID du repas n'a pas été fourni!";
    header('Location: meal_list.php');
    exit;
}

$mealId = htmlspecialchars($_GET['id']);
$mealData = $mealController->getById($mealId);

if (!$mealData) {
    $_SESSION['error_message'] = "Repas non trouvé!";
    header('Location: meal_list.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $mealController->update($mealId, $_POST);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "Repas mis à jour avec succès!";
        header('Location: meal_list.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'header.php'; ?>

	<!-- edit meal section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="section-title text-center mb-5">	
						<h3><span class="orange-text">Modifier</span> le Repas</h3>
						<p>Mettre à jour les informations du repas</p>
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
							<label for="name" class="form-label font-weight-bold">Nom du repas <span class="text-danger">*</span></label>
							<input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
								id="name" name="name" placeholder="Ex: Déjeuner du lundi" 
								value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($mealData['name']); ?>" required>
							<?php if (isset($errors['name'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['name']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="date" class="form-label font-weight-bold">Date du repas <span class="text-danger">*</span></label>
							<input type="date" class="form-control <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>" 
								id="date" name="date" 
								value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : htmlspecialchars($mealData['date']); ?>" required>
							<?php if (isset($errors['date'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['date']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label for="notes" class="form-label font-weight-bold">Notes (optionnel)</label>
							<textarea class="form-control <?php echo isset($errors['notes']) ? 'is-invalid' : ''; ?>" 
								id="notes" name="notes" rows="4" placeholder="Ajouter des notes sur le repas..."
								maxlength="500"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : htmlspecialchars($mealData['notes']); ?></textarea>
							<small class="form-text text-muted">Maximum 500 caractères</small>
							<?php if (isset($errors['notes'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['notes']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-group mb-4">
							<label class="form-label font-weight-bold">Ingrédients <span class="text-danger">*</span></label>
							<p class="text-muted">Sélectionnez les ingrédients et spécifiez les quantités (en grammes)</p>
							<?php if (isset($errors['ingredients'])): ?>
								<div class="alert alert-danger">
									<?php echo $errors['ingredients']; ?>
								</div>
							<?php endif; ?>
							<?php
							// Prepare arrays for pre-filling
							$selectedIngredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : array_column($mealData['ingredients'], 'id');
							$quantities = isset($_POST['quantities']) ? $_POST['quantities'] : array_column($mealData['ingredients'], 'quantity', 'id');
							?>
							<div class="row">
								<?php foreach ($ingredients as $ingredient): ?>
									<div class="col-md-6 mb-3">
										<div class="card p-3">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" 
													id="ingredient_<?php echo $ingredient['id']; ?>" 
													name="ingredients[]" 
													value="<?php echo $ingredient['id']; ?>"
													<?php echo in_array($ingredient['id'], $selectedIngredients) ? 'checked' : ''; ?>>
												<label class="form-check-label font-weight-bold" for="ingredient_<?php echo $ingredient['id']; ?>">
													<?php echo htmlspecialchars($ingredient['name']); ?>
												</label>
											</div>
											<div class="mt-2">
												<label for="quantity_<?php echo $ingredient['id']; ?>" class="form-label small">Quantité (g)</label>
												<input type="number" class="form-control form-control-sm" 
													id="quantity_<?php echo $ingredient['id']; ?>" 
													name="quantities[<?php echo $ingredient['id']; ?>]" 
													min="1" step="0.1" placeholder="Ex: 100"
													value="<?php echo isset($quantities[$ingredient['id']]) ? htmlspecialchars($quantities[$ingredient['id']]) : ''; ?>">
											</div>
											<small class="text-muted">
												Cal: <?php echo $ingredient['calories']; ?>/100g, Prot: <?php echo $ingredient['proteins']; ?>g, Gluc: <?php echo $ingredient['glucides']; ?>g, Lip: <?php echo $ingredient['lipides']; ?>g
											</small>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>

						<div class="form-group mt-5">
							<button type="submit" class="boxed-btn btn-block"><i class="fas fa-save"></i> Mettre à jour le repas</button>
							<a href="meal_list.php" class="btn btn-secondary btn-block mt-2">Annuler</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- end edit meal section -->

<?php include 'footer.php'; ?>
