<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة الدورات</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">الدورات</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">قائمة الدورات</h5>
            <a href="/admin/courses/create" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة دورة جديدة
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="neo-table neo-table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>رابط الشركة</th>
                            <th>المسؤول</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="table-empty-state">
                                    <i class="fas fa-book"></i>
                                    <h4>لا توجد دورات</h4>
                                    <p>لم يتم العثور على أي دورات في النظام. يمكنك إضافة دورة جديدة باستخدام زر "إضافة دورة جديدة".</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $index => $course): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($course['image'])): ?>
                                        <div class="me-2">
                                            <img src="<?php echo $course['image']; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <?php echo htmlspecialchars($course['title']); ?>
                                            <div class="text-muted small"><?php echo htmlspecialchars($course['slug']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($course['company_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($course['company_url']); ?>" target="_blank" class="text-primary">
                                            <?php echo htmlspecialchars($course['company_url']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">غير محدد</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($course['instructor_name'] ?? 'غير محدد'); ?></td>
                                <td>
                                    <?php if ($course['is_free']): ?>
                                    <span class="status-badge status-active">مجاني</span>
                                    <?php else: ?>
                                    <span class="fw-bold">$<?php echo number_format($course['price'], 2); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($course['status']) && $course['status'] === 'published'): ?>
                                    <span class="status-badge status-active">منشور</span>
                                    <?php else: ?>
                                    <span class="status-badge status-pending">مسودة</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($course['created_at'])); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/admin/courses/view/<?php echo $course['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/courses/edit/<?php echo $course['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $course['id']; ?>" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $course['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $course['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content neo-card">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $course['id']; ?>">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف الدورة <strong><?php echo htmlspecialchars($course['title']); ?></strong>؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <a href="/admin/courses/delete/<?php echo $course['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
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