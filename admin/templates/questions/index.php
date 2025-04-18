<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة الأسئلة</h1>
                <?php if ($exam): ?>
                <p class="text-muted">
                    الاختبار: <?php echo htmlspecialchars($exam['title']); ?> | 
                    الدورة: <?php echo htmlspecialchars($exam['course_title']); ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <?php if ($exam): ?>
                    <li class="breadcrumb-item"><a href="/admin/exams">الاختبارات</a></li>
                    <li class="breadcrumb-item active">أسئلة <?php echo htmlspecialchars($exam['title']); ?></li>
                    <?php else: ?>
                    <li class="breadcrumb-item active">الأسئلة</li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <h5 class="fw-bold mb-0 me-3">قائمة الأسئلة</h5>
                
                <?php if (!$exam): ?>
                <div class="ms-3">
                    <form action="/admin/questions" method="get" class="d-flex">
                        <select name="exam_id" class="form-select form-select-sm me-2 neo-select" onchange="this.form.submit()">
                            <option value="">جميع الاختبارات</option>
                            <?php foreach ($exams as $exam_item): ?>
                            <option value="<?php echo $exam_item['id']; ?>" <?php echo (isset($_GET['exam_id']) && $_GET['exam_id'] == $exam_item['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($exam_item['title']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            
            <a href="/admin/questions/create<?php echo $exam ? '?exam_id=' . $exam['id'] : ''; ?>" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة سؤال جديد
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="neo-table neo-table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>السؤال</th>
                            <th>النوع</th>
                            <th>الاختبار</th>
                            <th>النقاط</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($questions)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="table-empty-state">
                                    <i class="fas fa-question-circle"></i>
                                    <h4>لا توجد أسئلة</h4>
                                    <p>لم يتم العثور على أي أسئلة في النظام. يمكنك إضافة سؤال جديد باستخدام زر "إضافة سؤال جديد".</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($questions as $index => $question): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div>
                                        <?php echo htmlspecialchars(substr($question['question_text'], 0, 100) . (strlen($question['question_text']) > 100 ? '...' : '')); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($question['question_type'] === 'single_choice'): ?>
                                    <span class="neo-badge neo-badge-primary"><i class="fas fa-dot-circle me-1"></i> اختيار واحد</span>
                                    <?php elseif ($question['question_type'] === 'multiple_choice'): ?>
                                    <span class="neo-badge neo-badge-success"><i class="fas fa-check-square me-1"></i> اختيار متعدد</span>
                                    <?php elseif ($question['question_type'] === 'drag_drop'): ?>
                                    <span class="neo-badge neo-badge-info"><i class="fas fa-arrows-alt me-1"></i> سحب وإفلات</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($question['exam_title']); ?></td>
                                <td><?php echo $question['points']; ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/admin/questions/view/<?php echo $question['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/questions/edit/<?php echo $question['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $question['id']; ?>" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $question['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $question['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content neo-card">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $question['id']; ?>">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف هذا السؤال؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <a href="/admin/questions/delete/<?php echo $question['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>