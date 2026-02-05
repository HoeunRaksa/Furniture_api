<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = "{{ session('success') }}";
        const errorMessage = "{{ session('error') }}";
        
        if (successMessage) {
            toastr.success(successMessage);
        }
        if (errorMessage) {
            toastr.error(errorMessage);
        }
    });
</script>
