<?php load_header(); ?>

<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Ajouter un événement</h4>
                </div>

                <div class="card-body">

                    <form method="POST" action="../public/index.php?page=event_add_action" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">Image de couverture :</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Titre :</label>
                            <input type="text" name="titre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description :</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catégorie :</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de l'événement :</label>
                            <input type="date" name="date_event" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Heure :</label>
                            <input type="time" name="heure" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lieu :</label>
                            <input type="text" name="lieu" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Ajouter l'événement
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>