<?php load_header(); ?>

<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 p-4" style="border-radius: 16px 16px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <?php if ($viewMode === 'details'): ?>
                        <div class="d-flex align-items-center gap-3">
                            <a href="?page=inscriptions_list" class="btn btn-light rounded-circle shadow-sm"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="mb-1 fw-bold"><?= htmlspecialchars($eventDetails['titre'] ?? 'Event Details') ?>
                                </h4>
                                <p class="text-muted mb-0 small">Participants List</p>
                            </div>
                            <?php if (!empty($eventDetails['capacite'])): ?>
                                <?php
                                $count = count($inscriptions);
                                $cap = $eventDetails['capacite'];
                                $pct = min(100, ($count / $cap) * 100);
                                $barColor = ($count >= $cap) ? 'bg-danger' : 'bg-primary';
                                ?>
                                <div class="ms-4 d-flex align-items-center gap-3">
                                    <div class="progress" style="width: 120px; height: 8px;">
                                        <div class="progress-bar <?= $barColor ?>" role="progressbar"
                                            style="width: <?= $pct ?>%" aria-valuenow="<?= $count ?>" aria-valuemin="0"
                                            aria-valuemax="<?= $cap ?>"></div>
                                    </div>
                                    <span class="small fw-bold text-muted"><?= $count ?>/<?= $cap ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div>
                            <h4 class="mb-1 fw-bold">Registrations Overview</h4>
                            <p class="text-muted mb-0 small">Select an event to view participants</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <?php if ($viewMode === 'details'): ?>
                        <a href="#" onclick="downloadPDF(); return false;" class="btn btn-light shadow-sm me-2"
                            title="Export PDF">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        <div class="btn-group shadow-sm" role="group">
                            <button type="button" class="btn btn-light active" id="view-toggle-list" title="List View">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button type="button" class="btn btn-light" id="view-toggle-grid" title="Grid View">
                                <i class="bi bi-grid-fill"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="stat-card-icon purple" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">

            <?php if ($viewMode === 'overview'): ?>
                <!-- OVERVIEW MODE: List of Events -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Event</th>
                                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Date</th>
                                <th class="px-4 py-3 text-secondary small fw-bold text-uppercase text-center">Participants
                                </th>
                                <th class="px-4 py-3 text-end text-secondary small fw-bold text-uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody id="inscriptions-table-body">
                            <?php foreach ($eventsStats as $stat): ?>
                                <tr style="cursor: pointer;"
                                    onclick="window.location='?page=inscriptions_list&event_id=<?= $stat['id'] ?>'">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="uploads/<?= !empty($stat['image']) ? htmlspecialchars($stat['image']) : 'default.jpg' ?>"
                                                alt="<?= htmlspecialchars($stat['titre']) ?>" class="rounded-3 object-fit-cover"
                                                style="width: 48px; height: 48px;">
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($stat['titre']) ?></div>
                                                <div class="small text-muted"><i
                                                        class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($stat['lieu']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-secondary fw-semibold">
                                        <?= date('d M Y', strtotime($stat['date_event'])) ?>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="badge bg-purple bg-opacity-10 text-purple rounded-pill px-3 py-2 fw-bold me-2"
                                            style="color: #7c3aed; background-color: #f3e8ff;">
                                            <i class="bi bi-people-fill me-1"></i> <?= $stat['total_participants'] ?>
                                        </span>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold">
                                            <i class="bi bi-person-check-fill me-1"></i>
                                            <?= $stat['capacite'] ? $stat['capacite'] : '∞' ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <a href="?page=inscriptions_list&event_id=<?= $stat['id'] ?>"
                                            class="btn btn-sm btn-light text-primary fw-bold px-3">
                                            View List <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($viewMode === 'details'): ?>
                <!-- DETAILS MODE: List of Participants for one Event -->
                <?php if (empty($inscriptions)): ?>
                    <div class="text-center p-5">
                        <div class="mb-3">
                            <i class="bi bi-person-x text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                        <h6 class="fw-bold text-muted">No participants found</h6>
                        <p class="text-muted small">No one has registered for this event yet.</p>
                    </div>
                <?php else: ?>
                    <div id="participants-list-view" class="table-responsive transition-fade">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc;">
                                <tr>
                                    <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Participant</th>
                                    <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Email</th>
                                    <th class="px-4 py-3 text-secondary small fw-bold text-uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody id="inscriptions-table-body">
                                <?php foreach ($inscriptions as $i): ?>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold"
                                                    style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                    <?= strtoupper(substr($i['nom_participant'], 0, 1)) ?>
                                                </div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($i['nom_participant']) ?></div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-secondary fw-semibold">
                                            <?= htmlspecialchars($i['email_participant']) ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">
                                                Confirmed
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="participants-grid-view" class="row g-4 p-4 d-none transition-fade">
                        <!-- Content loaded via AJAX -->
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Inject Event Data for PDF
    const eventTitle = "<?= isset($eventDetails['titre']) ? htmlspecialchars($eventDetails['titre']) : 'Event' ?>";
    const eventDate = "<?= isset($eventDetails['date_event']) ? date('d M Y', strtotime($eventDetails['date_event'])) : '' ?>";
    const eventLocation = "<?= isset($eventDetails['lieu']) ? htmlspecialchars($eventDetails['lieu']) : '' ?>";
    const eventImage = "<?= !empty($eventDetails['image']) ? 'uploads/' . htmlspecialchars($eventDetails['image']) : '' ?>";
    const totalParticipants = "<?= isset($inscriptions) ? count($inscriptions) : 0 ?>";

    function downloadPDF() {
        const urlParams = new URLSearchParams(window.location.search);
        const eventId = urlParams.get('event_id');
        const filename = 'Report_' + eventTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.pdf';

        const searchInput = document.getElementById('admin-search-input');
        const query = searchInput ? searchInput.value : '';

        let url = '?page=inscriptions_ajax&q=' + encodeURIComponent(query);
        if (eventId) {
            url += '&event_id=' + encodeURIComponent(eventId);
        }
        url += '&view=list';

        fetch(url)
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');

                // Construct Premium Header
                const headerContent = `
                    <div style="display: flex; align-items: center; margin-bottom: 30px; border-bottom: 3px solid #6f42c1; padding-bottom: 20px;">
                        ${eventImage ? `
                        <div style="width: 120px; height: 120px; flex-shrink: 0; margin-right: 25px;">
                            <img src="${eventImage}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px; border: 2px solid #eee;">
                        </div>` : ''}
                        <div style="flex-grow: 1;">
                            <div style="text-transform: uppercase; font-size: 10px; letter-spacing: 2px; color: #6f42c1; font-weight: bold; margin-bottom: 5px;">Event Report</div>
                            <h1 style="margin: 0 0 10px 0; color: #1a1a1a; font-size: 26px; font-weight: 800; line-height: 1.2;">${eventTitle}</h1>
                            <div style="display: flex; align-items: center; color: #6c757d; font-size: 13px;">
                                <span style="margin-right: 20px; display: flex; align-items: center;">
                                    <span style="margin-right: 5px; color: #6f42c1;">📅</span> ${eventDate}
                                </span>
                                <span style="display: flex; align-items: center;">
                                    <span style="margin-right: 5px; color: #6f42c1;">📍</span> ${eventLocation}
                                </span>
                            </div>
                        </div>
                        <div style="text-align: right; margin-left: auto;">
                            <div style="font-size: 32px; font-weight: 800; color: #6f42c1;">${totalParticipants}</div>
                            <div style="font-size: 11px; color: #adb5bd; text-transform: uppercase;">Participants</div>
                        </div>
                    </div>
                `;

                tempDiv.innerHTML = `
                    <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 30px; color: #333; background: #fff;">
                        ${headerContent}
                        
                        <h3 style="margin-bottom: 20px; color: #1a1a1a; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Participant List</h3>
                        
                        <table style="width: 100%; border-collapse: collapse; font-size: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <thead>
                                <tr style="background-color: #6f42c1; color: #ffffff;">
                                    <th style="padding: 15px; text-align: left; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Participant</th>
                                    <th style="padding: 15px; text-align: left; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                                    <th style="padding: 15px; text-align: left; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${html}
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; display: flex; justify-content: space-between; font-size: 10px; color: #adb5bd;">
                            <div>Generated by EventApp Platform</div>
                            <div>${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</div>
                        </div>
                    </div>
                `;

                // Apply Styles to Body Rows (since they come from AJAX raw HTML)
                const rows = tempDiv.querySelectorAll('tbody tr');
                rows.forEach((tr, index) => {
                    // Striping
                    if (index % 2 !== 0) {
                        tr.style.backgroundColor = '#f8f9fa';
                    }
                    const cells = tr.querySelectorAll('td');
                    cells.forEach(td => {
                        td.style.padding = '12px 15px';
                        td.style.borderBottom = '1px solid #e9ecef';
                        td.style.color = '#495057';
                    });

                    // Style specific cells differently if possible
                    const badges = tr.querySelectorAll('.badge');
                    badges.forEach(badge => {
                        badge.style.display = 'inline-block';
                        badge.style.padding = '4px 10px';
                        badge.style.borderRadius = '20px';
                        badge.style.fontSize = '10px';
                        badge.style.fontWeight = 'bold';
                        badge.style.backgroundColor = 'rgba(25, 135, 84, 0.1)';
                        badge.style.color = '#198754';
                        badge.style.border = '1px solid rgba(25, 135, 84, 0.2)';
                    });
                });

                const opt = {
                    margin: 0.4,
                    filename: filename,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        letterRendering: true
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                };

                html2pdf().from(tempDiv).set(opt).save();
            })
            .catch(err => console.error('Error generating PDF:', err));
    }

    document.addEventListener('DOMContentLoaded', function ()  {
        const searchInput = document.getElementById('admin-search-input');
        const tableBody = document.getElementById('inscriptions-table-body');
        const listView = document.getElementById('participants-list-view');
        const gridView = document.getElementById('participants-grid-view');
        const btnList = document.getElementById('view-toggle-list');
        const btnGrid = document.getElementById('view-toggle-grid');

        // State
        let currentView = 'list'; // 'list' or 'grid'
        let timeout = null;

        function loadData(query = '') {
            const urlParams = new URLSearchParams(window.location.search);
            const eventId = urlParams.get('event_id');

            let url = '?page=inscriptions_ajax&q=' + encodeURIComponent(query);
            if (eventId) {
                url += '&event_id=' + encodeURIComponent(eventId);
            }
            url += '&view=' + currentView;

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    if (currentView === 'list') {
                        if (tableBody) tableBody.innerHTML = html;
                        if (listView) listView.classList.remove('d-none');
                        if (gridView) gridView.classList.add('d-none');
                    } else {
                        if (gridView) {
                            gridView.innerHTML = html;
                            gridView.classList.remove('d-none');
                        }
                        if (listView) listView.classList.add('d-none');
                    }
                })
                .catch(err => console.error('Error loading data:', err));
        }

        // View Toggles
        if (btnList && btnGrid) {
            btnList.addEventListener('click', function ()  {
                if (currentView === 'list') return;
                currentView = 'list';
                btnList.classList.add('active');
                btnGrid.classList.remove('active');
                loadData(searchInput ? searchInput.value : ''); // Refresh data for new view
            });

            btnGrid.addEventListener('click', function ( ) {
                if (currentView === 'grid') return;
                currentView = 'grid';
                btnGrid.classList.add('active');
                btnList.classList.remove('active');
                loadData(searchInput ? searchInput.value : ''); // Refresh data for new view
            });
        }

        if (searchInput) {
            const searchForm = searchInput.closest('form');
            if (searchForm) {
                searchForm.addEventListener('submit', function  (e) {
                    e.preventDefault();
                });
            }

            searchInput.addEventListener('input', function  () {
                clearTimeout(timeout);
                const query = this.value;
                timeout = setTimeout(() => {
                    loadData(query);
                }, 300);
            });

            if (new URLSearchParams(window.location.search).get('q')) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        }
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>