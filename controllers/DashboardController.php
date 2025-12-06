<?php
/**
 * DASHBOARD CONTROLLER
 * ====================
 * Handles all admin dashboard analytics and statistics
 */

class DashboardController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Display main dashboard view
     */
    public function index()
    {
        // Check if user is admin
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
            header("Location: ?page=login");
            exit();
        }

        include __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Get key statistics (JSON)
     */
    public function getStats()
    {
        header('Content-Type: application/json');

        try {
            // Total events
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM evenements");
            $totalEvents = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Upcoming events (future)
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM evenements WHERE date_event > CURDATE()");
            $upcomingEvents = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total participants (total inscriptions)
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM inscriptions");
            $totalParticipants = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total categories
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM categories");
            $totalCategories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            echo json_encode([
                'success' => true,
                'data' => [
                    'totalEvents' => (int) $totalEvents,
                    'upcomingEvents' => (int) $upcomingEvents,
                    'totalParticipants' => (int) $totalParticipants,
                    'totalCategories' => (int) $totalCategories
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch statistics'
            ]);
        }
        exit();
    }

    /**
     * Get events per month for last 12 months (JSON)
     */
    public function getEventsPerMonth()
    {
        header('Content-Type: application/json');

        try {
            $query = "
                SELECT 
                    DATE_FORMAT(date_event, '%Y-%m') as month,
                    DATE_FORMAT(date_event, '%b %Y') as label,
                    COUNT(*) as count
                FROM evenements
                WHERE date_event >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY month, label
                ORDER BY month ASC
            ";

            $stmt = $this->pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fill in missing months with 0
            $months = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $label = date('M Y', strtotime("-$i months"));
                $months[$date] = ['month' => $date, 'label' => $label, 'count' => 0];
            }

            foreach ($results as $row) {
                if (isset($months[$row['month']])) {
                    $months[$row['month']]['count'] = (int) $row['count'];
                }
            }

            echo json_encode([
                'success' => true,
                'data' => array_values($months)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch events per month'
            ]);
        }
        exit();
    }

    /**
     * Get category distribution (JSON)
     */
    public function getCategoryDistribution()
    {
        header('Content-Type: application/json');

        try {
            $query = "
                SELECT 
                    c.nom as category,
                    COUNT(e.id) as count
                FROM categories c
                LEFT JOIN evenements e ON c.id = e.category_id
                GROUP BY c.id, c.nom
                ORDER BY count DESC
            ";

            $stmt = $this->pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array_map(function ($row) {
                return [
                    'category' => $row['category'],
                    'count' => (int) $row['count']
                ];
            }, $results);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch category distribution'
            ]);
        }
        exit();
    }

    /**
     * Get participants growth over time (JSON)
     */
    public function getParticipantsGrowth()
    {
        header('Content-Type: application/json');

        try {
            $query = "
                SELECT 
                    DATE_FORMAT(date_inscription, '%Y-%m') as month,
                    DATE_FORMAT(date_inscription, '%b %Y') as label,
                    COUNT(*) as count
                FROM inscriptions
                WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY month, label
                ORDER BY month ASC
            ";

            $stmt = $this->pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fill in missing months with 0
            $months = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $label = date('M Y', strtotime("-$i months"));
                $months[$date] = ['month' => $date, 'label' => $label, 'count' => 0];
            }

            foreach ($results as $row) {
                if (isset($months[$row['month']])) {
                    $months[$row['month']]['count'] = (int) $row['count'];
                }
            }

            echo json_encode([
                'success' => true,
                'data' => array_values($months)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch participants growth'
            ]);
        }
        exit();
    }

    /**
     * Get latest 5 events (JSON)
     */
    public function getLatestEvents()
    {
        header('Content-Type: application/json');

        try {
            $query = "
                SELECT 
                    e.id,
                    e.titre,
                    e.date_event,
                    e.lieu,
                    c.nom as categorie,
                    COUNT(i.id) as participants
                FROM evenements e
                LEFT JOIN categories c ON e.category_id = c.id
                LEFT JOIN inscriptions i ON e.id = i.evenement_id
                GROUP BY e.id, e.titre, e.date_event, e.lieu, c.nom
                ORDER BY e.id DESC
                LIMIT 5
            ";

            $stmt = $this->pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array_map(function ($row) {
                return [
                    'id' => (int) $row['id'],
                    'titre' => $row['titre'],
                    'date_event' => $row['date_event'],
                    'lieu' => $row['lieu'],
                    'categorie' => $row['categorie'] ?? 'Non catégorisé',
                    'participants' => (int) $row['participants']
                ];
            }, $results);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch latest events'
            ]);
        }
        exit();
    }

    /**
     * Get upcoming 5 events (JSON)
     */
    public function getUpcomingEvents()
    {
        header('Content-Type: application/json');

        try {
            $query = "
                SELECT 
                    e.id,
                    e.titre,
                    e.date_event,
                    e.lieu,
                    c.nom as categorie,
                    DATEDIFF(e.date_event, CURDATE()) as days_until
                FROM evenements e
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE e.date_event > CURDATE()
                ORDER BY e.date_event ASC
                LIMIT 5
            ";

            $stmt = $this->pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array_map(function ($row) {
                return [
                    'id' => (int) $row['id'],
                    'titre' => $row['titre'],
                    'date_event' => $row['date_event'],
                    'lieu' => $row['lieu'],
                    'categorie' => $row['categorie'] ?? 'Non catégorisé',
                    'days_until' => (int) $row['days_until']
                ];
            }, $results);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch upcoming events'
            ]);
        }
        exit();
    }

    /**
     * Get top 5 events by participant count (JSON)
     */
    public function getTopEvents()
    {
        header('Content-Type: application/json');

        try {
            $query = "
                SELECT 
                    e.id,
                    e.titre,
                    e.date_event,
                    c.nom as categorie,
                    COUNT(i.id) as participants
                FROM evenements e
                LEFT JOIN categories c ON e.category_id = c.id
                LEFT JOIN inscriptions i ON e.id = i.evenement_id
                GROUP BY e.id, e.titre, e.date_event, c.nom
                ORDER BY participants DESC
                LIMIT 5
            ";

            $stmt = $this->pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array_map(function ($row) {
                return [
                    'id' => (int) $row['id'],
                    'titre' => $row['titre'],
                    'date_event' => $row['date_event'],
                    'categorie' => $row['categorie'] ?? 'Non catégorisé',
                    'participants' => (int) $row['participants']
                ];
            }, $results);

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch top events'
            ]);
        }
        exit();
    }
}
