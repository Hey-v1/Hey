<?php
/**
 * Course View Template
 * 
 * This template displays the details of a course.
 */
?>

<div class="container mt-4">
    <div class="card neo-brutalism-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo htmlspecialchars($pageTitle); ?></h5>
            <div>
                <a href="/admin/courses/edit/<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="/admin/courses" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">العنوان</th>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                        </tr>
                        <tr>
                            <th>الرابط</th>
                            <td><?php echo htmlspecialchars($course['slug']); ?></td>
                        </tr>
                        <tr>
                            <th>رابط الشركة</th>
                            <td>
                                <?php if (!empty($course['company_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($course['company_url']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($course['company_url']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">غير محدد</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>الوصف</th>
                            <td><?php echo nl2br(htmlspecialchars($course['description'])); ?></td>
                        </tr>
                        <tr>
                            <th>السعر</th>
                            <td>
                                <?php if ($course['is_free']): ?>
                                    <span class="badge bg-success">مجاني</span>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($course['price']); ?> $
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                <?php if (isset($course['status']) && $course['status'] === 'published'): ?>
                                    <span class="badge bg-success">منشور</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">مسودة</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>المدرب</th>
                            <td><?php echo htmlspecialchars($course['instructor_name']); ?> (<?php echo htmlspecialchars($course['instructor_username']); ?>)</td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td><?php echo htmlspecialchars($course['created_at']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card neo-brutalism-card">
                        <div class="card-header">
                            <h6 class="mb-0">صورة الدورة</h6>
                        </div>
                        <div class="card-body text-center">
                            <?php if (!empty($course['image'])): ?>
                                <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="img-fluid" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="alert alert-info">
                                    لا توجد صورة للدورة
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>