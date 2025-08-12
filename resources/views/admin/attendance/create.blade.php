@extends('layouts.admin')

@section('title', 'تسجيل الحضور')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى سجل الحضور
    </a>
    @if(request()->has('group_id'))
    <a href="{{ route('admin.groups.show', request()->group_id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى الحلقة
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تسجيل الحضور</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.attendance.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="branch_id" class="form-label">الفرع <span class="text-danger">*</span></label>
                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                        <option value="">-- اختر الفرع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', request('branch_id')) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="group_id" class="form-label">الحلقة <span class="text-danger">*</span></label>
                    <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id" required>
                        <option value="">-- اختر الحلقة --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id', request('group_id')) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="date" class="form-label">تاريخ الحضور <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div id="students-container" class="mb-4">
                <div class="alert alert-info" id="no-students-message">
                    الرجاء اختيار الفرع والحلقة أولاً لعرض قائمة الطلاب.
                </div>
                
                <div id="students-list" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">قائمة الطلاب</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success mark-all" data-status="present">
                                        <i class="fas fa-check me-1"></i> تحديد الكل كحاضر
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger mark-all" data-status="absent">
                                        <i class="fas fa-times me-1"></i> تحديد الكل كغائب
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th style="width: 25%">اسم الطالب</th>
                                            <th style="width: 20%">الحالة</th>
                                            <th style="width: 50%">ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody id="students-table-body">
                                        <!-- سيتم ملء هذا الجزء بواسطة JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="teacher_id" class="form-label">المعلم المسؤول <span class="text-danger">*</span></label>
                <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                    <option value="">-- اختر المعلم --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }} {{ $teacher->last_name }}</option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> حفظ سجل الحضور
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // الحصول على طلاب الحلقة
    function getGroupStudents() {
        const groupId = document.getElementById('group_id').value;
        if (!groupId) {
            document.getElementById('no-students-message').style.display = 'block';
            document.getElementById('students-list').style.display = 'none';
            return;
        }
        
        // هنا يمكن إضافة طلب Ajax للحصول على طلاب الحلقة
        // على سبيل المثال:
        fetch(`/admin/groups/${groupId}/students`)
            .then(response => response.json())
            .then(data => {
                if (data.students && data.students.length > 0) {
                    renderStudents(data.students);
                    document.getElementById('no-students-message').style.display = 'none';
                    document.getElementById('students-list').style.display = 'block';
                } else {
                    document.getElementById('no-students-message').textContent = 'لا يوجد طلاب مسجلين في هذه الحلقة.';
                    document.getElementById('no-students-message').style.display = 'block';
                    document.getElementById('students-list').style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching students:', error);
                document.getElementById('no-students-message').textContent = 'حدث خطأ أثناء جلب بيانات الطلاب.';
                document.getElementById('no-students-message').style.display = 'block';
                document.getElementById('students-list').style.display = 'none';
            });
            
    }
    
    // عرض الطلاب في الجدول
    function renderStudents(students) {
        const tableBody = document.getElementById('students-table-body');
        tableBody.innerHTML = '';
        
        students.forEach((student, index) => {
            const row = document.createElement('tr');
            
            // رقم الطالب
            const indexCell = document.createElement('td');
            indexCell.textContent = index + 1;
            row.appendChild(indexCell);
            
            // اسم الطالب
            const nameCell = document.createElement('td');
            nameCell.textContent = `${student.name}`;
            row.appendChild(nameCell);
            
            // حالة الحضور
            const statusCell = document.createElement('td');
            const statusGroup = document.createElement('div');
            statusGroup.className = 'btn-group';
            statusGroup.setAttribute('role', 'group');
            
            const statuses = [
                { value: 'present', text: 'حاضر', class: 'btn-success' },
                { value: 'late', text: 'متأخر', class: 'btn-warning' },
                { value: 'excused', text: 'معذور', class: 'btn-info' },
                { value: 'absent', text: 'غائب', class: 'btn-danger' }
            ];
            
            statuses.forEach(status => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `btn btn-sm ${status.class}`;
                button.textContent = status.text;
                button.onclick = function() {
                    // إزالة الفئة النشطة من جميع الأزرار
                    statusGroup.querySelectorAll('button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // تحديد الزر الحالي كنشط
                    button.classList.add('active');
                    
                    // تعيين قيمة حقل الإدخال المخفي
                    document.getElementById(`status_${student.id}`).value = status.value;
                };
                
                // تعيين الحالة الافتراضية كحاضر
                if (status.value === 'present') {
                    button.classList.add('active');
                }
                
                statusGroup.appendChild(button);
            });
            
            // إضافة حقل مخفي لتخزين القيمة
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `students[${student.id}][status]`;
            hiddenInput.id = `status_${student.id}`;
            hiddenInput.value = 'present'; // القيمة الافتراضية
            
            statusCell.appendChild(statusGroup);
            statusCell.appendChild(hiddenInput);
            row.appendChild(statusCell);
            
            // ملاحظات
            const notesCell = document.createElement('td');
            const notesInput = document.createElement('input');
            notesInput.type = 'text';
            notesInput.className = 'form-control';
            notesInput.name = `students[${student.id}][notes]`;
            notesInput.placeholder = 'ملاحظات (اختياري)';
            
            notesCell.appendChild(notesInput);
            row.appendChild(notesCell);
            
            tableBody.appendChild(row);
        });
    }
    
    // تحديث قائمة الحلقات عند تغيير الفرع
    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        const groupSelect = document.getElementById('group_id');
        
        // إعادة تعيين قائمة الحلقات
        groupSelect.innerHTML = '<option value="">-- اختر الحلقة --</option>';
        
        if (branchId) {
            // هنا يمكن إضافة طلب Ajax للحصول على حلقات الفرع
            // على سبيل المثال:
            
            fetch(`/admin/branches/${branchId}/groups`)
                .then(response => response.json())
                .then(data => {
                    data.groups.forEach(group => {
                        const option = document.createElement('option');
                        option.value = group.id;
                        option.textContent = group.name;
                        groupSelect.appendChild(option);
                    });
                });
            
        }
        
        // إعادة تعيين قائمة الطلاب
        document.getElementById('no-students-message').style.display = 'block';
        document.getElementById('students-list').style.display = 'none';
    });
    
    // الحصول على طلاب الحلقة عند تغيير الحلقة
    document.getElementById('group_id').addEventListener('change', getGroupStudents);
    
    // تحديد جميع الطلاب بحالة معينة
    document.querySelectorAll('.mark-all').forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            const tableBody = document.getElementById('students-table-body');
            const rows = tableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const statusButtons = row.querySelectorAll('.btn-group button');
                const statusInput = row.querySelector('input[type="hidden"]');
                
                // إزالة الفئة النشطة من جميع الأزرار
                statusButtons.forEach(btn => {
                    btn.classList.remove('active');
                    
                    // تحديد الزر المناسب كنشط
                    if (btn.textContent.trim() === getStatusText(status)) {
                        btn.classList.add('active');
                    }
                });
                
                // تعيين قيمة حقل الإدخال المخفي
                if (statusInput) {
                    statusInput.value = status;
                }
            });
        });
    });
    
    // الحصول على النص العربي للحالة
    function getStatusText(status) {
        switch (status) {
            case 'present': return 'حاضر';
            case 'absent': return 'غائب';
            case 'late': return 'متأخر';
            case 'excused': return 'معذور';
            default: return '';
        }
    }
    
    // تحميل الطلاب عند تحميل الصفحة إذا كانت الحلقة محددة
    document.addEventListener('DOMContentLoaded', function() {
        const groupId = document.getElementById('group_id').value;
        if (groupId) {
            getGroupStudents();
        }
    });
</script>
@endsection
