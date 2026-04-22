<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || 
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect non-admin users to home page
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from themewagon.github.io/inapp/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Apr 2026 13:10:19 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
  <meta charset="UTF-8" />
  <title>InApp Inventory Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logooo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logooo.png">
  <link rel="manifest" href="assets/site.webmanifest">

  <script type="module" crossorigin src="assets/js/main.js"></script>
  <link rel="stylesheet" crossorigin href="assets/css/main.css">
</head>

<body>
  <div id="overlay" class="overlay"></div>
  <!-- TOPBAR -->
  <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm ">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>

    <!-- MOBILE -->
    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
      <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <div>
      <!-- Navbar nav -->
      <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
        <!-- Pages link -->

        <!-- Bell icon -->
        <li>
          <a class="position-relative btn-icon btn-sm btn-light btn rounded-circle" data-bs-toggle="dropdown"
            aria-expanded="false" href="#" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
              class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
              <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
            </svg>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger mt-2 ms-n2">
              2
              <span class="visually-hidden">unread messages</span>
            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0">
            <ul class="list-unstyled p-0 m-0">
              <li class="p-3 border-bottom ">
                <div class="d-flex gap-3">
                  <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-sm rounded-circle" />
                  <div class="flex-grow-1 small">
                    <p class="mb-0">New order received</p>
                    <p class="mb-1">Order #12345 has been placed</p>
                    <div class="text-secondary">5 minutes ago</div>
                  </div>
                </div>
              </li>
              <li class="p-3 border-bottom ">
                <div class="d-flex gap-3">
                  <img src="assets/images/avatar-4.jpg" alt="" class="avatar avatar-sm rounded-circle" />
                  <div class="flex-grow-1 small">
                    <p class="mb-0">New user registered</p>
                    <p class="mb-1">User @john_doe has signed up</p>
                    <div class="text-secondary">30 minutes ago</div>
                  </div>
              </li>

              <li class="p-3 border-bottom">
                <div class="d-flex gap-3">
                  <img src="assets/images/avatar-2.jpg" alt="" class="avatar avatar-sm rounded-circle" />
                  <div class="flex-grow-1 small">
                    <p class="mb-0">Payment confirmed</p>
                    <p class="mb-1">Payment of $299 has been received</p>
                    <div class="text-secondary">1 hour ago</div>
                  </div>
                </div>
              </li>
              <li class="px-4 py-3 text-center">
                <a href="#" class="text-success ">View all notifications</a>
              </li>
            </ul>
          </div>
        </li> 
        <!-- Dropdown -->
        <li class="ms-3 dropdown">
          <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-sm rounded-circle" />
          </a>
          <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 250px; position: absolute; right: 0; top: 100%; margin-top: 0.5rem;" data-popper-placement="bottom-end">
            <div>
              <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-4 py-3">
                <img src="assets/images/avatar-1.jpg" alt="" class="avatar avatar-md rounded-circle" />
                <div>
                  <h5 class="mb-0 small fw-600"><?php echo htmlspecialchars($_SESSION['user_nom']); ?></h5>
                  <p class="mb-0 text-muted small"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                </div>
              </div>
              <div class="p-3 d-flex flex-column gap-2 small lh-lg">
                <a href="profile.php" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">
                  <i class="ti ti-user text-success"></i>
                  <span>Profil</span>
                </a>
                <a href="#" onclick="logout(); return false;" class="text-decoration-none text-body d-flex align-items-center gap-2 px-2 py-2 rounded" style="transition: all 0.2s ease;">
                  <i class="ti ti-logout text-danger"></i>
                  <span>Déconnexion</span>
                </a>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

  </nav>

  <!-- SIDEBAR -->
  <aside id="sidebar" class="sidebar">
    <div class="logo-area">
     <a href="index.php" class="d-inline-flex"><img src="assets/images/logooo.png" alt="Nutrimind" style="max-height: 50px; width: auto;"></a>
    </div>
    <ul class="nav flex-column">
      <li class="px-4 py-2"><small class="nav-text">Main</small></li>
      <li><a class="nav-link active" href="index.php"><i class="ti ti-home"></i><span
            class="nav-text">Dashboard</span></a></li>
      <li><a class="nav-link" href="users.php"><i class="ti ti-users"></i><span
            class="nav-text">Users</span></a></li>
      <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
      <li><a class="nav-link" href="planning_list.php"><i class="ti ti-calendar-event"></i><span
            class="nav-text">Manage Plans</span></a></li>
      <li><a class="nav-link" href="planning_create.php"><i class="ti ti-plus"></i><span
            class="nav-text">Create Plan</span></a></li>
      <li><a class="nav-link" href="inventory.html"><i class="ti ti-box-seam"></i><span
            class="nav-text">Inventory</span></a></li>
      <li><a class="nav-link" href="create-product.html"><i class="ti ti-plus"></i><span class="nav-text">Add
            Product</span></a></li>
    <li><a class="nav-link" href="reports.html"><i class="ti ti-receipt"></i><span class="nav-text">Reports</span></a>
      </li>
    <li><a class="nav-link" href="404-error.html"><i class="ti ti-alert-circle"></i><span class="nav-text">404 Error</span></a>
      </li>
      <li><a class="nav-link" href="docs.html"><i class="ti ti-file-text"></i><span class="nav-text">Docs</span></a></li>


      <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
      <li><a class="nav-link" href="#" onclick="logout(); return false;"><i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span></a>
      </li>
    </ul>

  </aside>

  <!-- MAIN CONTENT -->
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <div class="row ">
        <div class="col-12">
          <div class="mb-6">
            <h1 class="fs-3 mb-1">Tableau de Bord</h1>
            <p>Votre contenu principal va ici…</p>
          </div>
        </div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-lg-3 col-12">

          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">

            <div class="d-flex gap-3 ">
              <div class="icon-shape icon-md bg-success text-white rounded-2">
                <i class="ti ti-report-analytics fs-4"></i>
              </div>
              <div>
                <h2 class="mb-3 fs-6">Total Sales</h2>
                <h3 class="fw-bold mb-0">$25,000</h3>
                <p class="text-success mb-0 small">+5% since last month</p>
              </div>
            </div>
          </div>


        </div>
        <div class="col-lg-3 col-12">

          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">

            <div class="d-flex gap-3 ">
              <div class="icon-shape icon-md bg-success text-white rounded-2">
                <i class="ti ti-repeat fs-4"></i>
              </div>
              <div>
                <h2 class="mb-3 fs-6">Total Purchase</h2>
                <h3 class="fw-bold mb-0">$18,000</h3>
                <p class="text-success mb-0 small">+22% since last month</p>
              </div>
            </div>
          </div>


        </div>
        <div class="col-lg-3 col-12">

          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">

            <div class="d-flex gap-3 ">
              <div class="icon-shape icon-md bg-success text-white rounded-2">
                <i class="ti ti-currency-dollar fs-4"></i>
              </div>
              <div>
                <h2 class="mb-3 fs-6">Total Expenses</h2>
                <h3 class="fw-bold mb-0">$9,000</h3>
                <p class="text-success mb-0 small">+10% since last month</p>
              </div>
            </div>
          </div>


        </div>
        <div class="col-lg-3 col-12">

          <div class="card p-4  bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">

            <div class="d-flex gap-3 ">
              <div class="icon-shape icon-md bg-success text-white rounded-2">
                <i class="ti ti-notes fs-4"></i>
              </div>
              <div>
                <h2 class="mb-3 fs-6">Invoice Due</h2>
                <h3 class="fw-bold mb-0">$25,000</h3>
                <p class="text-success mb-0 small">+35% since last month</p>
              </div>
            </div>
          </div>


        </div>

      </div>
      <div class="row g-3 mb-3">
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                <div>
                  <h3 class="fw-bold h4">$25,458</h3>
                  <span>Total Profit</span>
                </div>
                <div>
                  <i class="ti ti-layers-subtract fs-1 text-success"></i>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center small">
                <div class="text-muted"><span class="text-success">+35%</span> vs Last Month</div>
                <div><a href="#" class="link-success text-decoration-underline">View</a></div>
              </div>
            </div>
          </div>

        </div>
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                <div>
                  <h3 class="fw-bold h4">$45,458</h3>
                  <span>Total Payment Returns</span>
                </div>
                <div>
                  <i class="ti ti-credit-card fs-1 text-danger"></i>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center small">
                <div class="text-muted"><span class="text-danger">-20%</span> vs Last Month</div>
                 <div><a href="#" class="link-primary text-decoration-underline">View</a></div>
              </div>
            </div>
          </div>

        </div>
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                <div>
                  <h3 class="fw-bold h4">$34,458</h3>
                  <span>Total Expenses</span>
                </div>
                <div>
                  <i class="ti ti-cash-banknote fs-1 text-success"></i>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center small">
                <div class="text-muted"><span class="text-success">-20%</span> vs Last Month</div>
                <div><a href="#" class="link-primary text-decoration-underline">View</a></div>
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
              <h3 class="h5 mb-0">Sales vs Purchase</h3>
              <div>
                <select class="form-select form-select-sm">
                  <option selected>This Year</option>
                  <option>This Month</option>
                  <option>This Week</option>
                </select>
              </div>
            </div>
            <div class="card-body p-4">

              <div id="salesPurchaseChart"></div>
            </div>
          </div>
        </div>


        <div class="col-12 col-lg-6">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
              <h3 class="h5 mb-0">Overall Information</h3>
              <div>
                <select class="form-select form-select-sm">
                  <option selected>Last 6 Months</option>
                  <option>This Month</option>
                  <option>This Week</option>
                </select>
              </div>
            </div>
            <div class="card-body p-4">
              <h3 class="h6">Customers Overview</h3>
              <div class="row align-items-center">
                <div class="col-sm-6">
                  <div id="customerChart">

                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="row">
                    <div class="col-6 border-end">
                      <div class="text-center ">
                        <h2 class="mb-1">5.5K</h2>
                        <p class="text-success mb-2">First Time</p>
                        <span class="badge bg-success"><i class="ti ti-arrow-up-left me-1"></i>25%</span>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <h2 class="mb-1">3.5K</h2>
                        <p class="text-success mb-2">Return</p>
                        <span class="badge bg-success badge-xs d-inline-flex align-items-center"><i
                            class="ti ti-arrow-up-left me-1"></i>21%</span>
                      </div>
                    </div>
                  </div>
                </div>


              </div>
              <div class="row text-center border-top mt-4 pt-4">
                <div class="col-4 border-end">
                  <h3 class="fw-bold mb-2">6987</h3>
                  <small class="text-secondary">Suppliers</small>
                </div>
                <div class="col-4 border-end">
                  <h3 class="fw-bold mb-2">4896</h3>
                  <small class="text-secondary">Customers</small>
                </div>
                <div class="col-4">
                  <h3 class="fw-bold mb-2">487</h3>
                  <small class="text-secondary">Orders</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-3">

        <!-- CARD 1 — Top Selling Products -->
        <div class="col-lg-4">
          <div class="card  h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
              <h4 class="mb-0 h5">Top Selling Products</h4>
              <button class="btn btn-sm btn-outline-secondary">
                <i class="ti ti-calendar"></i> Today
              </button>
            </div>

            <ul class="list-group list-group-flush">

              <!-- item -->
              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-2.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Wireless Earphones</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">$89 </small>
                    <small>•</small>
                    <small>1,250 Units</small>
                  </div>
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger">18%</span>
              </li>

              <!-- repeat -->
              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-1.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Gaming Joy Stick</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">$49 </small>
                    <small>•</small>
                    <small>5,420 Units</small>
                  </div>

                </div>
                <span class="badge bg-success-subtle text-success border border-success">32%</span>
              </li>

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-3.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Smart Watch Pro</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">$98 </small>
                    <small>•</small>
                    <small>862 Units</small>
                  </div>

                </div>
                <span class="badge bg-success-subtle text-success border border-success">22%</span>
              </li>
              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-4.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">USB-C Fast Charger</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">$35 </small>
                    <small>•</small>
                    <small>3,200 Units</small>
                  </div>

                </div>
                <span class="badge bg-success-subtle text-success border border-success">28%</span>
              </li>
              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-5.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Portable Bluetooth Speaker</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">$65 </small>
                    <small>•</small>
                    <small>2,890 Units</small>
                  </div>

                </div>
                <span class="badge bg-success-subtle text-success border border-success">25%</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- CARD 2 — Low Stock Products -->
        <div class="col-lg-4">
          <div class="card  h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
              <div class="d-flex align-items-center">

                <h4 class="mb-0 h5">Low Stock Products</h4>
              </div>
              <a href="#" class="small text-success text-decoration-underline">View All</a>
            </div>

            <ul class="list-group list-group-flush">

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-8.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Wireless Headphones</p>
                  <small>ID: #554433</small>
                </div>
                <div class="d-flex flex-column gap-0 align-items-center">
                  <span class="fw-semibold text-success">06</span>
                  <small class="text-muted">In Stock</small>
                </div>
              </li>

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-4.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">USB-C Cable Pack</p>
                  <small>ID: #887766</small>
                </div>
                <div class="d-flex flex-column gap-0 align-items-center">
                  <span class="fw-semibold text-success">09</span>
                  <small class="text-muted">In Stock</small>
                </div>
              </li>

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-10.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Phone Screen Protector</p>
                  <small>ID: #332211</small>
                </div>
                <div class="d-flex flex-column gap-0 align-items-center">
                  <span class="fw-semibold text-success">03</span>
                  <small class="text-muted">In Stock</small>
                </div>
              </li>
              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-4.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Portable Charger 20000mAh</p>
                  <small>ID: #998877</small>
                </div>
                <div class="d-flex flex-column gap-0 align-items-center">
                  <span class="fw-semibold text-success">07</span>
                  <small class="text-muted">In Stock</small>
                </div>
              </li>
              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-6.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Mechanical Keyboard RGB</p>
                  <small>ID: #665544</small>
                </div>
                <div class="d-flex flex-column gap-0 align-items-center">
                  <span class="fw-semibold text-success">02</span>
                  <small class="text-muted">In Stock</small>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- CARD 3 — Recent Sales -->
        <div class="col-lg-4">
          <div class="card  h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
              <h4 class="mb-0 h5">Recent Sales</h4>
              <button class="btn btn-sm btn-outline-secondary">
                <i class="ti ti-calendar-event"></i> Weekly
              </button>
            </div>

            <ul class="list-group list-group-flush">

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-7.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">MacBook Pro 16"</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">Computers </small>
                    <small>•</small>
                    <small>2,$2,499</small>
                  </div>

                </div>
                <span class="badge bg-success-subtle text-success">Completed</span>
              </li>

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-9.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">AirPods Pro Max</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">Audio </small>
                    <small>•</small>
                    <small>$549</small>
                  </div>

                </div>
                <span class="badge bg-success-subtle text-success">Processing</span>
              </li>

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-8.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">iPad Air 11"</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">Tablets </small>
                    <small>•</small>
                    <small>$799</small>
                  </div>
                </div>
                <span class="badge bg-success-subtle text-success">Completed</span>
              </li>

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-3.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Apple Watch Ultra</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">Wearables </small>
                    <small>•</small>
                    <small>$799</small>
                  </div>
                </div>
                <span class="badge bg-success-subtle text-success">Pending</span>
              </li>

              <li class="list-group-item d-flex align-items-center gap-3">
                <img src="assets/images/product-6.png" class="rounded" width="48">
                <div class="flex-grow-1">
                  <p class="mb-1">Magic Keyboard</p>
                  <div class="d-flex align-items-center gap-2 text-muted">
                    <small class="fw-semibold">Accessories </small>
                    <small>•</small>
                    <small>$299</small>
                  </div>

                </div>
                <span class="badge bg-danger-subtle text-danger">Cancelled</span>
              </li>
            </ul>
          </div>
        </div>

      </div>
      <div class="row">
        <div class="col-12">
<footer class="text-center py-2 mt-6 text-secondary ">
        <p class="mb-0">Copyright © 2026 InApp Inventory Dashboard. Developed by <a href="https://codescandy.com/" target="_blank" class="text-success">CodesCandy</a> • Distributed by <a href="https://themewagon.com/" target="_blank" class="text-success">ThemeWagon</a> </p>
      </footer>
        </div>

      </div>

    </div>
  </main>

  <!-- Bootstrap JS -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Logout function
    function showLogoutModal() {
        const modal = document.createElement('div');
        modal.className = 'logout-modal-overlay';
        modal.innerHTML = `
            <div class="logout-modal">
                <div class="logout-modal-content">
                    <h3>Confirmation de Déconnexion</h3>
                    <p>Êtes-vous sûr de vouloir vous déconnecter?</p>
                    <div class="logout-modal-buttons">
                        <button class="logout-btn-cancel">Annuler</button>
                        <button class="logout-btn-confirm">Déconnexion</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const cancelBtn = modal.querySelector('.logout-btn-cancel');
        const confirmBtn = modal.querySelector('.logout-btn-confirm');
        
        const closeModal = () => modal.remove();
        
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        confirmBtn.addEventListener('click', () => {
            fetch('../../controllers/UserController.php?action=logout')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../index.php';
                    } else {
                        alert('Error during logout');
                    }
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    alert('An error occurred during logout');
                });
        });
    }
    
    function logout() {
        showLogoutModal();
    }

    // Initialize dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        const profileDropdownToggle = document.querySelector('.dropdown button[data-bs-toggle="dropdown"], .dropdown a[data-bs-toggle="dropdown"]');
        if (profileDropdownToggle) {
            profileDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                    dropdown.classList.toggle('show');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdownMenus = document.querySelectorAll('.dropdown-menu.show');
            dropdownMenus.forEach(menu => {
                const dropdownContainer = menu.closest('.dropdown');
                if (dropdownContainer && !dropdownContainer.contains(e.target)) {
                    menu.classList.remove('show');
                }
            });
        });
    });
  </script>

  <style>
    .logout-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .logout-modal {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    }
    
    .logout-modal-content {
        padding: 30px;
        min-width: 400px;
        text-align: center;
    }
    
    .logout-modal-content h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 22px;
    }
    
    .logout-modal-content p {
        margin: 0 0 30px 0;
        color: #666;
        font-size: 16px;
    }
    
    .logout-modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    
    .logout-btn-cancel, .logout-btn-confirm {
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .logout-btn-cancel {
        background-color: #e0e0e0;
        color: #333;
    }
    
    .logout-btn-cancel:hover {
        background-color: #d0d0d0;
    }
    
    .logout-btn-confirm {
        background-color: #dc3545;
        color: white;
    }
    
    .logout-btn-confirm:hover {
        background-color: #c82333;
    }
    
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Dropdown menu positioning to keep it on screen */
    .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        margin-top: 0.5rem !important;
        z-index: 1050 !important;
        max-height: 90vh;
        overflow-y: auto;
    }

    .dropdown-menu.show {
        display: block !important;
    }

    .dropdown {
        position: relative;
    }
  </style>

</body>


<!-- Mirrored from themewagon.github.io/inapp/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Apr 2026 13:10:27 GMT -->
</html>