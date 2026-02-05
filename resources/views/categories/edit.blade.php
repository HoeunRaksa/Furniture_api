<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form method="POST">
                @csrf
                <input type="hidden" name="cate_id">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name</label>
                        <input type="text" name="name" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
