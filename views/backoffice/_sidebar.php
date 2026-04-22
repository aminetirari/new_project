<?php
// Shared backoffice sidebar. Set $activeNav (e.g. 'posts', 'planning', 'dashboard')
// before including this file to highlight the corresponding link.
$activeNav = $activeNav ?? '';
?>
<aside id="sidebar" class="sidebar">
    <div class="logo-area">
        <a href="index.php" class="d-inline-flex">
            <img src="assets/images/logooo.png" alt="Nutrimind" style="max-height:50px; width:auto;">
        </a>
    </div>
    <ul class="nav flex-column">
        <li class="px-4 py-2"><small class="nav-text">Main</small></li>
        <li>
            <a class="nav-link <?php echo $activeNav === 'dashboard' ? 'active' : ''; ?>" href="index.php">
                <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a class="nav-link <?php echo $activeNav === 'users' ? 'active' : ''; ?>" href="users.php">
                <i class="ti ti-users"></i><span class="nav-text">Users</span>
            </a>
        </li>

        <li class="px-4 py-2"><small class="nav-text">Blog</small></li>
        <li>
            <a class="nav-link <?php echo $activeNav === 'posts' ? 'active' : ''; ?>" href="posts_list.php">
                <i class="ti ti-article"></i><span class="nav-text">Gérer les posts</span>
            </a>
        </li>
        <li>
            <a class="nav-link <?php echo $activeNav === 'posts_create' ? 'active' : ''; ?>" href="post_create.php">
                <i class="ti ti-plus"></i><span class="nav-text">Nouveau post</span>
            </a>
        </li>

        <li class="px-4 py-2"><small class="nav-text">Planning</small></li>
        <li>
            <a class="nav-link <?php echo $activeNav === 'planning' ? 'active' : ''; ?>" href="planning_list.php">
                <i class="ti ti-calendar-event"></i><span class="nav-text">Manage Plans</span>
            </a>
        </li>
        <li>
            <a class="nav-link <?php echo $activeNav === 'planning_create' ? 'active' : ''; ?>" href="planning_create.php">
                <i class="ti ti-plus"></i><span class="nav-text">Create Plan</span>
            </a>
        </li>

        <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
        <li>
            <a class="nav-link" href="#" onclick="logout(); return false;">
                <i class="ti ti-logout"></i><span class="nav-text">Déconnexion</span>
            </a>
        </li>
    </ul>
</aside>
