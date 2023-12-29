<form action="/admin/role/{{ $roles->id }}/update" method="POST" id="formEditRole">
    @csrf
    <div class="modal-body">
        <div class="row">

            <input type="text" name="id" id="id" value="{{ $roles->id }}" hidden>

            <div class="col-lg-12">
                <label class="form-label">Role Name</label>
                <div class="input-icon mb-3">
                    <input type="text" class="form-control @error('name') 
                is-invalid
                @enderror" name="name" id="name" value="{{ $roles->name }}">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-12">
                <label class="form-label">Guard Name</label>
                <div class="input-icon mb-3">
                    <input type="text" class="form-control @error('guard_name') 
                is-invalid
                @enderror" name="guard_name" id="guard_name" value="{{ $roles->guard_name }}">
                    @error('guard_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">Close</a>
        <button type="submit" class="btn btn-primary ms-auto">Create</button>
    </div>
</form>