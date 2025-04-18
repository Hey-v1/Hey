<?php
/**
 * Exam View Template
 * 
 * This template displays the details of an exam.
 */
?>

<div class="container mt-4">
    <div class="card neo-brutalism-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo htmlspecialchars($pageTitle); ?></h5>
            <div>
                <a href="/admin/questions?exam_id=<?php echo $exam['id']; ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-question-circle"></i> الأسئلة (<?php echo $questionsCount; ?>)
                </a>
                <a href="/admin/exams/edit/<?php echo $exam['id']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="/admin/exams" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">العنوان</th>
                            <td><?php echo htmlspecialchars($exam['title']); ?></td>
                        </tr>
                        <tr>
                            <th>الوصف</th>
                            <td><?php echo nl2br(htmlspecialchars($exam['description'])); ?></td>
                        </tr>
                        <tr>
                            <th>الدورة</th>
                            <td>
                                <a href="/admin/courses/view/<?php echo $exam['course_id']; ?>">
                                    <?php echo htmlspecialchars($exam['course_title']); ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>الوقت المحدد</th>
                            <td><?php echo htmlspecialchars($exam['time_limit'] ?? $exam['duration'] ?? 0); ?> دقيقة</td>
                        </tr>
                        <tr>
                            <th>درجة النجاح</th>
                            <td><?php echo htmlspecialchars($exam['passing_score']); ?>%</td>
                        </tr>
                        <tr>
                            <th>عدد الأسئلة</th>
                            <td><?php echo $questionsCount; ?></td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                <?php if (isset($exam['status']) && $exam['status'] === 'published'): ?>
                                    <span class="badge bg-success">منشور</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">مسودة</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td><?php echo htmlspecialchars($exam['created_at']); ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="mt-4">
                    <h5>الدورات الموصى بها بعد الاختبار</h5>
                    <?php
                    // Get recommended courses for this exam
                    global $conn;
                    $recommended_courses = [];
                    if ($conn) {
                        $stmt = $conn->prepare("
                            SELECT r.priority, c.id, c.title, c.company_url
                            FROM exam_course_recommendations r
                            JOIN courses c ON r.course_id = c.id
                            WHERE r.exam_id = ?
                            ORDER BY r.priority
                        ");
                        $stmt->execute([$exam['id']]);
                        $recommended_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    
                    if (!empty($recommended_courses)):
                    ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان الدورة</th>
                                    <th>رابط الشركة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recommended_courses as $index => $course): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <a href="/admin/courses/view/<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </a>
                                    </td>
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
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        لا توجد دورات موصى بها لهذا الاختبار. يمكنك إضافة توصيات من خلال <a href="/admin/exams/edit/<?php echo $exam['id']; ?>">تعديل الاختبار</a>.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>