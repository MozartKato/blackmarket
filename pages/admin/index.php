<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] == false) {
    header("Location: /pages/login");
    exit();
}

require __DIR__ . "../../../includes/connectdb.php";

$adminRole = $_SESSION['admin_role'];

// Fungsi untuk mendapatkan data admin dan customer
function getAdmins($db)
{
    return $db->query("SELECT * FROM admins")->fetchAll();
}

function getCustomers($db)
{
    return $db->query("SELECT * FROM customers")->fetchAll();
}

$admins = getAdmins($database);
$customers = getCustomers($database);

include __DIR__ . "../../../includes/header.php";
?>
<div class="admin-menu-container">
    <h1>Admin Menu</h1>
    <div class="admin-customer-container">
        <?php if ($adminRole == 'superadmin'): ?>
            <div class="admin-container">
                <h2>Manage Admins</h2>
                <button class="action-button" onclick="addAdmin()">Tambah Admin</button>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><?= $admin['Id'] ?></td>
                                <td><?= $admin['Name'] ?></td>
                                <td><?= $admin['Email'] ?></td>
                                <td><?= $admin['role'] ?></td>
                                <td>
                                    <button class="action-button" onclick="editAdmin(<?= $admin['Id'] ?>)">Edit</button>
                                    <button class="action-button" onclick="deleteAdmin(<?= $admin['Id'] ?>)">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="customer-container">
            <h2>Manage Customers</h2>
            <button class="action-button" onclick="addCustomer()">Tambah Customer</button>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>No Telepon</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= $customer['Id'] ?></td>
                            <td><?= $customer['Name'] ?></td>
                            <td><?= $customer['Email'] ?></td>
                            <td><?= $customer['No_telepon'] ?></td>
                            <td>
                                <button class="action-button" onclick="editCustomer(<?= $customer['Id'] ?>)">Edit</button>
                                <button class="action-button" onclick="deleteCustomer(<?= $customer['Id'] ?>)">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include __DIR__ . "../../../includes/footer.php";
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function addAdmin() {
        Swal.fire({
            title: 'Add Admin',
            html: `
                <input id="admin-name" class="swal2-input" placeholder="Name">
                <input id="admin-email" class="swal2-input" placeholder="Email">
                <input id="admin-password" type="password" class="swal2-input" placeholder="Password">
                <input id="admin-role" class="swal2-input" placeholder="Role">
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Add',
            preConfirm: () => {
                const name = document.getElementById('admin-name').value;
                const email = document.getElementById('admin-email').value;
                const password = document.getElementById('admin-password').value;
                const role =document.getElementById('admin-role').value;

                if (!name || !email || !password || !role) {
                    Swal.showValidationMessage(`Please enter valid values.`);
                }

                return {
                    name: name,
                    email: email,
                    password: password,
                    role: role
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const {
                    name,
                    email,
                    password,
                    role
                } = result.value;

                fetch('add_admin.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            email: email,
                            password: password,
                            role: role
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire("Added!", "Admin has been added.", "success");
                            location.reload();
                        } else {
                            Swal.fire("Error!", "Failed to add admin.", "error");
                        }
                    });
            }
        });
    }

    function editAdmin(adminId) {
        // Ambil data admin
        fetch('get_admin.php?id=' + adminId)
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: 'Edit Admin',
                    html: `
                        <input id="admin-name" class="swal2-input" placeholder="Name" value="${data.Name}">
                        <input id="admin-email" class="swal2-input" placeholder="Email" value="${data.Email}">
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    preConfirm: () => {
                        const name = document.getElementById('admin-name').value;
                        const email = document.getElementById('admin-email').value;

                        if (!name || !email) {
                            Swal.showValidationMessage(`Please enter valid values.`);
                        }

                        return {
                            id: adminId,
                            name: name,
                            email: email
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const {
                            id,
                            name,
                            email
                        } = result.value;

                        fetch('edit_admin.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: id,
                                    name: name,
                                    email: email
                                })
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire("Updated!", "Admin has been updated.", "success");
                                    location.reload();
                                } else {
                                    Swal.fire("Error!", "Failed to update admin.", "error");
                                }
                            });
                    }
                });
            });
    }

    function deleteAdmin(adminId) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_admin.php?id=' + adminId, {
                        method: 'POST'
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire("Deleted!", "Admin has been deleted.", "success");
                            location.reload();
                        } else {
                            Swal.fire("Error!", "Failed to delete admin.", "error");
                        }
                    });
            }
        });
    }

    function addCustomer() {
        Swal.fire({
            title: 'Add Customer',
            html: `
                <input id="customer-name" class="swal2-input" placeholder="Name">
                <input id="customer-email" class="swal2-input" placeholder="Email">
                <input id="customer-phone" class="swal2-input" placeholder="No Telepon">
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Add',
            preConfirm: () => {
                const name = document.getElementById('customer-name').value;
                const email = document.getElementById('customer-email').value;
                const phone = document.getElementById('customer-phone').value;

                if (!name || !email || !phone) {
                    Swal.showValidationMessage(`Please enter valid values.`);
                }

                return {
                    name: name,
                    email: email,
                    phone: phone
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const {
                    name,
                    email,
                    phone
                } = result.value;

                fetch('add_customer.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            email: email,
                            phone: phone
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire("Added!", "Customer has been added.", "success");
                            location.reload();
                        } else {
                            Swal.fire("Error!", "Failed to add customer.", "error");
                        }
                    });
            }
        });
    }

    function editCustomer(customerId) {
        // Ambil data customer
        fetch('get_customer.php?id=' + customerId)
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: 'Edit Customer',
                    html: `
                        <input id="customer-name" class="swal2-input" placeholder="Name" value="${data.Name}">
                        <input id="customer-email" class="swal2-input" placeholder="Email" value="${data.Email}">
                        <input id="customer-phone" class="swal2-input" placeholder="No Telepon" value="${data.No_telepon}">
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    preConfirm: () => {
                        const name = document.getElementById('customer-name').value;
                        const email = document.getElementById('customer-email').value;
                        const phone = document.getElementById('customer-phone').value;

                        if (!name || !email || !phone) {
                            Swal.showValidationMessage(`Please enter valid values.`);
                        }

                        return {
                            id: customerId,
                            name: name,
                            email: email,
                            phone: phone
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const {
                            id,
                            name,
                            email,
                            phone
                        } = result.value;

                        fetch('edit_customer.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: id,
                                    name: name,
                                    email: email,
                                    phone: phone
                                })
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire("Updated!", "Customer has been updated.", "success");
                                    location.reload();
                                } else {
                                    Swal.fire("Error!", "Failed to update customer.", "error");
                                }
                            });
                    }
                });
            });
    }

    function deleteCustomer(customerId) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_customer.php?id=' + customerId, {
                        method: 'POST'
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire("Deleted!", "Customer has been deleted.", "success");
                            location.reload();
                        } else {
                            Swal.fire("Error!", "Failed to delete customer.", "error");
                        }
                    });
            }
        });
    }
</script>