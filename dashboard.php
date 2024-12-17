<?php 
$title = 'Dashboard'; 
ob_start(); 

// Start session and check login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: index.php"); 
    exit;
}

$username = $_SESSION['user']['username'];

// Include required files
require_once 'db/connect.php';
require_once 'api/Return.php';
require_once 'api/Rent.php';

// Initialize objects
$database = new Database();
$returnObj = new Returns($database);
$rentalDataObj = new Rent($database);

$rentalDataList = $rentalDataObj->getAllData();
$rentalReturnDataList = $returnObj->getAllReturns();

$totalRental = count($rentalDataList);
$totalReturned = count($rentalReturnDataList);
$totalCustomers = $totalRental + $totalReturned;
?>

<!-- Header -->
<header class="bg-white">
    <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="flex flex-col items-start gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-4xl">Halo,
                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($username); ?></span>!
                </h1>
                <p class="mt-1.5 text-lg text-gray-500">Welcome back to rental</p>
            </div>

            <div class="flex items-center gap-4">
                <a href="report.php">
                    <button
                        class="inline-flex items-center justify-center gap-1.5 rounded border border-gray-200 bg-white px-5 py-3 text-gray-900 transition hover:text-gray-700 focus:outline-none focus:ring"
                        type="button">
                        <span class="text-sm font-medium"> View Report </span>

                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </button>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Statistics -->
<div class="px-4 py-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 lg:py-20">
    <div class="grid grid-cols-2 row-gap-8 md:grid-cols-3 gap-8">
        <div class="text-center bg-white h-32 shadow-xl flex flex-col justify-center border">
            <h6 class="text-3xl font-bold text-green-600"><?php echo $totalReturned; ?></h6>
            <p class="font-bold">Total yang sudah dikembalikan</p>
        </div>
        <div class="text-center bg-white h-32 shadow-xl flex flex-col justify-center border">
            <h6 class="text-3xl font-bold text-green-600"><?php echo $totalRental; ?></h6>
            <p class="font-bold">Total Rental</p>
        </div>
        <div class="text-center bg-white h-32 shadow-xl flex flex-col justify-center border">
            <h6 class="text-3xl font-bold text-green-600"><?php echo $totalCustomers; ?></h6>
            <p class="font-bold">Total Pelanggan</p>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="w-full flex flex-col justify-center items-center h-24 px-8">
    <h2 class="text-5xl font-bold drop-shadow-lg">BENDI CAR RENTAL</h2>
    <p class="text-xl text-gray-500">Tempat rental aman dan terpercaya</p>
</div>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
                label: 'Sales Over Time',
                data: [15, 20, 18, 25, 30, 35],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php
$content = ob_get_clean(); 
include 'template/layout.php'; 
?>