<?php
/**
 * Admin Dashboard Controller
 * 
 * This controller handles the admin dashboard.
 */
class AdminDashboardController {
    /**
     * Display the dashboard
     */
    public function index() {
        global $conn;
        
        // Get statistics
        $totalUsers = 0;
        $totalCourses = 0;
        $totalExams = 0;
        $totalQuestions = 0;
        $totalEnrollments = 0;
        $totalExamAttempts = 0;
        $totalRevenue = 0;
        
        if ($conn) {
            // Get user count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
            $stmt->execute();
            $totalUsers = $stmt->fetchColumn();
            
            // Get course count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM courses");
            $stmt->execute();
            $totalCourses = $stmt->fetchColumn();
            
            // Get exam count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM exams");
            $stmt->execute();
            $totalExams = $stmt->fetchColumn();
            
            // Get question count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM questions");
            $stmt->execute();
            $totalQuestions = $stmt->fetchColumn();
            
            // Get enrollment count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments");
            $stmt->execute();
            $totalEnrollments = $stmt->fetchColumn();
            
            // Get exam attempt count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM exam_attempts");
            $stmt->execute();
            $totalExamAttempts = $stmt->fetchColumn();
            
            // Get revenue
            $stmt = $conn->prepare("SELECT SUM(amount) FROM payments WHERE status = 'completed'");
            $stmt->execute();
            $totalRevenue = $stmt->fetchColumn() ?: 0;
        } else {
            // Sample data if database connection is not available
            $totalUsers = 1250;
            $totalCourses = 15;
            $totalExams = 45;
            $totalQuestions = 1200;
            $totalEnrollments = 3500;
            $totalExamAttempts = 8750;
            $totalRevenue = 125000;
        }
        
        // Get recent users
        $recentUsers = [];
        if ($conn) {
            $stmt = $conn->prepare("SELECT id, username, email, first_name, last_name, created_at FROM users ORDER BY created_at DESC LIMIT 5");
            $stmt->execute();
            $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $recentUsers = [
                [
                    'id' => 1,
                    'username' => 'john_doe',
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'created_at' => '2023-07-15 10:30:00'
                ],
                [
                    'id' => 2,
                    'username' => 'jane_smith',
                    'email' => 'jane@example.com',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'created_at' => '2023-07-14 14:45:00'
                ],
                [
                    'id' => 3,
                    'username' => 'mohammed_ali',
                    'email' => 'mohammed@example.com',
                    'first_name' => 'Mohammed',
                    'last_name' => 'Ali',
                    'created_at' => '2023-07-13 09:15:00'
                ],
                [
                    'id' => 4,
                    'username' => 'sarah_johnson',
                    'email' => 'sarah@example.com',
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'created_at' => '2023-07-12 16:20:00'
                ],
                [
                    'id' => 5,
                    'username' => 'david_brown',
                    'email' => 'david@example.com',
                    'first_name' => 'David',
                    'last_name' => 'Brown',
                    'created_at' => '2023-07-11 11:10:00'
                ]
            ];
        }
        
        // Get recent courses
        $recentCourses = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT c.id, c.title, c.is_free, c.created_at, 
                       a.name
                FROM courses c
                JOIN admins a ON c.admin_id = a.id
                ORDER BY c.created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            $recentCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $recentCourses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'is_free' => 0,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-15 10:35:00'
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'is_free' => 0,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-14 15:00:00'
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics',
                    'is_free' => 1,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-13 09:30:00'
                ],
                [
                    'id' => 4,
                    'title' => 'CCNP Enterprise',
                    'is_free' => 0,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-12 16:45:00'
                ],
                [
                    'id' => 5,
                    'title' => 'Cloud Computing Fundamentals',
                    'is_free' => 1,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-11 11:30:00'
                ]
            ];
        }
        
        // Get recent exams
        $recentExams = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT e.id, e.title, e.duration_minutes, e.created_at,
                       c.title as course_title
                FROM exams e
                JOIN courses c ON e.course_id = c.id
                ORDER BY e.created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            $recentExams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $recentExams = [
                [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'course_title' => 'CCNA Certification',
                    'duration_minutes' => 60,
                    'created_at' => '2023-07-15 11:00:00'
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Practice Exam',
                    'course_title' => 'Security+ Certification',
                    'duration_minutes' => 90,
                    'created_at' => '2023-07-14 16:00:00'
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics Quiz',
                    'course_title' => 'Network+ Basics',
                    'duration_minutes' => 30,
                    'created_at' => '2023-07-13 10:00:00'
                ],
                [
                    'id' => 4,
                    'title' => 'CCNA Practice Exam 2',
                    'course_title' => 'CCNA Certification',
                    'duration_minutes' => 60,
                    'created_at' => '2023-07-12 17:00:00'
                ],
                [
                    'id' => 5,
                    'title' => 'CCNP Enterprise Practice Exam',
                    'course_title' => 'CCNP Enterprise',
                    'duration_minutes' => 120,
                    'created_at' => '2023-07-11 12:00:00'
                ]
            ];
        }
        
        // Get recent exam attempts
        $recentAttempts = [];
        $examPassFailData = ['passed' => 0, 'failed' => 0];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT ea.id, ea.user_id, ea.exam_id, ea.score, ea.is_passed, ea.created_at,
                       u.username, u.first_name, u.last_name,
                       e.title as exam_title
                FROM exam_attempts ea
                JOIN users u ON ea.user_id = u.id
                JOIN exams e ON ea.exam_id = e.id
                ORDER BY ea.created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            $recentAttempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate percentage for each attempt
            foreach ($recentAttempts as &$attempt) {
                // Get total questions for this exam
                $stmtQuestions = $conn->prepare("
                    SELECT COUNT(*) as total_questions 
                    FROM questions 
                    WHERE exam_id = ?
                ");
                $stmtQuestions->execute([$attempt['exam_id']]);
                $totalQuestions = $stmtQuestions->fetchColumn() ?: 1; // Avoid division by zero
                
                // Calculate percentage - score should be a percentage of correct answers
                // Assuming score is the number of correct answers
                $attempt['percentage'] = min(100, round(($attempt['score'] / $totalQuestions) * 100));
            }
            
            // Get pass/fail statistics for chart
            $stmtPassFail = $conn->prepare("
                SELECT is_passed, COUNT(*) as count
                FROM exam_attempts
                GROUP BY is_passed
            ");
            $stmtPassFail->execute();
            $passFailResults = $stmtPassFail->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($passFailResults as $result) {
                if ($result['is_passed'] == 1) {
                    $examPassFailData['passed'] = (int)$result['count'];
                } else {
                    $examPassFailData['failed'] = (int)$result['count'];
                }
            }
            
            // Get monthly revenue data for chart
            $revenueChartData = ['labels' => [], 'values' => []];
            $stmtRevenue = $conn->prepare("
                SELECT 
                    DATE_FORMAT(payment_date, '%Y-%m') as month,
                    SUM(amount) as total
                FROM payments
                WHERE status = 'completed'
                GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                ORDER BY month ASC
                LIMIT 6
            ");
            $stmtRevenue->execute();
            $revenueResults = $stmtRevenue->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($revenueResults as $result) {
                $monthDate = new DateTime($result['month'] . '-01');
                $revenueChartData['labels'][] = $monthDate->format('M Y');
                $revenueChartData['values'][] = (float)$result['total'];
            }
            
            // If no revenue data, provide sample data
            if (empty($revenueChartData['labels'])) {
                $revenueChartData = [
                    'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                    'values' => [12500, 15000, 10000, 20000, 17500, 22500]
                ];
            }
            
            // Get user registration data for chart
            $userChartData = ['labels' => [], 'values' => []];
            $stmtUsers = $conn->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as count
                FROM users
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
                LIMIT 6
            ");
            $stmtUsers->execute();
            $userResults = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($userResults as $result) {
                $monthDate = new DateTime($result['month'] . '-01');
                $userChartData['labels'][] = $monthDate->format('M Y');
                $userChartData['values'][] = (int)$result['count'];
            }
            
            // If no user data, provide sample data
            if (empty($userChartData['labels'])) {
                $userChartData = [
                    'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                    'values' => [25, 40, 35, 50, 45, 60]
                ];
            }
        } else {
            // Sample chart data
            $examPassFailData = ['passed' => 35, 'failed' => 15];
            $revenueChartData = [
                'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                'values' => [12500, 15000, 10000, 20000, 17500, 22500]
            ];
            $userChartData = [
                'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                'values' => [25, 40, 35, 50, 45, 60]
            ];
            
            // Sample data
            $recentAttempts = [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'exam_id' => 1,
                    'score' => 42,
                    'percentage' => 70,
                    'passed' => 1,
                    'username' => 'john_doe',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'exam_title' => 'CCNA Practice Exam 1',
                    'created_at' => '2023-07-15 11:30:00',
                    'completed_at' => '2023-07-15 11:30:00',
                    'passing_score' => 60
                ],
                [
                    'id' => 2,
                    'user_id' => 2,
                    'exam_id' => 3,
                    'score' => 67,
                    'percentage' => 75,
                    'passed' => 1,
                    'username' => 'jane_smith',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'exam_title' => 'Security+ Practice Exam',
                    'created_at' => '2023-07-14 16:15:00',
                    'completed_at' => '2023-07-14 16:15:00',
                    'passing_score' => 70
                ],
                [
                    'id' => 3,
                    'user_id' => 3,
                    'exam_id' => 4,
                    'score' => 27,
                    'percentage' => 90,
                    'passed' => 1,
                    'username' => 'mohammed_ali',
                    'first_name' => 'Mohammed',
                    'last_name' => 'Ali',
                    'exam_title' => 'Network+ Basics Quiz',
                    'created_at' => '2023-07-13 10:00:00',
                    'completed_at' => '2023-07-13 10:00:00',
                    'passing_score' => 25
                ],
                [
                    'id' => 4,
                    'user_id' => 4,
                    'exam_id' => 1,
                    'score' => 36,
                    'percentage' => 60,
                    'passed' => 0,
                    'username' => 'sarah_johnson',
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'exam_title' => 'CCNA Practice Exam 1',
                    'created_at' => '2023-07-12 17:30:00',
                    'completed_at' => '2023-07-12 17:30:00',
                    'passing_score' => 70
                ],
                [
                    'id' => 5,
                    'user_id' => 5,
                    'exam_id' => 5,
                    'score' => 85,
                    'percentage' => 85,
                    'passed' => 1,
                    'username' => 'david_brown',
                    'first_name' => 'David',
                    'last_name' => 'Brown',
                    'exam_title' => 'CCNP Enterprise Practice Exam',
                    'created_at' => '2023-07-11 12:45:00',
                    'completed_at' => '2023-07-11 12:45:00',
                    'passing_score' => 80
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'لوحة التحكم';
        
        // Add Chart.js library
        $extraStyles = '';
        $extraScripts = '
            <script src="/admin/assets/js/dashboard-charts.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Initialize Revenue Chart
                    const revenueData = ' . json_encode($revenueChartData) . ';
                    initRevenueChart(revenueData);
                    
                    // Initialize User Registration Chart
                    const userData = ' . json_encode($userChartData) . ';
                    initUserRegistrationChart(userData);
                    
                    // Initialize Exam Completion Chart
                    const examData = ' . json_encode($examPassFailData) . ';
                    initExamCompletionChart(examData);
                });
            </script>
        ';
        
        // Start output buffering
        ob_start();
        
        // Include the dashboard template
        include ADMIN_ROOT . '/templates/dashboard.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
}