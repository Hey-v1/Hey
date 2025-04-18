<?php
/**
 * Admin Exam Controller
 * 
 * This controller handles exam management in the admin panel.
 */
class AdminExamController {
    /**
     * Display a list of exams
     */
    public function index() {
        global $conn;
        
        // Get exams
        $exams = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT e.*
                FROM exams e
                ORDER BY e.created_at DESC
            ");
            $stmt->execute();
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get recommended courses for each exam
            foreach ($exams as &$exam) {
                $stmt = $conn->prepare("
                    SELECT c.id, c.title
                    FROM exam_course_recommendations ecr
                    JOIN courses c ON ecr.course_id = c.id
                    WHERE ecr.exam_id = ?
                    ORDER BY ecr.priority
                ");
                $stmt->execute([$exam['id']]);
                $exam['recommended_courses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // Sample data
            $exams = [
                [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'description' => 'Practice exam covering CCNA fundamentals and networking basics.',
                    'duration' => 60,
                    'passing_score' => 70,
                    'status' => 'draft',
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2',
                    'description' => 'Advanced practice exam covering CCNA routing and switching topics.',
                    'duration' => 90,
                    'passing_score' => 70,
                    'status' => 'draft',
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 3,
                    'title' => 'CCNP Enterprise Core Exam',
                    'description' => 'Practice exam for CCNP Enterprise Core (350-401 ENCOR) certification.',
                    'duration' => 120,
                    'passing_score' => 75,
                    'status' => 'draft',
                    'course_id' => 4,
                    'course_title' => 'CCNP Enterprise',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 4,
                    'title' => 'Security+ Practice Test',
                    'description' => 'Comprehensive practice test for CompTIA Security+ certification.',
                    'duration' => 90,
                    'passing_score' => 75,
                    'status' => 'draft',
                    'course_id' => 2,
                    'course_title' => 'Security+ Certification',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 5,
                    'title' => 'Network+ Sample Questions',
                    'description' => 'Free sample questions for Network+ certification.',
                    'duration' => 30,
                    'passing_score' => 70,
                    'status' => 'draft',
                    'course_id' => 3,
                    'course_title' => 'Network+ Basics',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 6,
                    'title' => 'Linux Essentials Quiz',
                    'description' => 'Quick quiz to test your Linux knowledge.',
                    'duration' => 20,
                    'passing_score' => 70,
                    'status' => 'draft',
                    'course_id' => 5,
                    'course_title' => 'Linux Essentials',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إدارة الاختبارات';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to create a new exam
     */
    public function create() {
        global $conn;
        
        // Get courses
        $courses = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, title
                FROM courses
                ORDER BY title
            ");
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $courses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification'
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Certification'
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics'
                ],
                [
                    'id' => 4,
                    'title' => 'CCNP Enterprise'
                ],
                [
                    'id' => 5,
                    'title' => 'Cloud Computing Fundamentals'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إضافة اختبار جديد';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new exam
     */
    public function store() {
        global $conn;
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = intval($_POST['duration'] ?? 0);
        $passing_score = intval($_POST['passing_score'] ?? 0);
        $status = $_POST['status'] ?? 'draft';
        $course_id = !empty($_POST['course_id']) ? intval($_POST['course_id']) : null;
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'عنوان الاختبار مطلوب';
        }
        
        if (empty($description)) {
            $errors[] = 'وصف الاختبار مطلوب';
        }
        
        if ($duration <= 0) {
            $errors[] = 'مدة الاختبار يجب أن تكون أكبر من صفر';
        }
        
        if ($passing_score < 0 || $passing_score > 100) {
            $errors[] = 'درجة النجاح يجب أن تكون بين 0 و 100';
        }
        
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header('Location: /admin/exams/create');
            exit;
        }
        
        // Create exam
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO exams (title, slug, description, duration_minutes, passing_score, course_id, is_published, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                // Generate a slug from the title
                $slug = strtolower(str_replace(' ', '-', $title));
                
                // Convert status to is_published
                $is_published = ($status === 'published') ? 1 : 0;
                
                $stmt->execute([
                    $title,
                    $slug,
                    $description,
                    $duration,
                    $passing_score,
                    $course_id,
                    $is_published
                ]);
                
                $exam_id = $conn->lastInsertId();
                
                // Handle recommended courses
                if (isset($_POST['recommended_courses']) && is_array($_POST['recommended_courses'])) {
                    $recommended_courses = $_POST['recommended_courses'];
                    
                    // Insert recommended courses
                    $stmt = $conn->prepare("
                        INSERT INTO exam_course_recommendations (exam_id, course_id, priority, created_at, updated_at)
                        VALUES (?, ?, ?, NOW(), NOW())
                    ");
                    
                    $priority = 1;
                    foreach ($recommended_courses as $course_id) {
                        $stmt->execute([
                            $exam_id,
                            (int)$course_id,
                            $priority++
                        ]);
                    }
                }
                
                // Set success message
                setFlashMessage('تم إنشاء الاختبار بنجاح', 'success');
                
                // Redirect to exams list
                header('Location: /admin/exams');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error creating exam: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء إنشاء الاختبار', 'danger');
                
                // Redirect back to the form
                header('Location: /admin/exams/create');
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم إنشاء الاختبار بنجاح (وضع العرض)', 'success');
            
            // Redirect to exams list
            header('Location: /admin/exams');
            exit;
        }
    }
    
    /**
     * Display an exam
     * 
     * @param int $id The exam ID
     */
    public function view($id) {
        global $conn;
        
        // Get exam
        $exam = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT e.*, c.title as course_title
                FROM exams e
                LEFT JOIN courses c ON e.course_id = c.id
                WHERE e.id = ?
            ");
            $stmt->execute([$id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $exams = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'description' => 'Practice exam covering CCNA routing and switching topics.',
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'time_limit' => 60,
                    'passing_score' => 70,
                    'status' => 'published',
                    'created_at' => '2023-07-10 10:00:00'
                ],
                2 => [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2',
                    'description' => 'Advanced practice exam for CCNA certification.',
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'time_limit' => 90,
                    'passing_score' => 75,
                    'status' => 'published',
                    'created_at' => '2023-07-11 09:30:00'
                ]
            ];
            
            $exam = $exams[$id] ?? null;
        }
        
        if (!$exam) {
            // Set error message
            setFlashMessage('الاختبار غير موجود', 'danger');
            
            // Redirect to exams list
            header('Location: /admin/exams');
            exit;
        }
        
        // Get questions count
        $questionsCount = 0;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count
                FROM questions
                WHERE exam_id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $questionsCount = $result['count'] ?? 0;
        } else {
            // Sample data
            $questionsCount = $id == 1 ? 3 : 2;
        }
        
        // Set page title
        $pageTitle = 'عرض الاختبار: ' . $exam['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to edit an exam
     * 
     * @param int $id The exam ID
     */
    public function edit($id) {
        global $conn;
        
        // Get exam
        $exam = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT *
                FROM exams
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $exams = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'description' => 'Practice exam covering CCNA fundamentals and networking basics.',
                    'duration' => 60,
                    'passing_score' => 70,
                    'status' => 'draft',
                    'course_id' => 1
                ],
                2 => [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2',
                    'description' => 'Advanced practice exam covering CCNA routing and switching topics.',
                    'duration' => 90,
                    'passing_score' => 70,
                    'status' => 'draft',
                    'course_id' => 1
                ],
                3 => [
                    'id' => 3,
                    'title' => 'CCNP Enterprise Core Exam',
                    'description' => 'Practice exam for CCNP Enterprise Core (350-401 ENCOR) certification.',
                    'duration' => 120,
                    'passing_score' => 75,
                    'status' => 'draft',
                    'course_id' => 4
                ]
            ];
            
            $exam = $exams[$id] ?? null;
        }
        
        if (!$exam) {
            // Set error message
            setFlashMessage('الاختبار غير موجود', 'danger');
            
            // Redirect to exams list
            header('Location: /admin/exams');
            exit;
        }
        
        // Get courses
        $courses = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, title
                FROM courses
                ORDER BY title
            ");
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $courses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification'
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Certification'
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics'
                ],
                [
                    'id' => 4,
                    'title' => 'CCNP Enterprise'
                ],
                [
                    'id' => 5,
                    'title' => 'Cloud Computing Fundamentals'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'تعديل الاختبار';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/exams/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update an exam
     * 
     * @param int $id The exam ID
     */
    public function update($id) {
        global $conn;
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration = intval($_POST['duration'] ?? 0);
        $passing_score = intval($_POST['passing_score'] ?? 0);
        $status = $_POST['status'] ?? 'draft';
        $course_id = !empty($_POST['course_id']) ? intval($_POST['course_id']) : null;
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'عنوان الاختبار مطلوب';
        }
        
        if (empty($description)) {
            $errors[] = 'وصف الاختبار مطلوب';
        }
        
        if ($duration <= 0) {
            $errors[] = 'مدة الاختبار يجب أن تكون أكبر من صفر';
        }
        
        if ($passing_score < 0 || $passing_score > 100) {
            $errors[] = 'درجة النجاح يجب أن تكون بين 0 و 100';
        }
        
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header("Location: /admin/exams/edit/$id");
            exit;
        }
        
        // Update exam
        if ($conn) {
            try {
                // Generate a slug from the title
                $slug = strtolower(str_replace(' ', '-', $title));
                
                // Convert status to is_published
                $is_published = ($status === 'published') ? 1 : 0;
                
                $stmt = $conn->prepare("
                    UPDATE exams
                    SET title = ?, slug = ?, description = ?, duration_minutes = ?, passing_score = ?, is_published = ?, course_id = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title,
                    $slug,
                    $description,
                    $duration,
                    $passing_score,
                    $is_published,
                    $course_id,
                    $id
                ]);
                
                // Handle recommended courses
                $recommended_courses = $_POST['recommended_courses'] ?? [];
                
                // First, delete all existing recommendations for this exam
                $stmt = $conn->prepare("DELETE FROM exam_course_recommendations WHERE exam_id = ?");
                $stmt->execute([$id]);
                
                // Then, insert new recommendations
                if (!empty($recommended_courses)) {
                    $stmt = $conn->prepare("
                        INSERT INTO exam_course_recommendations (exam_id, course_id, priority)
                        VALUES (?, ?, ?)
                    ");
                    
                    foreach ($recommended_courses as $priority => $course_id) {
                        $stmt->execute([$id, $course_id, $priority + 1]);
                    }
                }
                
                // Set success message
                setFlashMessage('تم تحديث الاختبار والدورات الموصى بها بنجاح', 'success');
                
                // Redirect to exams list
                header('Location: /admin/exams');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating exam: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث الاختبار', 'danger');
                
                // Redirect back to the form
                header("Location: /admin/exams/edit/$id");
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث الاختبار بنجاح (وضع العرض)', 'success');
            
            // Redirect to exams list
            header('Location: /admin/exams');
            exit;
        }
    }
    
    /**
     * Delete an exam
     * 
     * @param int $id The exam ID
     */
    public function delete($id) {
        global $conn;
        
        if ($conn) {
            try {
                // Delete exam
                $stmt = $conn->prepare("DELETE FROM exams WHERE id = ?");
                $stmt->execute([$id]);
                
                // Set success message
                setFlashMessage('تم حذف الاختبار بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting exam: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف الاختبار', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم حذف الاختبار بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect to exams list
        header('Location: /admin/exams');
        exit;
    }
}