@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View Plan Modal
        document.querySelectorAll('.view-plan').forEach(function(button) {
            button.addEventListener('click', function() {
                const student = this.getAttribute('data-student');
                const savingFrom = this.getAttribute('data-saving-from');
                const savingTo = this.getAttribute('data-saving-to');
                const reviewFrom = this.getAttribute('data-review-from');
                const reviewTo = this.getAttribute('data-review-to');
                const month = this.getAttribute('data-month');
                
                document.getElementById('view_student').textContent = student;
                document.getElementById('view_saving_from').textContent = savingFrom;
                document.getElementById('view_saving_to').textContent = savingTo;
                document.getElementById('view_review_from').textContent = reviewFrom;
                document.getElementById('view_review_to').textContent = reviewTo;
                document.getElementById('view_month').textContent = month;
            });
        });
        
        // Edit Plan Modal
        document.querySelectorAll('.edit-plan').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const studentId = this.getAttribute('data-student-id');
                const savingFrom = this.getAttribute('data-saving-from');
                const savingTo = this.getAttribute('data-saving-to');
                const reviewFrom = this.getAttribute('data-review-from');
                const reviewTo = this.getAttribute('data-review-to');
                const month = this.getAttribute('data-month');
                
                document.getElementById('edit_student_id').value = studentId;
                document.getElementById('edit_saving_from').value = savingFrom;
                document.getElementById('edit_saving_to').value = savingTo;
                document.getElementById('edit_review_from').value = reviewFrom;
                document.getElementById('edit_review_to').value = reviewTo;
                document.getElementById('edit_month').value = month;
                
                // Set the form action URL with the correct route for update
                document.getElementById('editPlanForm').action = `{{ url('admin/student_plans') }}/${id}`;
            });
        });
    });
</script>
@endpush
