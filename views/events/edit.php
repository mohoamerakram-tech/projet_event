<?php load_header(); ?>

<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white text-center"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h4 class="mb-0">Modifier l'événement</h4>
                </div>

                <div class="card-body">

                    <form method="POST" action="?page=event_edit_action&id=<?= $evenement['id'] ?>"
                        enctype="multipart/form-data">

                        <!-- Image Upload Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase mb-2">Event Cover</label>
                            <div class="upload-zone p-4 border-2 border-dashed rounded-4 text-center position-relative"
                                style="border-color: #e5e7eb; background: #f9fafb; transition: all 0.3s ease;">
                                <input type="file" name="image"
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                                    accept="image/*" onchange="previewImage(this, 'preview-edit-page')"
                                    style="z-index: 10;">

                                <div id="preview-edit-page-container"
                                    class="<?= !empty($evenement['image']) ? '' : 'd-none' ?>">
                                    <img id="preview-edit-page"
                                        src="<?= !empty($evenement['image']) ? 'uploads/' . htmlspecialchars($evenement['image']) : '' ?>"
                                        alt="Preview" class="rounded-3 shadow-sm" style="max-height: 200px; object-fit: cover;">
                                    <div class="mt-2 text-primary small fw-semibold">Click to change image</div>
                                </div>

                                <div id="upload-placeholder-edit-page"
                                    class="<?= !empty($evenement['image']) ? 'd-none' : '' ?>">
                                    <div class="mb-3">
                                        <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 3rem; opacity: 0.5;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Drop your image here, or browse</h6>
                                    <p class="text-muted small mb-0">Supports: JPG, PNG, WEBP</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Titre :</label>
                            <input type="text" name="titre" class="form-control"
                                value="<?= htmlspecialchars($evenement['titre']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description :</label>
                            <textarea name="description" class="form-control" rows="4"
                                required><?= htmlspecialchars($evenement['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catégorie :</label>
                            <input type="hidden" name="category_id" id="selectedCategoryInputEditPage" value="<?= $evenement['category_id'] ?? '' ?>" required>

                            <!-- Search Filter & Quick Add -->
                            <div class="mb-3">
                                <div class="d-flex gap-2">
                                    <div class="input-group input-group-sm flex-grow-1">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                        <input type="text" id="categorySearchInputEditPage" class="form-control bg-light border-start-0"
                                            placeholder="Filter categories..."
                                            onkeyup="filterCategoriesEditPage()">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleAddCategoryEditPage()">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>

                                <!-- Quick Add Form -->
                                <div id="addCategoryFormEditPage" class="mt-2 d-none">
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="newCategoryNameEditPage" class="form-control" placeholder="New category name...">
                                        <button type="button" class="btn btn-success" onclick="createNewCategoryEditPage()">Save</button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2" id="categoryTagsContainerEditPage" style="max-height: 150px; overflow-y: auto;">
                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                    $isSelected = (isset($evenement['category_id']) && $evenement['category_id'] == $cat['id']);
                                    $btnClass = $isSelected ? 'btn-primary text-white' : 'btn-outline-primary';
                                    $activeClass = $isSelected ? 'active' : '';
                                    ?>
                                    <button type="button" class="btn <?= $btnClass ?> <?= $activeClass ?> rounded-pill btn-sm px-3 category-tag-edit-page"
                                        data-id="<?= $cat['id'] ?>"
                                        onclick="selectCategoryEditPage(this, <?= $cat['id'] ?>)">
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <script>
                                function filterCategoriesEditPage() {
                                    let input = document.getElementById('categorySearchInputEditPage');
                                    let filter = input.value.toLowerCase();
                                    let tags = document.querySelectorAll('.category-tag-edit-page');

                                    tags.forEach(tag => {
                                        let text = tag.textContent || tag.innerText;
                                        if (text.toLowerCase().indexOf(filter) > -1) {
                                            tag.style.display = "";
                                        } else {
                                            tag.style.display = "none";
                                        }
                                    });
                                }

                                function selectCategoryEditPage(btn, id) {
                                    document.getElementById('selectedCategoryInputEditPage').value = id;
                                    document.querySelectorAll('.category-tag-edit-page').forEach(b => {
                                        b.classList.remove('active', 'btn-primary', 'text-white');
                                        b.classList.add('btn-outline-primary');
                                    });
                                    btn.classList.remove('btn-outline-primary');
                                    btn.classList.add('active', 'btn-primary', 'text-white');
                                }

                                function toggleAddCategoryEditPage() {
                                    let form = document.getElementById('addCategoryFormEditPage');
                                    form.classList.toggle('d-none');
                                    if (!form.classList.contains('d-none')) {
                                        document.getElementById('newCategoryNameEditPage').focus();
                                    }
                                }

                                function createNewCategoryEditPage() {
                                    let nameInput = document.getElementById('newCategoryNameEditPage');
                                    let name = nameInput.value.trim();
                                    if (!name) return;

                                    fetch('?page=category_ajax_create', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                nom: name
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                let container = document.getElementById('categoryTagsContainerEditPage');
                                                let btn = document.createElement('button');
                                                btn.type = 'button';
                                                btn.className = 'btn btn-outline-primary rounded-pill btn-sm px-3 category-tag-edit-page';
                                                btn.setAttribute('data-id', data.id);
                                                btn.textContent = data.nom;
                                                btn.onclick = function() {
                                                    selectCategoryEditPage(this, data.id);
                                                };

                                                container.appendChild(btn);
                                                selectCategoryEditPage(btn, data.id);

                                                nameInput.value = '';
                                                toggleAddCategoryEditPage();
                                                container.scrollTop = container.scrollHeight;
                                            } else {
                                                alert('Error: ' + data.message);
                                            }
                                        });
                                }
                            </script>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de l'événement :</label>
                                <input type="date" name="date_event" class="form-control"
                                    value="<?= htmlspecialchars($evenement['date_event']) ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Heure :</label>
                                <input type="time" name="heure" class="form-control"
                                    value="<?= !empty($evenement['heure']) ? date('H:i', strtotime($evenement['heure'])) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lieu :</label>
                            <input type="text" name="lieu" class="form-control"
                                value="<?= htmlspecialchars($evenement['lieu']) ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn text-white fw-bold py-2"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none;">
                                Enregistrer les modifications
                            </button>
                            <a href="?page=admin_dashboard" class="btn btn-light border">Annuler</a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<script>
    function previewImage(input, previewId) {
        const container = document.getElementById(previewId + '-container');
        const placeholder = document.getElementById('upload-placeholder-edit-page');
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>