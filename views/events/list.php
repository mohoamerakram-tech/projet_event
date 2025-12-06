<?php load_header(); ?>

<!-- Stats Cards -->
<div class="stats-grid">
  <div class="stat-card-admin">
    <div class="stat-card-header">
      <div class="stat-card-icon blue">
        <i class="bi bi-calendar-event"></i>
      </div>
    </div>
    <div class="stat-card-value"><?= count($evenements) ?></div>
    <div class="stat-card-label">Total Events</div>
    <div class="stat-card-trend up">
      <i class="bi bi-arrow-up"></i>
      <span>12% this month</span>
    </div>
  </div>

  <div class="stat-card-admin">
    <div class="stat-card-header">
      <div class="stat-card-icon green">
        <i class="bi bi-check-circle"></i>
      </div>
    </div>
    <div class="stat-card-value"><?= count(array_filter($evenements, fn($e) => strtotime($e['date_event']) > time())) ?>
    </div>
    <div class="stat-card-label">Upcoming Events</div>
    <div class="stat-card-trend up">
      <i class="bi bi-arrow-up"></i>
      <span>8% increase</span>
    </div>
  </div>

  <div class="stat-card-admin">
    <div class="stat-card-header">
      <div class="stat-card-icon purple">
        <i class="bi bi-people"></i>
      </div>
    </div>
    <div class="stat-card-value"><?= $totalParticipants ?></div>
    <div class="stat-card-label">Total Participants</div>
    <div class="stat-card-trend up">
      <i class="bi bi-arrow-up"></i>
      <span>15% increase</span>
    </div>
  </div>

  <div class="stat-card-admin">
    <div class="stat-card-header">
      <div class="stat-card-icon orange">
        <i class="bi bi-grid-3x3-gap"></i>
      </div>
    </div>
    <div class="stat-card-value">9</div>
    <div class="stat-card-label">Categories</div>
  </div>
</div>

<!-- Events Table Section -->
<div class="card border-0 shadow-sm" style="border-radius: 16px;">
  <div class="card-header bg-white border-0 p-4" style="border-radius: 16px 16px 0 0;">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1 fw-bold">Events List</h4>
        <p class="text-muted mb-0 small">Manage all your events</p>
      </div>
      <button class="btn btn-primary shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#addEventModal"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 12px; padding: 10px 20px; font-weight: 600;">
        <i class="bi bi-plus-circle me-2"></i> Add Event
      </button>
    </div>
  </div>
  <div class="card-body p-0">
    <?php if (empty($evenements)): ?>
      <p class="text-center text-muted p-5">No events registered yet.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead style="background: #f8fafc;">
            <tr>
              <th class="px-4 py-3" style="font-weight: 600; color: #6b7280; font-size: 0.875rem;">Title</th>
              <th class="px-4 py-3" style="font-weight: 600; color: #6b7280; font-size: 0.875rem;">Date</th>
              <th class="px-4 py-3" style="font-weight: 600; color: #6b7280; font-size: 0.875rem;">Location</th>
              <th class="px-4 py-3 text-end" style="font-weight: 600; color: #6b7280; font-size: 0.875rem;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($evenements as $e): ?>
              <tr style="border-bottom: 1px solid #f3f4f6;">
                <td class="px-4 py-3">
                  <div class="d-flex align-items-center gap-3">
                    <?php if (!empty($e['image'])): ?>
                      <img src="uploads/<?= htmlspecialchars($e['image']) ?>" alt="<?= htmlspecialchars($e['titre']) ?>"
                        style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                    <?php else: ?>
                      <div
                        style="width: 48px; height: 48px; border-radius: 8px; background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-calendar-event" style="color: #8b5cf6; font-size: 1.25rem;"></i>
                      </div>
                    <?php endif; ?>
                    <div>
                      <div style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($e["titre"]) ?></div>
                      <div style="font-size: 0.875rem; color: #6b7280;">
                        <?= htmlspecialchars(substr($e["description"], 0, 50)) ?>...</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div
                    style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #eff6ff; border-radius: 8px; font-size: 0.875rem; font-weight: 600; color: #1e40af;">
                    <i class="bi bi-calendar3"></i>
                    <?= date('d M Y', strtotime($e["date_event"])) ?>
                    <?php if (!empty($e["heure"])): ?>
                      <span style="margin-left: 4px; color: #6b7280;"><?= date('H:i', strtotime($e["heure"])) ?></span>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div style="display: flex; align-items: center; gap: 6px; color: #6b7280; font-size: 0.875rem;">
                    <i class="bi bi-geo-alt-fill" style="color: #8b5cf6;"></i>
                    <?= htmlspecialchars($e["lieu"]) ?>
                  </div>
                </td>
                <td class="px-4 py-3 text-end">
                  <button class="btn btn-sm hover-lift" data-bs-toggle="modal" data-bs-target="#editModal<?= $e['id'] ?>"
                    style="background: #fef3c7; color: #92400e; border: none; border-radius: 8px; padding: 6px 12px; margin-right: 8px;">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm hover-lift" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $e['id'] ?>"
                    style="background: #fee2e2; color: #991b1b; border: none; border-radius: 8px; padding: 6px 12px;">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>

              <!-- MODAL MODIFIER (Premium) -->
              <div class="modal fade" id="editModal<?= $e['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="modal-header border-0 p-4"
                      style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                      <div class="d-flex align-items-center gap-3">
                        <div
                          class="rounded-circle bg-white bg-opacity-25 p-2 d-flex align-items-center justify-content-center"
                          style="width: 48px; height: 48px;">
                          <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                          <h5 class="modal-title fw-bold mb-0">Edit Event</h5>
                          <p class="mb-0 small opacity-75">Update event details</p>
                        </div>
                      </div>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="?page=event_edit_action&id=<?= $e['id'] ?>" enctype="multipart/form-data">
                      <div class="modal-body p-4 p-lg-5">
                        <div class="row g-4">
                          <!-- Image Upload Section -->
                          <div class="col-12">
                            <label class="form-label fw-bold text-secondary small text-uppercase mb-2">Event Cover</label>
                            <div class="upload-zone p-4 border-2 border-dashed rounded-4 text-center position-relative"
                              style="border-color: #e5e7eb; background: #f9fafb; transition: all 0.3s ease;">
                              <input type="file" name="image"
                                class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                                accept="image/*" onchange="previewImage(this, 'preview-edit-<?= $e['id'] ?>')"
                                style="z-index: 10;">

                              <div id="preview-edit-<?= $e['id'] ?>-container"
                                class="<?= !empty($e['image']) ? '' : 'd-none' ?>">
                                <img id="preview-edit-<?= $e['id'] ?>"
                                  src="<?= !empty($e['image']) ? 'uploads/' . htmlspecialchars($e['image']) : '' ?>"
                                  alt="Preview" class="rounded-3 shadow-sm" style="max-height: 200px; object-fit: cover;">
                                <div class="mt-2 text-primary small fw-semibold">Click to change image</div>
                              </div>

                              <div id="upload-placeholder-edit-<?= $e['id'] ?>"
                                class="<?= !empty($e['image']) ? 'd-none' : '' ?>">
                                <div class="mb-3">
                                  <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 3rem; opacity: 0.5;"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Drop your image here, or browse</h6>
                                <p class="text-muted small mb-0">Supports: JPG, PNG, WEBP</p>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Event Title</label>
                            <div class="input-group">
                              <span class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                  class="bi bi-type-h1"></i></span>
                              <input type="text" name="titre"
                                class="form-control bg-light border-start-0 rounded-end-3 py-3 fw-semibold"
                                value="<?= htmlspecialchars($e['titre']) ?>" required>
                            </div>
                          </div>

                          <div class="col-12">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Category</label>
                            <input type="hidden" name="category_id" id="selectedCategoryInputEdit<?= $e['id'] ?>" value="<?= $e['category_id'] ?? '' ?>" required>

                            <!-- Search Filter & Quick Add -->
                            <div class="mb-3">
                              <div class="d-flex gap-2">
                                <div class="input-group input-group-sm flex-grow-1">
                                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                  <input type="text" id="categorySearchInputEdit<?= $e['id'] ?>" class="form-control bg-light border-start-0"
                                    placeholder="Filter categories..."
                                    onkeyup="filterCategoriesEdit(<?= $e['id'] ?>)">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleAddCategoryEdit(<?= $e['id'] ?>)">
                                  <i class="bi bi-plus-lg"></i>
                                </button>
                              </div>

                              <!-- Quick Add Form -->
                              <div id="addCategoryFormEdit<?= $e['id'] ?>" class="mt-2 d-none">
                                <div class="input-group input-group-sm">
                                  <input type="text" id="newCategoryNameEdit<?= $e['id'] ?>" class="form-control" placeholder="New category name...">
                                  <button type="button" class="btn btn-success" onclick="createNewCategoryEdit(<?= $e['id'] ?>)">Save</button>
                                </div>
                              </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2" id="categoryTagsContainerEdit<?= $e['id'] ?>" style="max-height: 150px; overflow-y: auto;">
                              <?php
                              if (isset($categories)) {
                                foreach ($categories as $cat) {
                                  $isSelected = (isset($e['category_id']) && $e['category_id'] == $cat['id']);
                                  $btnClass = $isSelected ? 'btn-primary text-white' : 'btn-outline-primary';
                                  $activeClass = $isSelected ? 'active' : '';

                                  echo '<button type="button" class="btn ' . $btnClass . ' ' . $activeClass . ' rounded-pill btn-sm px-3 category-tag-edit-' . $e['id'] . '" 
                                          onclick="selectCategoryEdit(this, ' . $cat['id'] . ', ' . $e['id'] . ')">' .
                                    htmlspecialchars($cat['nom']) .
                                    '</button>';
                                }
                              }
                              ?>
                            </div>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Date</label>
                            <div class="input-group">
                              <span class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                  class="bi bi-calendar-event"></i></span>
                              <input type="date" name="date_event"
                                class="form-control bg-light border-start-0 rounded-end-3 py-3"
                                value="<?= htmlspecialchars($e['date_event']) ?>" required>
                            </div>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Time</label>
                            <div class="input-group">
                              <span class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                  class="bi bi-clock"></i></span>
                              <input type="time" name="heure"
                                class="form-control bg-light border-start-0 rounded-end-3 py-3"
                                value="<?= !empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : '' ?>">
                            </div>
                          </div>

                          <div class="col-12">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Description</label>
                            <div class="input-group">
                              <span class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                  class="bi bi-text-paragraph"></i></span>
                              <textarea name="description" class="form-control bg-light border-start-0 rounded-end-3 py-3"
                                rows="4" required><?= htmlspecialchars($e['description']) ?></textarea>
                            </div>
                          </div>

                          <div class="col-12">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Location</label>
                            <div class="input-group">
                              <span class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                  class="bi bi-geo-alt"></i></span>
                              <input type="text" name="lieu" class="form-control bg-light border-start-0 rounded-end-3 py-3"
                                value="<?= htmlspecialchars($e['lieu']) ?>" required>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer border-top-0 p-4 bg-light bg-opacity-50">
                        <button type="button" class="btn btn-light border-0 px-4 py-2 fw-semibold rounded-3"
                          data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn px-5 py-2 fw-bold text-white rounded-3 shadow-sm hover-lift"
                          style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none;">
                          <i class="bi bi-check-lg me-2"></i> Save Changes
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- MODAL SUPPRIMER (Premium) -->
              <div class="modal fade" id="deleteModal<?= $e['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="modal-header border-0 p-4"
                      style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white;">
                      <div class="d-flex align-items-center gap-3">
                        <div
                          class="rounded-circle bg-white bg-opacity-25 p-2 d-flex align-items-center justify-content-center"
                          style="width: 48px; height: 48px;">
                          <i class="bi bi-trash fs-4"></i>
                        </div>
                        <div>
                          <h5 class="modal-title fw-bold mb-0">Delete Event</h5>
                          <p class="mb-0 small opacity-75">This action cannot be undone</p>
                        </div>
                      </div>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                      <div class="mb-3">
                        <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem; opacity: 0.5;"></i>
                      </div>
                      <h5 class="fw-bold text-dark mb-2">Are you sure?</h5>
                      <p class="text-muted mb-0">You are about to delete the event:</p>
                      <div class="fw-bold text-dark mt-2 p-3 bg-light rounded-3 border border-light">
                        "<?= htmlspecialchars($e['titre']) ?>"
                      </div>
                    </div>
                    <div class="modal-footer border-top-0 p-4 bg-light bg-opacity-50 justify-content-center">
                      <button type="button" class="btn btn-light border-0 px-4 py-2 fw-semibold rounded-3"
                        data-bs-dismiss="modal">Cancel</button>
                      <a href="?page=event_delete&id=<?= $e['id'] ?>"
                        class="btn px-5 py-2 fw-bold text-white rounded-3 shadow-sm hover-lift"
                        style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); border: none;">
                        <i class="bi bi-trash me-2"></i> Delete Event
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  function previewImage(input, previewId) {
    const container = document.getElementById(previewId + '-container');
    const placeholder = document.getElementById('upload-placeholder-' + (previewId.includes('edit') ? previewId.replace('preview-', '') : 'add'));
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

<style>
  .hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2) !important;
  }

  .form-control:focus,
  .form-select:focus {
    box-shadow: none;
    border-color: #667eea;
    background-color: #fff !important;
  }

  .input-group-text {
    border-color: #dee2e6;
  }

  .form-control {
    border-color: #dee2e6;
  }

  .input-group:focus-within .input-group-text {
    border-color: #667eea;
    background-color: #fff !important;
    color: #667eea !important;
  }
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>