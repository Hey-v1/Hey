<?php
/**
 * Admin Courses Controller
 * 
 * This controller handles course management in the admin panel.
 */
class AdminCoursesController {
    /**
     * Display a list of courses
     */
    public function index() {
        global $conn;
        
        // Get courses
        $courses = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT c.*, a.username as instructor_username, a.name as instructor_name
                FROM courses c
                LEFT JOIN admins a ON c.admin_id = a.id
                ORDER BY c.created_at DESC
            ");
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $courses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'slug' => 'ccna-certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.',
                    'price' => 99.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 1,
                    'instructor_username' => 'admin',
                    'instructor_name' => 'Admin User',
                    'created_at' => '2023-07-10 09:00:00'
                ],
                [
                    'id' => 2,
                    'title' => 'CCNP Enterprise',
                    'slug' => 'ccnp-enterprise',
                    'description' => 'Advanced Cisco networking certification course.',
                    'price' => 199.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 1,
                    'instructor_username' => 'admin',
                    'instructor_name' => 'Admin User',
                    'created_at' => '2023-07-11 10:30:00'
                ],
                [
                    'id' => 3,
                    'title' => 'Security+ Certification',
                    'slug' => 'security-plus-certification',
                    'description' => 'CompTIA Security+ certification preparation course.',
                    'price' => 79.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-12 14:15:00'
                ],
                [
                    'id' => 4,
                    'title' => 'Network+ Basics',
                    'slug' => 'network-plus-basics',
                    'description' => 'Introduction to networking concepts for Network+ certification.',
                    'price' => 0,
                    'is_free' => 1,
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-13 11:45:00'
                ],
                [
                    'id' => 5,
                    'title' => 'Linux Essentials',
                    'slug' => 'linux-essentials',
                    'description' => 'Essential Linux skills for IT professionals.',
                    'price' => 49.99,
                    'is_free' => 0,
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-14 16:20:00'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إدارة الدورات';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to create a new course
     */
    public function create() {
        global $conn;
        
        // Get admins
        $instructors = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, username, name
                FROM admins
                ORDER BY name
            ");
            $stmt->execute();
            $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $instructors = [
                [
                    'id' => 1,
                    'username' => 'admin',
                    'name' => 'Admin User'
                ],
                [
                    'id' => 2,
                    'username' => 'instructor',
                    'name' => 'Instructor User'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'إضافة دورة جديدة';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new course
     */
    public function store() {
        global $conn;
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $company_url = trim($_POST['company_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $is_free = isset($_POST['is_free']) ? 1 : 0;
        $status = $_POST['status'] ?? 'draft';
        $admin_id = intval($_POST['admin_id'] ?? 0);
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'عنوان الدورة مطلوب';
        }
        
        if (empty($slug)) {
            // Generate slug from title
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        } else {
            // Validate slug format
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                $errors[] = 'الرابط المختصر يجب أن يحتوي على أحرف صغيرة وأرقام وشرطات فقط';
            }
        }
        
        if (empty($description)) {
            $errors[] = 'وصف الدورة مطلوب';
        }
        
        if ($is_free) {
            $price = 0;
        } else if ($price < 0) {
            $errors[] = 'السعر يجب أن يكون أكبر من أو يساوي صفر';
        }
        
        if ($admin_id <= 0) {
            $errors[] = 'يجب اختيار مدرب للدورة';
        }
        
        // Check if slug already exists
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM courses WHERE slug = ?");
            $stmt->execute([$slug]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'الرابط المختصر مستخدم بالفعل';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header('Location: /admin/courses/create');
            exit;
        }
        
        // Upload course image if provided
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = ROOT . '/uploads/courses/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = '/uploads/courses/' . $file_name;
            } else {
                // Set error message
                setFlashMessage('حدث خطأ أثناء رفع صورة الدورة', 'danger');
                
                // Redirect back to the form
                header('Location: /admin/courses/create');
                exit;
            }
        }
        
        // Create course
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO courses (title, slug, company_url, description, price, is_free, image, status, admin_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $title,
                    $slug,
                    $company_url,
                    $description,
                    $price,
                    $is_free,
                    $image_path,
                    $status,
                    $admin_id
                ]);
                
                // Set success message
                setFlashMessage('تم إنشاء الدورة بنجاح', 'success');
                
                // Redirect to courses list
                header('Location: /admin/courses');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error creating course: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء إنشاء الدورة', 'danger');
                
                // Redirect back to the form
                header('Location: /admin/courses/create');
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم إنشاء الدورة بنجاح (وضع العرض)', 'success');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
    }
    
    /**
     * Display a course
     * 
     * @param int $id The course ID
     */
    public function view($id) {
        global $conn;
        
        // Get course
        $course = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT c.*, a.username as instructor_username, a.name as instructor_name
                FROM courses c
                LEFT JOIN admins a ON c.admin_id = a.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $courses = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'slug' => 'ccna-certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.',
                    'price' => 99.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'admin_id' => 1,
                    'instructor_username' => 'admin',
                    'instructor_name' => 'Admin User',
                    'created_at' => '2023-07-10 09:00:00'
                ],
                2 => [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'slug' => 'security-plus-certification',
                    'description' => 'CompTIA Security+ certification preparation course.',
                    'price' => 79.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'admin_id' => 2,
                    'instructor_username' => 'instructor',
                    'instructor_name' => 'Instructor User',
                    'created_at' => '2023-07-12 14:15:00'
                ]
            ];
            
            $course = $courses[$id] ?? null;
        }
        
        if (!$course) {
            // Set error message
            setFlashMessage('الدورة غير موجودة', 'danger');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
        
        // Set page title
        $pageTitle = 'عرض الدورة: ' . $course['title'];
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the form to edit a course
     * 
     * @param int $id The course ID
     */
    public function edit($id) {
        global $conn;
        
        // Get course
        $course = null;
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT *
                FROM courses
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $courses = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'slug' => 'ccna-certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.',
                    'price' => 99.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'instructor_id' => 1
                ],
                2 => [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'slug' => 'security-plus-certification',
                    'description' => 'CompTIA Security+ certification preparation course.',
                    'price' => 79.99,
                    'is_free' => 0,
                    'image' => '',
                    'status' => 'published',
                    'instructor_id' => 2
                ]
            ];
            
            $course = $courses[$id] ?? null;
        }
        
        if (!$course) {
            // Set error message
            setFlashMessage('الدورة غير موجودة', 'danger');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
        
        // Get admins
        $instructors = [];
        if ($conn) {
            $stmt = $conn->prepare("
                SELECT id, username, name
                FROM admins
                ORDER BY name
            ");
            $stmt->execute();
            $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $instructors = [
                [
                    'id' => 1,
                    'username' => 'admin',
                    'name' => 'Admin User'
                ],
                [
                    'id' => 2,
                    'username' => 'instructor',
                    'name' => 'Instructor User'
                ]
            ];
        }
        
        // Set page title
        $pageTitle = 'تعديل الدورة';
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include ADMIN_ROOT . '/templates/courses/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update a course
     * 
     * @param int $id The course ID
     */
    public function update($id) {
        global $conn;
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $company_url = trim($_POST['company_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $is_free = isset($_POST['is_free']) ? 1 : 0;
        $status = $_POST['status'] ?? 'draft';
        $admin_id = intval($_POST['admin_id'] ?? 0);
        
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'عنوان الدورة مطلوب';
        }
        
        if (empty($slug)) {
            // Generate slug from title
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        } else {
            // Validate slug format
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                $errors[] = 'الرابط المختصر يجب أن يحتوي على أحرف صغيرة وأرقام وشرطات فقط';
            }
        }
        
        if (empty($description)) {
            $errors[] = 'وصف الدورة مطلوب';
        }
        
        if ($is_free) {
            $price = 0;
        } else if ($price < 0) {
            $errors[] = 'السعر يجب أن يكون أكبر من أو يساوي صفر';
        }
        
        if ($admin_id <= 0) {
            $errors[] = 'يجب اختيار مدرب للدورة';
        }
        
        // Check if slug already exists (excluding current course)
        if ($conn && empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM courses WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = 'الرابط المختصر مستخدم بالفعل';
            }
        }
        
        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            
            // Redirect back to the form
            header("Location: /admin/courses/edit/$id");
            exit;
        }
        
        // Get current course image
        $image_path = '';
        if ($conn) {
            $stmt = $conn->prepare("SELECT image FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            $current_course = $stmt->fetch(PDO::FETCH_ASSOC);
            $image_path = $current_course['image'] ?? '';
        }
        
        // Upload course image if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = ROOT . '/uploads/courses/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Delete old image if exists
                if (!empty($image_path) && file_exists(ROOT . $image_path)) {
                    unlink(ROOT . $image_path);
                }
                
                $image_path = '/uploads/courses/' . $file_name;
            } else {
                // Set error message
                setFlashMessage('حدث خطأ أثناء رفع صورة الدورة', 'danger');
                
                // Redirect back to the form
                header("Location: /admin/courses/edit/$id");
                exit;
            }
        }
        
        // Update course
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    UPDATE courses
                    SET title = ?, slug = ?, company_url = ?, description = ?, price = ?, is_free = ?, image = ?, status = ?, admin_id = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title,
                    $slug,
                    $company_url,
                    $description,
                    $price,
                    $is_free,
                    $image_path,
                    $status,
                    $admin_id,
                    $id
                ]);
                
                // Set success message
                setFlashMessage('تم تحديث الدورة بنجاح', 'success');
                
                // Redirect to courses list
                header('Location: /admin/courses');
                exit;
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating course: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء تحديث الدورة', 'danger');
                
                // Redirect back to the form
                header("Location: /admin/courses/edit/$id");
                exit;
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم تحديث الدورة بنجاح (وضع العرض)', 'success');
            
            // Redirect to courses list
            header('Location: /admin/courses');
            exit;
        }
    }
    
    /**
     * Delete a course
     * 
     * @param int $id The course ID
     */
    public function delete($id) {
        global $conn;
        
        if ($conn) {
            try {
                // Get course image
                $stmt = $conn->prepare("SELECT image FROM courses WHERE id = ?");
                $stmt->execute([$id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Delete course
                $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
                $stmt->execute([$id]);
                
                // Delete course image if exists
                if (!empty($course['image']) && file_exists(ROOT . $course['image'])) {
                    unlink(ROOT . $course['image']);
                }
                
                // Set success message
                setFlashMessage('تم حذف الدورة بنجاح', 'success');
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting course: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف الدورة', 'danger');
            }
        } else {
            // Set success message (for demo without database)
            setFlashMessage('تم حذف الدورة بنجاح (وضع العرض)', 'success');
        }
        
        // Redirect to courses list
        header('Location: /admin/courses');
        exit;
    }
}