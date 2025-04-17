<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إدارة المستخدمين</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item active">المستخدمين</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="neo-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">قائمة المستخدمين</h5>
            <a href="/admin/users/create" class="neo-btn neo-btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة مستخدم جديد
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="neo-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الاسم الكامل</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8">
                            <div class="table-empty-state">
                                <i class="fas fa-users"></i>
                                <h4>لا يوجد مستخدمين</h4>
                                <p>لم يتم العثور على أي مستخدمين في النظام. يمكنك إضافة مستخدم جديد باستخدام زر "إضافة مستخدم جديد".</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td>
                                <?php 
                                $roleLabels = [
                                    'admin' => '<span class="neo-badge neo-badge-danger"><i class="fas fa-user-shield me-1"></i> مدير</span>',
                                    'instructor' => '<span class="neo-badge neo-badge-primary"><i class="fas fa-chalkboard-teacher me-1"></i> مدرب</span>',
                                    'data_entry' => '<span class="neo-badge neo-badge-warning"><i class="fas fa-keyboard me-1"></i> مدخل بيانات</span>',
                                    'user' => '<span class="neo-badge neo-badge-info"><i class="fas fa-user me-1"></i> مستخدم</span>'
                                ];
                                echo $roleLabels[$user['role']] ?? '<span class="neo-badge neo-badge-secondary"><i class="fas fa-user me-1"></i> ' . $user['role'] . '</span>';
                                ?>
                            </td>
                            <td>
                                <?php 
                                $statusLabels = [
                                    'active' => '<span class="status-badge status-active">نشط</span>',
                                    'inactive' => '<span class="status-badge status-pending">غير نشط</span>',
                                    'banned' => '<span class="status-badge status-inactive">محظور</span>'
                                ];
                                echo $statusLabels[$user['status']] ?? '<span class="status-badge">' . $user['status'] . '</span>';
                                ?>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="/admin/users/view/<?php echo $user['id']; ?>" class="neo-btn neo-btn-sm neo-btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/users/edit/<?php echo $user['id']; ?>" class="neo-btn neo-btn-sm neo-btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="neo-btn neo-btn-sm neo-btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id']; ?>" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content neo-card">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $user['id']; ?>">تأكيد الحذف</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                هل أنت متأكد من حذف المستخدم <strong><?php echo htmlspecialchars($user['username']); ?></strong>؟
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="neo-btn neo-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                <a href="/admin/users/delete/<?php echo $user['id']; ?>" class="neo-btn neo-btn-danger">حذف</a>
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