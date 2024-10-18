<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-center">User Form</h1>
        <form id="userForm" enctype="multipart/form-data" class="border p-4 rounded bg-light">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" placeholder="Name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="Phone" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" placeholder="Description"></textarea>
            </div>
            <div class="form-group">
                <label for="role_id">Role</label>
                <select name="role_id" class="form-control" required>
                    <option value="">Select Role</option>
                    @foreach(\App\Models\Role::all() as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" name="profile_image" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <div id="errorMessages" class="mt-3 text-danger"></div>

        <h2 class="mt-5">Users List</h2>
        <table class="table table-striped" id="userTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Description</th>
                    <th>Role</th>
                    <th>Profile Image</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <input type="hidden" value="{{ url('/') }}" id="ur">

    <script>

        $(document).ready(function() {
         var mainURL = $("#ur").val();

         $('#userForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var saveData = $.ajax({
                url: mainURL + '/api/users',

                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    loadUsers();
                    $('#userForm')[0].reset();
                    alert(response.message);
                },
                error: function(xhr) {
                    $('#errorMessages').html('');
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        $('#errorMessages').append('<p>' + errors[key][0] + '</p>');
                    }
                }
            });
        });

         function loadUsers() {

               $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: mainURL + '/api/users',
                type: 'GET',
                success: function(users) {
                    const userTableBody = $('#userTable tbody');
                    userTableBody.empty();
                    users.forEach(user => {
                     userTableBody.append(`
                        <tr>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.phone}</td>
                        <td>${user.description}</td>
                        <td>${user.role.name}</td>
                        <td><img src="${mainURL}/storage/app/public/${user.profile_image}" width="50" class="img-thumbnail"></td>
                        </tr>
                        `);

                 });
                }
            });
        }

    // Load users on page load
        loadUsers();
    });
</script>

</body>
</html>
