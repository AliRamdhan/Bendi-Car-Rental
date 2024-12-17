<?php 
$title = 'Laporan Rental'; 
ob_start(); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: index.php"); 
    exit;
}

require_once 'db/connect.php';
require_once 'api/Return.php';
require_once 'api/Rent.php';
require_once 'api/Customer.php';

$database = new Database();
$returnObj = new Returns($database);
$rentaldata = new Rent($database);
$rentalDataList = $rentaldata->getAllDataReport(); 
?>

<!-- header -->
<div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
    <div class="flex justify-between items-center">
        <div class="flex flex-col items-start gap-4 md:flex-row md:items-center md:justify-between border-b-2 pb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Data Laporan</h1>

                <p class="mt-1.5 text-sm text-gray-500">
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit. Iure, recusandae.
                </p>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="mt-16">
        <table id="dataTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                        Penyewa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobil
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        Pinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        Pembayaran</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Dummy data rows -->
                <?php
                if (!empty($rentalDataList)) {
                    $no = 1;
                    foreach ($rentalDataList as $row) {
                        echo "<tr>
                                <td class='px-6 py-4 whitespace-nowrap'>{$no}</td>
                                <td class='px-6 py-4 whitespace-nowrap'>{$row['customer_name']}</td>
                                <td class='px-6 py-4 whitespace-nowrap'>{$row['car_model']}</td>
                                <td class='px-6 py-4 whitespace-nowrap'>{$row['start_date']}</td>
                                <td class='px-6 py-4 whitespace-nowrap'>{$row['return_date']}</td>
                                <td class='px-6 py-4 whitespace-nowrap'>Rp " . number_format($row['total_payment'], 0, ',', '.') . "</td>
                            </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr>
                            <td colspan='6' class='px-6 py-4 whitespace-nowrap text-center'>Data tidak ditemukan</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            responsive: true,
            pageLength: 10,
        });
    });
</script>

<?php
$content = ob_get_clean();
include 'template/layout.php';
?>