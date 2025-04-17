<?php
/**
 * Admin Question Controller
 * 
 * This controller handles question management in the admin panel.
 */
class AdminQuestionController {
    /**
     * Display a list of questions
     */
    public function index() {
        global $conn;
        
        // Get exam ID from query string
        $exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
        
        // Get exam details if exam_id is provided
        $exam = null;
        if ($exam_id > 0) {
            if ($conn) {
                $stmt = $conn->prepare("
                    SELECT e.*, c.title as course_title
                    FROM exams e
                    LEFT JOIN courses c ON e.course_id = c.id
                    WHERE e.id = ?
                ");
                $stmt->execute([$exam_id]);
                $exam = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                // Sample data
                $exams = [
                    1 => [
                        'id' => 1,
                        'title' => 'CCNA Practice Exam 1',
                        'description' => 'Practice exam covering CCNA routing and switching topics.',
                        'course_id' => 1,
                        'course_title' => 'CCNA Certification'
                    ],
                    2 => [
                        'id' => 2,
                        'title' => 'CCNA Practice Exam 2',
                        'description' => 'Advanced practice exam for CCNA certification.',
                        'course_id' => 1,
                        'course_title' => 'CCNA Certification'
                    ]
                ];
                
                $exam = $exams[$exam_id] ?? null;
            }
        }
        
        // Get questions
        $questions = [];
        if ($conn) {
            $query = "
                SELECT q.*, e.title as exam_title
                FROM questions q
                LEFT JOIN exams e ON q.exam_id = e.id
            ";
            
            $params = [];
            
            if ($exam_id > 0) {
                $query .= " WHERE q.exam_id = ?";
                $params[] = $exam_id;
            }
            
            $query .= " ORDER BY q.created_at DESC";
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $questions = [
                [
                    'id' => 1,
                    'exam_id' => 1,
                    'exam_title' => 'CCNA Practice Exam 1',
                    'question_text' => 'Which of the following protocols operates at the Network layer of the OSI model?',
                    'question_type' => 'single_choice',
                    'options' => json_encode(['HTTP', 'TCP', 'IP', 'Ethernet']),
                    'correct_answer' => json_encode(['IP']),
                    'points' => 1,
                    'created_at' => '2023-07-10 10:30:00'
                ],
                [
                    'id' => 2,
                    'exam_id' => 1,
                    'exam_title' => 'CCNA Practice Exam 1',
                    'question_text' => 'Which of the following are valid IPv4 addresses? (Select all that apply)',
                    'question_type' => 'multiple_choice',
                    'options' => json_encode(['192.168.1.1', '256.0.0.1', '10.0.0.1', '172.16.0.300']),
                    'correct_answer' => json_encode(['192.168.1.1', '10.0.0.1']),
                    'points' => 2,
                    'created_at' => '2023-07-10 11:15:00'
                ],
                [
                    'id' => 3,
                    'exam_id' => 1,
                    'exam_title' => 'CCNA Practice Exam 1',
                    'question_text' => 'Match the following protocols with their default port numbers.',
                    'question_type' => 'drag_drop',
                    'options' => json_encode(['HTTP', 'HTTPS', 'FTP', 'SSH']),
                    'correct_answer' => json_encode(['80', '443', '21', '22']),
                    'points' => 3,
                    'created_at' => '2023-07-10 12:00:00'
                ],
                [
                    'id' => 4,
                    'exam_id' => 2,
                    'exam_title' => 'CCNA Practice Exam 2',
                    'question_text' => 'What is the maximum transmission unit (MTU) of an Ethernet frame?',
                    'question_type' => 'single_choice',
                    'options' => json_encode(['1500 bytes', '1460 bytes', '576 bytes', '1024 bytes']),
                    'correct_answer' => json_encode(['1500 bytes']),
                    'points' => 1,
                    'created_at' => '2023-07-11 09:45:00'
                ],
                [
                    'id' => 5,
                    'exam_id' => 2,
                    'exam_title' => 'CCNA Practice Exam 2',
                    'question_text' => 'Which of the following are routing protocols? (Select all that apply)',
                    'question_type' => 'multiple_choice',
                    'options' => json_encode(['OSPF', 'HTTP', 'BGP', 'DHCP', 'EIGRP']),
                    'correct_answer' => json_encode(['OSPF', 'BGP', 'EIGRP']),
                    'points' => 2,
                    'created_at' => '2023-07-11 10:30:00'
                ]
            ];
            
            // Filter questions by exam_id if provided
            if ($exam_id > 0) {
                $questions = array_filter($questions, function($question) use ($exam_id) {
                    return $question['exam_id'] == $exam_id;
                });
            }
        }
        
        // Get exams for filter dropdown
        $exams = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, title
                FROM exams
                ORDER BY title
            ");
            $stmt->execute();
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $exams = [
                [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1'
                ],
                [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2'
                ],
                [
                    'id' => 3,
                    'title' => 'Security+ Practice Exam'
                ],
                [
                    'id' => 4,
                    'title' => 'Network+ Basics Quiz'
                ],
                [
                    'id' => 5,
                    'title' => 'CCNP Enterprise Practice Exam'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إدارة الأسئلة';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/questions/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to create a new question
     */
    public function create() {
        global $conn;
        
        // Get exam ID from query string
        $exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
        
        // Get exams
        $exams = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, title
                FROM exams
                ORDER BY title
            ");
            $stmt->execute();
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $exams = [
                [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1'
                ],
                [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2'
                ],
                [
                    'id' => 3,
                    'title' => 'Security+ Practice Exam'
                ],
                [
                    'id' => 4,
                    'title' => 'Network+ Basics Quiz'
                ],
                [
                    'id' => 5,
                    'title' => 'CCNP Enterprise Practice Exam'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إضافة سؤال جديد';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/questions/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new question
     */
    public function store() {
        global $conn;
        
        // Validate input
        $exam_id = intval($_POST['exam_id'] ?? 0);
        $question_text = trim($_POST['question_text'] ?? '');
        $question_type = $_POST['question_type'] ?? '';
        $points = intval($_POST['points'] ?? 1);
        
        $errors = [];
        
        if ($exam_id <= 0) {
            $errors[] = 'يجب اختيار اختبار للسؤال';
        }
        
        if (empty($question_text)) {
            $errors[] = 'نص السؤال مطلوب';
        }
        
        if (!in_array($question_type, ['single_choice', 'multiple_choice', 'drag_drop'])) {
            $errors[] = 'نوع السؤال غير صالح';
        }
        
        if ($points <= 0) {
            $errors[] = 'النقاط يجب أن تكون أكبر من صفر';
        }
        
        // Process options and correct answers based on question type
        $options = [];
        $correct_answer = [];
        
        if ($question_type === 'single_choice') {
            $options = $_POST['options'] ?? [];
            $correct_option = isset($_POST['correct_option']) ? intval($_POST['correct_option']) : -1;
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if ($correct_option < 0 || $correct_option >= count($options)) {
                $errors[] = 'يجب تحديد الإجابة الصحيحة';
            } else {
                $correct_answer = [$options[$correct_option]];
            }
        } else if ($question_type === 'multiple_choice') {
            $options = $_POST['options'] ?? [];
            $correct_options = $_POST['correct_options'] ?? [];
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if (empty($correct_options)) {
                $errors[] = 'يجب تحديد إجابة صحيحة واحدة على الأقل';
            } else {
                foreach ($correct_options as $index) {
                    if (isset($options[$index])) {
                        $correct_answer[] = $options[$index];
                    }
                }
            }
        } else if ($question_type === 'drag_drop') {
            $drag_items = $_POST['drag_items'] ?? [];
            $drop_zones = $_POST['drop_zones'] ?? [];
            
            if (empty($drag_items)) {
                $errors[] = 'يجب إضافة عناصر للسحب';
            }
            
            if (empty($drop_zones)) {
                $errors[] = 'يجب إضافة مناطق للإفلات';
            }
            
            if (count($drag_items) !== count($drop_zones)) {
                $errors[] = 'عدد عناصر السحب يجب أن يساوي عدد مناطق الإفلات';
            }
            
            $options = $drag_items;
            $correct_answer = $drop_zones;
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header('Location: /admin/questions/create' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
            exit;
        }
        
        // Create question
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO questions (exam_id, question_text, question_type, options, correct_answer, points, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $exam_id,
                    $question_text,
                    $question_type,
                    json_encode($options),
                    json_encode($correct_answer),
                    $points
                ]);
                
                // Set success message
                setFlashMessage('تم إنشاء السؤال بنجاح', 'success');
                
                // Redirect to questions list
                header('Location: /admin/questions' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error creating question: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء إنشاء السؤال', 'danger');
                
                // Redirect back to the form
                header('Location: /admin/questions/create' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم إنشاء السؤال بنجاح (وضع العرض)', 'success');
            
            // Redirect to questions list
            header('Location: /admin/questions' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
            exit;
        }
    }
    
    /**
     * Display a question
     * 
     * @param int $id The question ID
     */
    public function view($id) {
        global $conn;
        
        // Get question
        $question = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT q.*, e.title as exam_title, c.title as course_title
                FROM questions q
                LEFT JOIN exams e ON q.exam_id = e.id
                LEFT JOIN courses c ON e.course_id = c.id
                WHERE q.id = ?
            ");
            $stmt->execute([$id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($question) {
                $question['options'] = json_decode($question['options'], true);
                $question['correct_answer'] = json_decode($question['correct_answer'], true);
            }
        } else {
            // Sample data
            $questions = [
                1 => [
                    'id' => 1,
                    'exam_id' => 1,
                    'exam_title' => 'CCNA Practice Exam 1',
                    'course_title' => 'CCNA Certification',
                    'question_text' => 'Which of the following protocols operates at the Network layer of the OSI model?',
                    'question_type' => 'single_choice',
                    'options' => ['HTTP', 'TCP', 'IP', 'Ethernet'],
                    'correct_answer' => ['IP'],
                    'points' => 1,
                    'created_at' => '2023-07-10 10:30:00'
                ],
                2 => [
                    'id' => 2,
                    'exam_id' => 1,
                    'exam_title' => 'CCNA Practice Exam 1',
                    'course_title' => 'CCNA Certification',
                    'question_text' => 'Which of the following are valid IPv4 addresses? (Select all that apply)',
                    'question_type' => 'multiple_choice',
                    'options' => ['192.168.1.1', '256.0.0.1', '10.0.0.1', '172.16.0.300'],
                    'correct_answer' => ['192.168.1.1', '10.0.0.1'],
                    'points' => 2,
                    'created_at' => '2023-07-10 11:15:00'
                ],
                3 => [
                    'id' => 3,
                    'exam_id' => 1,
                    'exam_title' => 'CCNA Practice Exam 1',
                    'course_title' => 'CCNA Certification',
                    'question_text' => 'Match the following protocols with their default port numbers.',
                    'question_type' => 'drag_drop',
                    'options' => ['HTTP', 'HTTPS', 'FTP', 'SSH'],
                    'correct_answer' => ['80', '443', '21', '22'],
                    'points' => 3,
                    'created_at' => '2023-07-10 12:00:00'
                ]
            ];
            
            $question = $questions[$id] ?? null;
        }
        
        if (!$question) {
            // Set error message
            setFlashMessage('السؤال غير موجود', 'danger');
            
            // Redirect to questions list
            header('Location: /admin/questions');
            exit;
        }
        
        // Set page title
        $pageTitle = 'عرض السؤال';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/questions/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to edit a question
     * 
     * @param int $id The question ID
     */
    public function edit($id) {
        global $conn;
        
        // Get question
        $question = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT *
                FROM questions
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($question) {
                $question['options'] = json_decode($question['options'], true);
                $question['correct_answer'] = json_decode($question['correct_answer'], true);
            }
        } else {
            // Sample data
            $questions = [
                1 => [
                    'id' => 1,
                    'exam_id' => 1,
                    'question_text' => 'Which of the following protocols operates at the Network layer of the OSI model?',
                    'question_type' => 'single_choice',
                    'options' => ['HTTP', 'TCP', 'IP', 'Ethernet'],
                    'correct_answer' => ['IP'],
                    'points' => 1
                ],
                2 => [
                    'id' => 2,
                    'exam_id' => 1,
                    'question_text' => 'Which of the following are valid IPv4 addresses? (Select all that apply)',
                    'question_type' => 'multiple_choice',
                    'options' => ['192.168.1.1', '256.0.0.1', '10.0.0.1', '172.16.0.300'],
                    'correct_answer' => ['192.168.1.1', '10.0.0.1'],
                    'points' => 2
                ],
                3 => [
                    'id' => 3,
                    'exam_id' => 1,
                    'question_text' => 'Match the following protocols with their default port numbers.',
                    'question_type' => 'drag_drop',
                    'options' => ['HTTP', 'HTTPS', 'FTP', 'SSH'],
                    'correct_answer' => ['80', '443', '21', '22'],
                    'points' => 3
                ]
            ];
            
            $question = $questions[$id] ?? null;
        }
        
        if (!$question) {
            // Set error message
            setFlashMessage('السؤال غير موجود', 'danger');
            
            // Redirect to questions list
            header('Location: /admin/questions');
            exit;
        }
        
        // Get exams
        $exams = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, title
                FROM exams
                ORDER BY title
            ");
            $stmt->execute();
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $exams = [
                [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1'
                ],
                [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2'
                ],
                [
                    'id' => 3,
                    'title' => 'Security+ Practice Exam'
                ],
                [
                    'id' => 4,
                    'title' => 'Network+ Basics Quiz'
                ],
                [
                    'id' => 5,
                    'title' => 'CCNP Enterprise Practice Exam'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'تعديل السؤال';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/questions/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update a question
     * 
     * @param int $id The question ID
     */
    public function update($id) {
        global $conn;
        
        // Validate input
        $exam_id = intval($_POST['exam_id'] ?? 0);
        $question_text = trim($_POST['question_text'] ?? '');
        $question_type = $_POST['question_type'] ?? '';
        $points = intval($_POST['points'] ?? 1);
        
        $errors = [];
        
        if ($exam_id <= 0) {
            $errors[] = 'يجب اختيار اختبار للسؤال';
        }
        
        if (empty($question_text)) {
            $errors[] = 'نص السؤال مطلوب';
        }
        
        if (!in_array($question_type, ['single_choice', 'multiple_choice', 'drag_drop'])) {
            $errors[] = 'نوع السؤال غير صالح';
        }
        
        if ($points <= 0) {
            $errors[] = 'النقاط يجب أن تكون أكبر من صفر';
        }
        
        // Process options and correct answers based on question type
        $options = [];
        $correct_answer = [];
        
        if ($question_type === 'single_choice') {
            $options = $_POST['options'] ?? [];
            $correct_option = isset($_POST['correct_option']) ? intval($_POST['correct_option']) : -1;
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if ($correct_option < 0 || $correct_option >= count($options)) {
                $errors[] = 'يجب تحديد الإجابة الصحيحة';
            } else {
                $correct_answer = [$options[$correct_option]];
            }
        } else if ($question_type === 'multiple_choice') {
            $options = $_POST['options'] ?? [];
            $correct_options = $_POST['correct_options'] ?? [];
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if (empty($correct_options)) {
                $errors[] = 'يجب تحديد إجابة صحيحة واحدة على الأقل';
            } else {
                foreach ($correct_options as $index) {
                    if (isset($options[$index])) {
                        $correct_answer[] = $options[$index];
                    }
                }
            }
        } else if ($question_type === 'drag_drop') {
            $drag_items = $_POST['drag_items'] ?? [];
            $drop_zones = $_POST['drop_zones'] ?? [];
            
            if (empty($drag_items)) {
                $errors[] = 'يجب إضافة عناصر للسحب';
            }
            
            if (empty($drop_zones)) {
                $errors[] = 'يجب إضافة مناطق للإفلات';
            }
            
            if (count($drag_items) !== count($drop_zones)) {
                $errors[] = 'عدد عناصر السحب يجب أن يساوي عدد مناطق الإفلات';
            }
            
            $options = $drag_items;
            $correct_answer = $drop_zones;
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header("Location: /admin/questions/edit/$id");
            exit;
        }
        
        // Update question
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    UPDATE questions
                    SET exam_id = ?, question_text = ?, question_type = ?, options = ?, correct_answer = ?, points = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $exam_id,
                    $question_text,
                    $question_type,
                    json_encode($options),
                    json_encode($correct_answer),
                    $points,
                    $id
                ]);
                
                // Set success message
                setFlashMessage('تم تحديث السؤال بنجاح', 'success');
                
                // Redirect to questions list
                header('Location: /admin/questions' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating question: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث السؤال', 'danger');
                
                // Redirect back to the form
                header("Location: /admin/questions/edit/$id");
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث السؤال بنجاح (وضع العرض)', 'success');
            
            // Redirect to questions list
            header('Location: /admin/questions' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
            exit;
        }
    }
    
    /**
     * Delete a question
     * 
     * @param int $id The question ID
     */
    public function delete($id) {
        global $conn;
        
        // Get exam_id for redirect
        $exam_id = 0;
        if ($conn) {
            $stmt = $conn->prepare("SELECT exam_id FROM questions WHERE id = ?");
            $stmt->execute([$id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);
            $exam_id = $question['exam_id'] ?? 0;
        }
        
        if ($conn) {
            try {
                // Delete question
                $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
                $stmt->execute([$id]);
                
                // Set success message
                setFlashMessage('تم حذف السؤال بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting question: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف السؤال', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم حذف السؤال بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect to questions list
        header('Location: /admin/questions' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
        exit;
    }
}