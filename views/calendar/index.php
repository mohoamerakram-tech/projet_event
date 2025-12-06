<?php load_header(); ?>

<div class="calendar-app container-fluid py-4">
    <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-xl-3 col-lg-4">
            <aside class="calendar-sidebar card border-0 shadow-sm p-4 h-100">
                <button class="btn btn-primary w-100 mb-3 py-2 rounded-pill" id="openCreateModal">
                    <i class="bi bi-plus-lg me-1"></i> Créer
                </button>

                <div class="mini-month card border-0 bg-light mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" id="miniPrevMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <h6 class="fw-semibold mb-0" id="miniMonthLabel">Novembre 2025</h6>
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" id="miniNextMonth">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>

                        <table class="mini-calendar-table w-100 text-center">
                            <thead>
                                <tr class="text-muted small fw-semibold">
                                    <?php $miniDays = ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di']; ?>
                                    <?php foreach ($miniDays as $mini): ?>
                                        <th><?= $mini ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody id="miniCalendarBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="timezone-card rounded-4 p-3 bg-white shadow-sm">
                    <p class="text-uppercase text-muted small mb-1">Fuseau horaire</p>
                    <h5 class="fw-bold mb-0">GMT+1 (Casablanca)</h5>
                    <p class="text-muted small mb-0">Africa/Casablanca</p>
                    <p class="text-muted small mb-0" id="sidebarToday"></p>
                </div>
            </aside>
        </div>

        <!-- Main calendar -->
        <div class="col-xl-9 col-lg-8">
            <div class="calendar-shell">
                <div class="calendar-toolbar d-flex flex-column flex-xl-row justify-content-between gap-3 align-items-start align-items-xl-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-light border rounded-circle" id="prevWeek">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="btn btn-light border rounded-circle" id="nextWeek">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <button class="btn btn-outline-primary rounded-pill px-4" id="todayBtn">Aujourd'hui</button>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1" id="calendarTitle">Calendrier</h2>
                        <p class="text-muted mb-0" id="calendarSubtitle">Vue hebdomadaire</p>
                    </div>
                    <div class="view-switch d-flex gap-2">
                        <button class="btn btn-light border rounded-pill px-3 py-2 active">Semaine</button>
                    </div>
                </div>

                <div class="calendar-surface card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="calendar-header d-grid">
                        <div class="time-column-header"></div>
                        <?php
                        $weekDays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                        foreach ($weekDays as $day): ?>
                            <div class="day-header text-center py-3">
                                <div class="fw-semibold text-uppercase small text-muted"><?= substr($day, 0, 3) ?></div>
                                <div class="day-number-header mt-1" data-day="<?= $day ?>"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="calendar-body-wrapper" style="max-height: 70vh; overflow-y: auto;">
                        <div class="calendar-body d-grid" id="calendarBody"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL REMOVED - Using global #addEventModal from header_admin.php -->

<style>
    .calendar-app {
        background: #f7f9fc;
    }

    .calendar-sidebar {
        border-radius: 18px;
        min-height: 100%;
    }

    .mini-calendar-table {
        border-collapse: separate;
        border-spacing: 4px;
    }

    .mini-calendar-table th {
        padding: 6px 0;
        font-weight: 600;
        color: #8b909a;
    }

    .mini-calendar-table td {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .mini-calendar-table td span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 10px;
    }

    .mini-calendar-table td:hover span {
        background: rgba(26, 115, 232, 0.12);
        color: #1a73e8;
    }

    .mini-calendar-table td.muted span {
        color: #c0c5d0;
    }

    .mini-calendar-table td.today span {
        background: #1a73e8;
        color: #fff;
        box-shadow: 0 6px 16px rgba(26, 115, 232, 0.35);
    }

    .calendar-shell {
        max-width: 1400px;
        margin: 0 auto;
    }

    .calendar-surface {
        background: #fff;
    }

    .calendar-header {
        grid-template-columns: 60px repeat(7, 1fr);
        border-bottom: 2px solid #e8eaed;
        background: #f8f9fa;
    }

    .time-column-header {
        border-right: 1px solid #e8eaed;
    }

    .day-header {
        border-right: 1px solid #e8eaed;
    }

    .day-header:last-child {
        border-right: none;
    }

    .day-number-header {
        font-size: 1.5rem;
        font-weight: 400;
        color: #3c4043;
    }

    .day-number-header.today {
        color: #1a73e8;
        font-weight: 600;
    }

    .calendar-body {
        grid-template-columns: 60px repeat(7, 1fr);
        position: relative;
    }

    .time-slot {
        height: 60px;
        border-bottom: 1px solid #e8eaed;
        border-right: 1px solid #e8eaed;
        position: relative;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .time-slot:hover {
        background-color: #f8f9fa;
    }

    .time-label {
        position: absolute;
        top: -8px;
        right: 8px;
        font-size: 0.75rem;
        color: #70757a;
        background: #fff;
        padding: 0 4px;
    }

    .time-slot.day-cell {
        border-right: 1px solid #e8eaed;
    }

    .time-slot.day-cell:last-child {
        border-right: none;
    }

    .time-slot.hour-mark {
        border-top: 1px solid #dadce0;
    }

    .event-block {
        position: absolute;
        left: 1px;
        right: 1px;
        background: #1a73e8;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
        z-index: 10;
        cursor: pointer;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .event-block:hover {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    .event-block.secondary {
        background: #34a853;
    }

    .event-block.tertiary {
        background: #ea4335;
    }

    .calendar-body-wrapper {
        scrollbar-width: thin;
        scrollbar-color: #dadce0 #fff;
    }

    .calendar-body-wrapper::-webkit-scrollbar {
        width: 8px;
    }

    .calendar-body-wrapper::-webkit-scrollbar-track {
        background: #fff;
    }

    .calendar-body-wrapper::-webkit-scrollbar-thumb {
        background: #dadce0;
        border-radius: 4px;
    }

    @media (max-width: 991.98px) {
        .calendar-app .row {
            flex-direction: column;
        }

        .calendar-sidebar {
            margin-bottom: 1.5rem;
        }

        .calendar-body-wrapper {
            max-height: 60vh;
        }
    }

    @media (max-width: 575.98px) {
        .calendar-toolbar {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        .calendar-toolbar>* {
            width: 100%;
            justify-content: space-between;
        }

        .view-switch {
            justify-content: space-between;
        }

        .calendar-header {
            grid-template-columns: 45px repeat(7, minmax(50px, 1fr));
        }

        .mini-calendar-table td,
        .mini-calendar-table td span {
            width: 32px;
            height: 32px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const calendarBody = document.getElementById('calendarBody');
        if (!calendarBody) return;

        const titleEl = document.getElementById('calendarTitle');
        const subtitleEl = document.getElementById('calendarSubtitle');
        const sidebarToday = document.getElementById('sidebarToday');
        const openCreateBtn = document.getElementById('openCreateModal');
        const miniCalendarBody = document.getElementById('miniCalendarBody');
        const miniMonthLabel = document.getElementById('miniMonthLabel');
        const miniPrevBtn = document.getElementById('miniPrevMonth');
        const miniNextBtn = document.getElementById('miniNextMonth');
        const nextWeekBtn = document.getElementById('nextWeek');
        const prevWeekBtn = document.getElementById('prevWeek');
        const todayBtn = document.getElementById('todayBtn');

        // References to global modal inputs (from header_admin.php)
        const globalModalId = 'addEventModal';
        const globalDateInputName = 'date_event';
        const globalTimeInputName = 'heure';

        let quickAddModalInstance = null;
        const getQuickAddModal = () => {
            // Look for global modal
            if (!quickAddModalInstance && typeof bootstrap !== 'undefined') {
                const modalEl = document.getElementById(globalModalId);
                if (modalEl) {
                    quickAddModalInstance = new bootstrap.Modal(modalEl);
                }
            }
            return quickAddModalInstance;
        };

        const normalizeDate = (value) => {
            if (!value) return '';
            return value.toString().substring(0, 10);
        };

        const events = <?php echo json_encode($evenements, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const eventsByDateTime = {};
        events.forEach(evt => {
            const dateKey = normalizeDate(evt.date_event);
            if (!dateKey) return;
            if (!eventsByDateTime[dateKey]) {
                eventsByDateTime[dateKey] = [];
            }
            eventsByDateTime[dateKey].push(evt);
        });
        Object.keys(eventsByDateTime).forEach(date => {
            eventsByDateTime[date].sort((a, b) => (a.heure || '') > (b.heure || '') ? 1 : -1);
        });

        let currentWeekStart = getWeekStart(new Date());
        let miniCurrent = new Date(currentWeekStart);
        const longDateFormatter = new Intl.DateTimeFormat('fr-FR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        function getWeekStart(date) {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day + (day === 0 ? -6 : 1);
            return new Date(d.setDate(diff));
        }

        function formatWeekRange(start) {
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            const monthFormatter = new Intl.DateTimeFormat('fr-FR', {
                month: 'long',
                year: 'numeric'
            });
            const dayFormatter = new Intl.DateTimeFormat('fr-FR', {
                day: 'numeric'
            });

            if (start.getMonth() === end.getMonth()) {
                return `${dayFormatter.format(start)} - ${dayFormatter.format(end)} ${monthFormatter.format(start)}`;
            }
            const startMonth = new Intl.DateTimeFormat('fr-FR', {
                month: 'long'
            }).format(start);
            const endMonth = new Intl.DateTimeFormat('fr-FR', {
                month: 'long',
                year: 'numeric'
            }).format(end);
            return `${dayFormatter.format(start)} ${startMonth} - ${dayFormatter.format(end)} ${endMonth}`;
        }

        function getMonthYear(start) {
            return new Intl.DateTimeFormat('fr-FR', {
                month: 'long',
                year: 'numeric'
            }).format(start);
        }

        function formatDateKey(dateObj) {
            const y = dateObj.getFullYear();
            const m = String(dateObj.getMonth() + 1).padStart(2, '0');
            const d = String(dateObj.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function openQuickAdd(date, time = '') {
            // Find global inputs
            const dateInput = document.querySelector(`input[name="${globalDateInputName}"]`);
            const timeInput = document.querySelector(`input[name="${globalTimeInputName}"]`);

            if (dateInput) dateInput.value = date;
            if (timeInput) timeInput.value = time;

            const modal = getQuickAddModal();
            if (modal) modal.show();
        }

        if (openCreateBtn) {
            openCreateBtn.addEventListener('click', () => {
                openQuickAdd(formatDateKey(new Date()));
            });
        }

        function renderCalendar() {
            calendarBody.innerHTML = '';
            titleEl.textContent = 'Calendrier - ' + getMonthYear(currentWeekStart);
            subtitleEl.textContent = formatWeekRange(currentWeekStart);
            if (sidebarToday) {
                sidebarToday.textContent = 'Aujourd\'hui : ' + longDateFormatter.format(new Date());
            }

            const dayHeaders = document.querySelectorAll('.day-number-header');
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            dayHeaders.forEach((header, i) => {
                const day = new Date(currentWeekStart);
                day.setDate(currentWeekStart.getDate() + i);
                header.textContent = day.getDate();
                header.classList.toggle('today', day.toDateString() === today.toDateString());
            });

            for (let hour = 0; hour < 24; hour++) {
                const timeSlot = document.createElement('div');
                timeSlot.className = 'time-slot';
                if (hour > 0) {
                    timeSlot.classList.add('hour-mark');
                }
                const timeLabel = document.createElement('div');
                timeLabel.className = 'time-label';
                timeLabel.textContent = hour.toString().padStart(2, '0') + ':00';
                timeSlot.appendChild(timeLabel);
                calendarBody.appendChild(timeSlot);

                for (let dayOffset = 0; dayOffset < 7; dayOffset++) {
                    const day = new Date(currentWeekStart);
                    day.setDate(currentWeekStart.getDate() + dayOffset);
                    day.setHours(hour, 0, 0, 0);

                    const dayCell = document.createElement('div');
                    dayCell.className = 'time-slot day-cell';
                    if (hour > 0) {
                        dayCell.classList.add('hour-mark');
                    }

                    dayCell.addEventListener('click', () => {
                        openQuickAdd(formatDateKey(day), hour.toString().padStart(2, '0') + ':00');
                    });

                    const isoDate = formatDateKey(day);
                    const dayEvents = eventsByDateTime[isoDate] || [];

                    dayEvents.forEach((evt, index) => {
                        let eventHour;
                        if (evt.heure) {
                            const eventTime = evt.heure.substring(0, 2);
                            eventHour = parseInt(eventTime, 10);
                        } else {
                            eventHour = 9 + (index % 9);
                        }

                        if (hour === eventHour) {
                            const eventBlock = document.createElement('div');
                            eventBlock.className = 'event-block';
                            if (index % 3 === 1) eventBlock.classList.add('secondary');
                            if (index % 3 === 2) eventBlock.classList.add('tertiary');

                            const timeLabel = evt.heure ? evt.heure.substring(0, 5) + ' - ' : '';
                            eventBlock.textContent = timeLabel + evt.titre;

                            let tooltip = evt.titre;
                            if (evt.heure) {
                                tooltip = evt.heure.substring(0, 5) + ' - ' + tooltip;
                            }
                            if (evt.description) {
                                tooltip += ' - ' + evt.description;
                            }
                            eventBlock.title = tooltip;
                            dayCell.appendChild(eventBlock);
                        }
                    });

                    calendarBody.appendChild(dayCell);
                }
            }
        }

        function renderMiniCalendar() {
            const year = miniCurrent.getFullYear();
            const month = miniCurrent.getMonth();
            if (miniMonthLabel) {
                miniMonthLabel.textContent = new Intl.DateTimeFormat('fr-FR', {
                    month: 'long',
                    year: 'numeric'
                }).format(miniCurrent);
            }

            const firstDay = new Date(year, month, 1);
            const startOffset = (firstDay.getDay() + 6) % 7;
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrev = new Date(year, month, 0).getDate();

            if (!miniCalendarBody) return;
            miniCalendarBody.innerHTML = '';
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            let rowEl;

            for (let i = 0; i < 42; i++) {
                if (i % 7 === 0) {
                    rowEl = document.createElement('tr');
                    miniCalendarBody.appendChild(rowEl);
                }
                const dayCell = document.createElement('td');
                const inner = document.createElement('span');
                let dayNumber;
                let displayDate;

                if (i < startOffset) {
                    dayNumber = daysInPrev - startOffset + i + 1;
                    displayDate = new Date(year, month - 1, dayNumber);
                    dayCell.classList.add('muted');
                } else if (i >= startOffset + daysInMonth) {
                    dayNumber = i - (startOffset + daysInMonth) + 1;
                    displayDate = new Date(year, month + 1, dayNumber);
                    dayCell.classList.add('muted');
                } else {
                    dayNumber = i - startOffset + 1;
                    displayDate = new Date(year, month, dayNumber);
                }

                inner.textContent = dayNumber;
                if (formatDateKey(displayDate) === formatDateKey(today)) {
                    dayCell.classList.add('today');
                }

                dayCell.addEventListener('click', () => {
                    currentWeekStart = getWeekStart(displayDate);
                    miniCurrent = new Date(displayDate);
                    renderCalendar();
                    renderMiniCalendar();
                });

                dayCell.appendChild(inner);
                rowEl.appendChild(dayCell);
            }
        }

        nextWeekBtn?.addEventListener('click', () => {
            currentWeekStart.setDate(currentWeekStart.getDate() + 7);
            miniCurrent = new Date(currentWeekStart);
            renderCalendar();
            renderMiniCalendar();
        });

        prevWeekBtn?.addEventListener('click', () => {
            currentWeekStart.setDate(currentWeekStart.getDate() - 7);
            miniCurrent = new Date(currentWeekStart);
            renderCalendar();
            renderMiniCalendar();
        });

        todayBtn?.addEventListener('click', () => {
            currentWeekStart = getWeekStart(new Date());
            miniCurrent = new Date();
            renderCalendar();
            renderMiniCalendar();
        });

        miniPrevBtn?.addEventListener('click', () => {
            miniCurrent.setMonth(miniCurrent.getMonth() - 1);
            renderMiniCalendar();
        });

        miniNextBtn?.addEventListener('click', () => {
            miniCurrent.setMonth(miniCurrent.getMonth() + 1);
            renderMiniCalendar();
        });

        renderCalendar();
        renderMiniCalendar();
    });
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>