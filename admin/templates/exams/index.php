<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة الاختبارات</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">الاختبارات</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">قائمة الاختبارات</h5>
            <a href="/admin/exams/create" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة اختبار جديد
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="neo-table neo-table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>الدورات الموصى بها</th>
                            <th>المدة (دقيقة)</th>
                            <th>درجة النجاح</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($exams)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="table-empty-state">
                                    <i class="fas fa-file-alt"></i>
                                    <h4>لا توجد اختبارات</h4>
                                    <p>لم يتم العثور على أي اختبارات في النظام. يمكنك إضافة اختبار جديد باستخدام زر "إضافة اختبار جديد".</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($exams as $index => $exam): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div>
                                        <?php echo htmlspecialchars($exam['title']); ?>
                                        <div class="text-muted small"><?php echo htmlspecialchars(substr($exam['description'], 0, 50) . (strlen($exam['description']) > 50 ? '...' : '')); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($exam['recommended_courses'])): ?>
                                        <?php 
                                            $courseNames = array_map(function($course) {
                                                return htmlspecialchars($course['title']);
                                            }, $exam['recommended_courses']);
                                            echo implode(', ', $courseNames);
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">لا توجد دورات موصى بها</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo isset($exam['duration']) ? $exam['duration'] : '30'; ?></td>
                                <td><?php echo $exam['passing_score']; ?>%</td>
                                <td>
                                    <?php if (isset($exam['status']) && $exam['status'] === 'published'): ?>
                                    <span class="status-badge status-active">منشور</span>
                                    <?php else: ?>
                                    <span class="status-badge status-pending">مسودة</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($exam['created_at'])); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/admin/exams/view/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/exams/edit/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/questions?exam_id=<?php echo $exam['id']; ?>" class="neo-btn neo-btn-sm neo-btn-primary" title="الأسئلة">
                                            <i class="fas fa-question-circle"></i>
                                        </a>
                                        <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $exam['id']; ?>" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $exam['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $exam['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content neo-card">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $exam['id']; ?>">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف الاختبار <strong><?php echo htmlspecialchars($exam['title']); ?></strong>؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <a href="/admin/exams/delete/<?php echo $exam['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
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