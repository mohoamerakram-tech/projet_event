<?php
load_header();

// Separate events into upcoming and past
$upcomingEvents = [];
$pastEvents = [];
$currentDate = date('Y-m-d');

foreach ($evenements as $event) {
    if ($event['date_event'] >= $currentDate) {
        $upcomingEvents[] = $event;
    } else {
        $pastEvents[] = $event;
    }
}
?>

<!-- Libraries for QR Code and Screenshot -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Hero Section -->
<div class="container mt-4 mb-5">
    <div class="glass-panel p-5 position-relative overflow-hidden">
        <div class="position-relative z-1">
            <h1 class="display-4 fw-bold mb-2"
                style="background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Hello, <?= htmlspecialchars($_SESSION['user']['prenom'] ?? 'Explorer') ?>! 👋
            </h1>
            <p class="lead text-muted mb-4">Ready to discover your next great experience?</p>

            <div class="d-flex gap-3">
                <a href="index.php?page=home" class="btn btn-glass">
                    <i class="bi bi-compass me-2"></i> Browse Events
                </a>
            </div>
        </div>

        <!-- Decorative Background Elements -->
        <div class="position-absolute top-0 end-0 p-5 opacity-10">
            <i class="bi bi-calendar-check" style="font-size: 15rem; color: var(--primary);"></i>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="glass-panel p-4 d-flex align-items-center gap-4 h-100">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center"
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-ticket-perforated-fill text-primary fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0"><?= count($evenements) ?></h3>
                    <p class="text-muted mb-0">Events Joined</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-panel p-4 d-flex align-items-center gap-4 h-100">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 d-flex align-items-center justify-content-center"
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">0</h3>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Events Tabs & List -->
<div class="container mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold mb-0">My Events</h3>

        <ul class="nav nav-pills glass-panel p-1 border-0" id="eventsTab" role="tablist" style="border-radius: 50px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4" id="upcoming-tab" data-bs-toggle="tab"
                    data-bs-target="#upcoming" type="button" role="tab">Upcoming</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="past-tab" data-bs-toggle="tab" data-bs-target="#past"
                    type="button" role="tab">Past</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="eventsTabContent">
        <!-- Upcoming Events Tab -->
        <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
            <?php if (empty($upcomingEvents)): ?>
                <div class="glass-panel p-5 text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4>No upcoming events</h4>
                    <p class="text-muted">You haven't registered for any events yet.</p>
                    <a href="index.php?page=home" class="btn btn-primary rounded-pill mt-3">Explore Events</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($upcomingEvents as $e): ?>
                        <div class="glass-panel p-0 overflow-hidden transition-hover">
                            <div class="row g-0">
                                <div class="col-md-3 position-relative">
                                    <img src="uploads/<?= !empty($e['image']) ? $e['image'] : 'default.jpg' ?>"
                                        class="img-fluid h-100 w-100 object-fit-cover" style="min-height: 200px;"
                                        alt="<?= htmlspecialchars($e['titre']) ?>">
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-white text-primary shadow-sm rounded-pill px-3 py-2">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= date('M d, Y', strtotime($e['date_event'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="p-4 d-flex flex-column h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary mb-2 rounded-pill px-3">Registered</span>
                                                <h3 class="fw-bold mb-1"><?= htmlspecialchars($e['titre']) ?></h3>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-light rounded-circle" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-4">
                                                    <li><a class="dropdown-item text-danger" href="index.php?page=cancel_registration&id=<?= $e['id'] ?>" onclick="return confirm('Are you sure you want to cancel your registration?');"><i
                                                                class="bi bi-x-circle me-2"></i> Cancel Registration</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <p class="text-muted mb-4 flex-grow-1">
                                            <?= htmlspecialchars(substr($e['description'], 0, 150)) ?>...
                                        </p>

                                        <div class="d-flex align-items-center justify-content-between mt-auto">
                                            <div class="d-flex align-items-center gap-4 text-muted small">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-clock"></i>
                                                    <?= !empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : 'TBA' ?>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-geo-alt"></i>
                                                    <?= htmlspecialchars($e['lieu']) ?>
                                                </div>
                                            </div>

                                            <button class="btn btn-glass btn-sm px-4" onclick="openTicketModal(
                                                '<?= addslashes($e['id']) ?>',
                                                '<?= addslashes($e['titre']) ?>',
                                                '<?= addslashes(date('M d, Y', strtotime($e['date_event']))) ?>',
                                                '<?= addslashes($e['lieu']) ?>',
                                                '<?= addslashes(!empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : 'TBA') ?>',
                                                '<?= addslashes(($_SESSION['user']['nom'] ?? '') . ' ' . ($_SESSION['user']['prenom'] ?? '')) ?>'
                                            )">
                                                View Ticket
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- PAST Events Tab -->
        <div class="tab-pane fade" id="past" role="tabpanel">
            <?php if (empty($pastEvents)): ?>
                <div class="glass-panel p-5 text-center">
                    <div class="mb-3">
                        <i class="bi bi-clock-history text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4>No past events</h4>
                    <p class="text-muted">Your event history will appear here.</p>
                    <a href="index.php?page=home" class="btn btn-primary rounded-pill mt-3">Explore Events</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($pastEvents as $e): ?>
                        <div class="glass-panel p-0 overflow-hidden transition-hover">
                            <div class="row g-0">
                                <div class="col-md-3 position-relative">
                                    <img src="uploads/<?= !empty($e['image']) ? $e['image'] : 'default.jpg' ?>"
                                        class="img-fluid h-100 w-100 object-fit-cover" style="min-height: 200px;"
                                        alt="<?= htmlspecialchars($e['titre']) ?>">
                                    <div class="position-absolute top-0 start-0 m-3">
                                        <span class="badge bg-secondary text-white shadow-sm rounded-pill px-3 py-2">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            <?= date('M d, Y', strtotime($e['date_event'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="p-4 d-flex flex-column h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary mb-2 rounded-pill px-3">Completed</span>
                                                <h3 class="fw-bold mb-1"><?= htmlspecialchars($e['titre']) ?></h3>
                                            </div>
                                        </div>

                                        <p class="text-muted mb-4 flex-grow-1">
                                            <?= htmlspecialchars(substr($e['description'], 0, 150)) ?>...
                                        </p>

                                        <div class="d-flex align-items-center justify-content-between mt-auto">
                                            <div class="d-flex align-items-center gap-4 text-muted small">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-clock"></i>
                                                    <?= !empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : 'TBA' ?>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-geo-alt"></i>
                                                    <?= htmlspecialchars($e['lieu']) ?>
                                                </div>
                                            </div>

                                            <button class="btn btn-glass btn-sm px-4" onclick="openTicketModal(
                                                '<?= addslashes($e['id']) ?>',
                                                '<?= addslashes($e['titre']) ?>',
                                                '<?= addslashes(date('M d, Y', strtotime($e['date_event']))) ?>',
                                                '<?= addslashes($e['lieu']) ?>',
                                                '<?= addslashes(!empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : 'TBA') ?>',
                                                '<?= addslashes(($_SESSION['user']['nom'] ?? '') . ' ' . ($_SESSION['user']['prenom'] ?? '')) ?>'
                                            )">
                                                View Ticket
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hidden Ticket Template for Generation -->
<div id="ticket-template" class="d-none">
    <div class="ticket-card position-relative overflow-hidden">
        <!-- Gradient Header -->
        <div class="ticket-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="fw-bold mb-1 text-white">ENSA Events</h5>
                    <small class="text-white opacity-75">Official Event Pass</small>
                </div>
                <div class="ticket-badge">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
            </div>
        </div>

        <div class="row g-0">
            <!-- Left: Event Details -->
            <div class="col-8 ticket-content">
                <!-- Event Title -->
                <div class="mb-4">
                    <h2 id="ticket-title" class="ticket-event-title">Event Title</h2>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                        <i class="bi bi-ticket-perforated me-1"></i> General Admission
                    </span>
                </div>

                <!-- Event Information Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-6">
                        <div class="ticket-info-item">
                            <i class="bi bi-calendar3 ticket-icon"></i>
                            <div>
                                <small class="ticket-label">Date</small>
                                <p id="ticket-date" class="ticket-value">Oct 24, 2024</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="ticket-info-item">
                            <i class="bi bi-clock ticket-icon"></i>
                            <div>
                                <small class="ticket-label">Time</small>
                                <p id="ticket-time" class="ticket-value">18:00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="ticket-info-item">
                            <i class="bi bi-geo-alt ticket-icon"></i>
                            <div>
                                <small class="ticket-label">Location</small>
                                <p id="ticket-location" class="ticket-value">Main Auditorium</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendee Information -->
                <div class="ticket-attendee-section">
                    <small class="ticket-label">Attendee Name</small>
                    <p id="ticket-attendee" class="ticket-value mb-0">John Doe</p>
                </div>
            </div>

            <!-- Right: QR Code Section -->
            <div class="col-4 ticket-qr-section">
                <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4">
                    <div id="ticket-qr" class="ticket-qr-container mb-3"></div>
                    <small class="text-muted text-center fw-bold mb-2">Scan for Entry</small>
                    <div class="ticket-id-badge">
                        #<span id="ticket-id">12345</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative Perforations -->
        <div class="ticket-perforation ticket-perforation-top"></div>
        <div class="ticket-perforation ticket-perforation-bottom"></div>
    </div>
</div>

<!-- Ticket Preview Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-ticket-perforated-fill text-primary me-2"></i>
                    Your Event Ticket
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-center align-items-center"
                    style="min-height: 400px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 15px;">
                    <div id="ticket-preview-container"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Close
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="downloadTicket()">
                    <i class="bi bi-download me-2"></i>Download Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Event List Hover Effects */
    .transition-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .object-fit-cover {
        object-fit: cover;
    }

    /* Ticket Card Styles */
    .ticket-card {
        width: 900px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .ticket-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px 30px;
    }

    .ticket-badge {
        background: rgba(255, 255, 255, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .ticket-content {
        padding: 35px 40px;
        background: white;
    }

    .ticket-event-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 12px;
        line-height: 1.2;
    }

    .ticket-info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .ticket-icon {
        font-size: 20px;
        color: #667eea;
        margin-top: 4px;
    }

    .ticket-label {
        display: block;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        color: #a0aec0;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .ticket-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .ticket-attendee-section {
        border-top: 2px dashed #e2e8f0;
        padding-top: 20px;
    }

    .ticket-qr-section {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-left: 2px dashed #cbd5e0;
        position: relative;
    }

    .ticket-qr-container {
        background: white;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .ticket-id-badge {
        background: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 700;
        color: #667eea;
        font-size: 0.95rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* Decorative Perforations */
    .ticket-perforation {
        position: absolute;
        right: 33.333%;
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 50%;
        z-index: 10;
    }

    .ticket-perforation-top {
        top: 0;
        transform: translateY(-50%);
    }

    .ticket-perforation-bottom {
        bottom: 0;
        transform: translateY(50%);
    }

    /* Modal Customization */
    #ticketModal .modal-content {
        border-radius: 20px;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .ticket-card {
            width: 100%;
            max-width: 700px;
        }

        .ticket-event-title {
            font-size: 1.5rem;
        }

        .ticket-content {
            padding: 25px 30px;
        }
    }

    @media (max-width: 768px) {
        .ticket-card {
            border-radius: 15px;
        }

        .ticket-qr-section {
            border-left: none;
            border-top: 2px dashed #cbd5e0;
        }

        .ticket-perforation {
            right: 50%;
            transform: translateX(50%);
        }

        .ticket-perforation-top {
            top: auto;
            bottom: 33.333%;
            transform: translateX(50%) translateY(50%);
        }

        .ticket-perforation-bottom {
            bottom: auto;
            top: 33.333%;
            transform: translateX(50%) translateY(-50%);
        }
    }
</style>

<script>
    // Global variable to store current ticket ID
    let currentTicketId = null;

    /**
     * Opens the ticket modal with event details
     */
    function openTicketModal(id, title, date, location, time, attendee) {
        currentTicketId = id;

        // Populate ticket information in the template
        document.getElementById('ticket-title').textContent = title;
        document.getElementById('ticket-date').textContent = date;
        document.getElementById('ticket-location').textContent = location;
        document.getElementById('ticket-time').textContent = time;
        document.getElementById('ticket-attendee').textContent = attendee;
        document.getElementById('ticket-id').textContent = id;

        // Clone and display ticket template FIRST
        renderTicketPreview();

        // Generate QR Code in the CLONED element (not the template)
        generateQRCode(id, attendee);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
        modal.show();
    }

    /**
     * Generates QR code in the cloned preview container
     */
    function generateQRCode(id, attendee) {
        // Target the QR container in the CLONED preview, not the template
        const qrContainer = document.querySelector('#ticket-preview-container #ticket-qr');

        if (!qrContainer) {
            console.error('QR container not found in preview');
            return;
        }

        qrContainer.innerHTML = ''; // Clear previous QR code

        new QRCode(qrContainer, {
            text: `EVENT-${id}-${attendee}`,
            width: 120,
            height: 120,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    /**
     * Renders the ticket preview in the modal
     */
    function renderTicketPreview() {
        const preview = document.getElementById('ticket-preview-container');
        const template = document.getElementById('ticket-template');

        preview.innerHTML = '';
        const ticketClone = template.cloneNode(true);
        ticketClone.classList.remove('d-none');
        preview.appendChild(ticketClone);
    }

    /**
     * Downloads the ticket as a JPG image
     */
    function downloadTicket() {
        const ticketElement = document.querySelector('#ticket-preview-container .ticket-card');

        if (!ticketElement) {
            console.error('Ticket element not found');
            return;
        }

        // Use html2canvas to convert ticket to image
        html2canvas(ticketElement, {
            scale: 3,
            backgroundColor: "#ffffff", // Ensure white background for JPG
            logging: false,
            useCORS: true
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `event-ticket-${currentTicketId}.jpg`;
            link.href = canvas.toDataURL("image/jpeg", 0.9);
            link.click();
        }).catch(error => {
            console.error('Error generating ticket image:', error);
            alert('Failed to download ticket. Please try again.');
        });
    }
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>