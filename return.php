<?php 
$title = 'Form Pengembalian'; 
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
$rentalDataList = $rentaldata->getAllData(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rentalId = $_POST['rental_id']; 
    $return_date = $_POST['return_date'];
    $late_days = $_POST['late_days'];
    $isDamage = isset($_POST['isDamage']) ? 1 : 0; 
    $damage_description = $isDamage ? $_POST['description_damage'] : null;
    $damage_fee = $isDamage ? $_POST['damage_fee'] : 0;
    $totalDamageFee = $_POST['total_damage_fee'];

    if ($returnObj->createReturn($rentalId, $return_date, $late_days, $isDamage, $damage_description, $damage_fee, $totalDamageFee)) {
        echo "Data successfully inserted!";
        header("Location: return.php");  
        exit;
    } else {
        echo "Error inserting data!";
    }
}
?>

<div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
    <div class="flex flex-col items-start gap-4 md:flex-row md:items-center md:justify-between border-b-2 pb-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Data Pengembalian</h1>
            <p class="mt-1.5 text-sm text-gray-500">
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Iure, recusandae.
            </p>
        </div>
    </div>
    <form id="return-form" action="" method="post">
        <div class="w-full grid grid-cols-1 gap-2 mt-4">
            <div class="mb-2">
                <label for="rental_id" class="block text-base font-medium text-gray-700 mb-2">Select Peminjam</label>
                <select id="rental_id" name="rental_id"
                    class="border border-gray-800 text-gray-900 text-sm rounded-lg block w-full p-2">
                    <option value="" selected>Choose a peminjam</option>
                    <?php
        foreach ($rentalDataList as $rental) {
            echo "<option value=\"{$rental['id']}\">{$rental['fullname']} ({$rental['phone_number']}) - {$rental['car_model']} [{$rental['car_license_plate']}]</option>";
        }
        ?>
                </select>
            </div>
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-x-8">
                <div class="mb-2">
                    <label for="return_date" class="block text-base font-medium text-gray-700">Tanggal
                        Pengembalian</label>
                    <input type="date" name="return_date" id="return_date"
                        class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" required />
                </div>
                <div class="mb-2">
                    <label for="late_days" class="block text-base font-medium text-gray-700">Keterlambatan
                        Hari</label>
                    <input type="number" name="late_days" id="late_days" placeholder="5 hari"
                        class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" required />
                </div>
            </div>
            <div class="mb-2">
                <div class="flex items-center">
                    <input id="isDamage-checkbox" name="isDamage" type="checkbox" value="1"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <label for="isDamage-checkbox" class="ms-2 text-base font-medium text-gray-900">Adanya
                        Kerusakan</label>
                </div>
            </div>
            <div class="mb-2" id="description_damage" style="display:none;">
                <label for="description_damage" class="block text-base font-medium text-gray-700">Kerusakan yang
                    terjadi</label>
                <textarea name="description_damage" id="description_damage"
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" cols="30" rows="5"
                    placeholder="Kerusakan yang terjadi"></textarea>
            </div>
            <div class="mb-2" id="damage_fee-field" style="display:none;">
                <label for="damage_fee" class="block text-base font-medium text-gray-700">Biaya Kerusakan</label>
                <input type="number" name="damage_fee" id="damage_fee" placeholder="Biaya kerusakan"
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
            </div>
            <div class="mb-2">
                <label for="total_damage_fee" class="block text-base font-medium text-gray-700">Total Damage Fee</label>
                <input type="text" id="total_damage_fee" name="total_damage_fee" value="0" readonly
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
            </div>
            <div class="mt-6">
                <button type="button" onclick="confirmData()"
                    class="w-full px-4 py-2 tracking-wide text-white transition-colors duration-300 transform bg-blue-500 rounded-lg hover:bg-blue-400 focus:outline-none focus:bg-blue-400 focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                    Submit
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const keterlambatanField = document.getElementById('late_days');
    const biayaKerusakanField = document.getElementById('damage_fee');
    const kerusakanCheckbox = document.getElementById('isDamage-checkbox');
    const totalDamageFeeField = document.getElementById('total_damage_fee');

    function calculateTotalDamageFee() {
        const keterlambatanHari = parseInt(keterlambatanField.value) || 0;
        const biayaKerusakan = kerusakanCheckbox.checked ? parseFloat(biayaKerusakanField.value) || 0 : 0;

        const lateFeePerDay = 100; // Example late fee per day
        const lateFee = keterlambatanHari * lateFeePerDay;

        const totalFee = lateFee + biayaKerusakan;
        totalDamageFeeField.value = totalFee.toFixed(2);
    }

    keterlambatanField.addEventListener('input', calculateTotalDamageFee);
    biayaKerusakanField.addEventListener('input', calculateTotalDamageFee);
    kerusakanCheckbox.addEventListener('change', function () {
        const kerusakanFields = document.getElementById('description_damage');
        const biayaKerusakanField = document.getElementById('damage_fee-field');
        kerusakanFields.style.display = this.checked ? 'block' : 'none';
        biayaKerusakanField.style.display = this.checked ? 'block' : 'none';
        calculateTotalDamageFee();
    });

    function confirmData() {
        const confirmation = confirm("Are you sure you want to create the data?");
        if (confirmation) {
            document.getElementById("return-form").submit();
        }
    }
</script>


<?php
$content = ob_get_clean();
include 'template/layout.php';
?>