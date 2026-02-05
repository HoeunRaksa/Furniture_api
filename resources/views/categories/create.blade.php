<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Living Room" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
