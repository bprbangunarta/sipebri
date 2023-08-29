<form action="/admin/permission/{{ $permission->id }}/update" method="POST" id="formEdit">
  @csrf
  <div class="modal-body">
    <div class="row">

      <input type="text" name="id" id="id" value="{{ $permission->id }}" hidden>

      <div class="col-lg-12">
        <label class="form-label">Permission Name</label>
        <div class="input-icon mb-3">
          <input type="text" class="form-control @error('name') 
              is-invalid
              @enderror" name="name" id="name" value="{{ $roles->name }}">
          @error('name')
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