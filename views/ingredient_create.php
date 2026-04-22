<?php
session_start();
require_once '../controllers/IngredientController.php';

$ingredientController = new IngredientController();
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $ingredientController->create($_POST);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "Ingrédient créé avec succès!";
        header('Location: ingredient_list.php');
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<?php include 'header.php'; ?>

	<!-- create ingredient section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="section-title text-center mb-5">	
						<h3><span class="orange-text">Créer</span> un Ingrédient</h3>
						<p>Ajouter un nouvel ingrédient à votre inventaire</p>
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

					<!-- Create Form -->
					<form method="POST" class="form">
						<div class="form-group mb-4">
						<label for="name" class="form-label font-weight-bold">Nom de l'ingrédient <span class="text-danger">*</span></label>
						<input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
							id="name" name="name" placeholder="Ex: Poitrine de poulet" 
								value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
							<?php if (isset($errors['name'])): ?>
								<div class="invalid-feedback d-block">
									<?php echo $errors['name']; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6 mb-4">
								<label for="calories" class="form-label font-weight-bold">Calories (kcal) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['calories']) ? 'is-invalid' : ''; ?>" 
									id="calories" name="calories" placeholder="0.00"
									value="<?php echo isset($_POST['calories']) ? htmlspecialchars($_POST['calories']) : ''; ?>" required>
								<?php if (isset($errors['calories'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['calories']; ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="form-group col-md-6 mb-4">
							<label for="proteins" class="form-label font-weight-bold">Protéines (g) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['proteins']) ? 'is-invalid' : ''; ?>" 
									id="proteins" name="proteins" placeholder="0.00"
									value="<?php echo isset($_POST['proteins']) ? htmlspecialchars($_POST['proteins']) : ''; ?>" required>
								<?php if (isset($errors['proteins'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['proteins']; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6 mb-4">
							<label for="glucides" class="form-label font-weight-bold">Glucides (g) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['glucides']) ? 'is-invalid' : ''; ?>" 
									id="glucides" name="glucides" placeholder="0.00"
									value="<?php echo isset($_POST['glucides']) ? htmlspecialchars($_POST['glucides']) : ''; ?>" required>
								<?php if (isset($errors['glucides'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['glucides']; ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="form-group col-md-6 mb-4">
							<label for="lipides" class="form-label font-weight-bold">Lipides (g) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0" class="form-control <?php echo isset($errors['lipides']) ? 'is-invalid' : ''; ?>" 
									id="lipides" name="lipides" placeholder="0.00"
									value="<?php echo isset($_POST['lipides']) ? htmlspecialchars($_POST['lipides']) : ''; ?>" required>
								<?php if (isset($errors['lipides'])): ?>
									<div class="invalid-feedback d-block">
										<?php echo $errors['lipides']; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="form-group mt-5">
						<button type="submit" class="boxed-btn btn-block"><i class="fas fa-save"></i> Créer l'ingrédient</button>
						<a href="ingredient_list.php" class="btn btn-secondary btn-block mt-2">Annuler</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- end create ingredient section -->

<?php include 'footer.php'; ?>
