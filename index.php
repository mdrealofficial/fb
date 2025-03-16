<?php include 'includes/header.php'; ?>

<div class="hero text-center">
    <h1 class="display-4 mb-4">Facebook Page Poster</h1>
    <p class="lead mb-4">Easily post images and text to your Facebook page with just a few clicks!</p>
    <?php if (!isLoggedIn()): ?>
        <a href="login.php" class="btn btn-light btn-lg">Get Started <i class="fas fa-arrow-right ms-2"></i></a>
    <?php else: ?>
        <a href="dashboard.php" class="btn btn-light btn-lg">Go to Dashboard <i class="fas fa-tachometer-alt ms-2"></i></a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-4 fade-in">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-image fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Post Images</h5>
                <p class="card-text">Upload and share beautiful images directly to your Facebook page.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 fade-in">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-comment fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Add Comments</h5>
                <p class="card-text">Automatically add the first comment to your posts to boost engagement.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 fade-in">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-history fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Track History</h5>
                <p class="card-text">Keep track of all your posts in one convenient dashboard.</p
