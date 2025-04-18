<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">إضافة دورة جديدة</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses">الدورات</a></li>
                    <li class="breadcrumb-item active">إضافة دورة</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">معلومات الدورة</h5>
        </div>
        <div class="card-body">
            <?php
            // Display errors if any
            if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                echo '<div class="alert alert-danger">';
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
            
            <form action="/admin/courses/store" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الدورة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug" class="form-label">الرابط المختصر</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($form_data['slug'] ?? ''); ?>">
                            <div class="form-text">اتركه فارغًا ليتم إنشاؤه تلقائيًا من العنوان. يجب أن يحتوي على أحرف صغيرة وأرقام وشرطات فقط.</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="company_url" class="form-label">رابط الشركة</label>
                    <input type="url" class="form-control" id="company_url" name="company_url" value="<?php echo htmlspecialchars($form_data['company_url'] ?? ''); ?>">
                    <div class="form-text">أدخل رابط موقع الشركة المرتبطة بالدورة (مثال: cisco.com لدورة CCNA)</div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">وصف الدورة <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="admin_id" class="form-label">المسؤول <span class="text-danger">*</span></label>
                            <select class="form-select" id="admin_id" name="admin_id" required>
                                <option value="">اختر المسؤول</option>
                                <?php foreach ($instructors as $instructor): ?>
                                <option value="<?php echo $instructor['id']; ?>" <?php echo (isset($form_data['admin_id']) && $form_data['admin_id'] == $instructor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($instructor['name'] . ' (' . $instructor['username'] . ')'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" <?php echo (isset($form_data['status']) && $form_data['status'] === 'draft') ? 'selected' : ''; ?>>مسودة</option>
                                <option value="published" <?php echo (isset($form_data['status']) && $form_data['status'] === 'published') ? 'selected' : ''; ?>>منشور</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free" <?php echo (isset($form_data['is_free'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_free">
                                    دورة مجانية
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="price" class="form-label">السعر ($)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($form_data['price'] ?? '0.00'); ?>">
                            <div class="form-text">سيتم تجاهل هذا الحقل إذا كانت الدورة مجانية.</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">صورة الدورة</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">الحد الأقصى لحجم الملف: 2 ميجابايت. الأنواع المسموح بها: JPG, JPEG, PNG.</div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/admin/courses" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle price field based on is_free checkbox
        const isFreeCheckbox = document.getElementById('is_free');
        const priceField = document.getElementById('price');
        
        function togglePriceField() {
            if (isFreeCheckbox.checked) {
                priceField.value = '0.00';
                priceField.disabled = true;
            } else {
                priceField.disabled = false;
            }
        }
        
        // Initial toggle
        togglePriceField();
        
        // Toggle on change
        isFreeCheckbox.addEventListener('change', togglePriceField);
        
        // Generate slug from title
        const titleField = document.getElementById('title');
        const slugField = document.getElementById('slug');
        
        titleField.addEventListener('blur', function() {
            if (slugField.value === '' && titleField.value !== '') {
                // Convert to lowercase, replace spaces and special chars with hyphens
                let slug = titleField.value.toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-')     // Replace spaces with hyphens
                    .replace(/-+/g, '-');     // Replace multiple hyphens with single hyphen
                
                slugField.value = slug;
            }
        });
    });
</script>