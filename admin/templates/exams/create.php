<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إضافة اختبار جديد</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/exams">الاختبارات</a></li>
                    <li class="breadcrumb-item active">إضافة اختبار</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card neo-card">
        <div class="card-header">
            <h5 class="card-title">معلومات الاختبار</h5>
        </div>
        <div class="card-body">
            <?php
            // Display errors if any
            if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                echo '<div class="alert alert-danger neo-alert-danger">';
                echo '<ul class="mb-0">';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul>';
                echo '</div>';
                
                // Clear errors
                unset($_SESSION['errors']);
            }
            
            // Get form data if any
            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']);
            ?>
            
            <form action="/admin/exams/store" method="post" class="neo-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 neo-form-group">
                            <label for="title" class="form-label">عنوان الاختبار <span class="text-danger">*</span></label>
                            <input type="text" class="form-control neo-form-control" id="title" name="title" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3 neo-form-group">
                            <label for="course_id" class="form-label">الدورة</label>
                            <select class="form-select neo-form-select" id="course_id" name="course_id">
                                <option value="">اختر الدورة</option>
                                <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo (isset($form_data['course_id']) && $form_data['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 neo-form-group">
                    <label for="description" class="form-label">وصف الاختبار <span class="text-danger">*</span></label>
                    <textarea class="form-control neo-form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3 neo-form-group">
                            <label for="duration" class="form-label">مدة الاختبار (دقيقة) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control neo-form-control" id="duration" name="duration" min="1" value="<?php echo htmlspecialchars($form_data['duration'] ?? '60'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3 neo-form-group">
                            <label for="passing_score" class="form-label">درجة النجاح (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control neo-form-control" id="passing_score" name="passing_score" min="0" max="100" value="<?php echo htmlspecialchars($form_data['passing_score'] ?? '70'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3 neo-form-group">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select neo-form-select" id="status" name="status">
                                <option value="draft" <?php echo (isset($form_data['status']) && $form_data['status'] === 'draft') ? 'selected' : ''; ?>>مسودة</option>
                                <option value="published" <?php echo (isset($form_data['status']) && $form_data['status'] === 'published') ? 'selected' : ''; ?>>منشور</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 mb-4">
                    <h5>الدورات الموصى بها بعد الاختبار</h5>
                    <p class="text-muted">اختر الدورات التي ترغب في توصيتها للمستخدمين بعد إكمال هذا الاختبار.</p>
                    
                    <div class="row">
                        <?php
                        // Get all courses
                        global $conn;
                        $recommended_courses = [];
                        $all_courses = [];
                        if ($conn) {
                            $stmt = $conn->prepare("
                                SELECT id, title, company_url
                                FROM courses
                                WHERE is_published = 1
                                ORDER BY title
                            ");
                            $stmt->execute();
                            $all_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }
                        
                        if (!empty($all_courses)):
                            foreach ($all_courses as $course):
                        ?>
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="recommended_courses[]" value="<?php echo $course['id']; ?>" id="course_<?php echo $course['id']; ?>" <?php echo (isset($form_data['recommended_courses']) && in_array($course['id'], $form_data['recommended_courses'] ?? [])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="course_<?php echo $course['id']; ?>">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                    <?php if (!empty($course['company_url'])): ?>
                                    <small class="text-muted d-block"><?php echo htmlspecialchars($course['company_url']); ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                        <?php
                            endforeach;
                        else:
                        ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                لا توجد دورات متاحة للتوصية. يرجى إضافة دورات أولاً.
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/exams" class="btn btn-secondary neo-btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary neo-btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>