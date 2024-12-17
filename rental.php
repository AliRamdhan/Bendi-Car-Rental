<?php
$title = 'Form Penyewaan';
ob_start();


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'db/connect.php';
require_once 'api/Rent.php';
require_once 'api/Car.php';

$database = new Database();
$rent = new Rent($database);
$car = new Car($database);
$cars = $car->getAllCars();

$total_payment = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fullname = $_POST['fullname'] ?? '';
    $kartu_type = $_POST['kartu_type'] ?? '';
    $identity_number = $_POST['identity_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $car_id = $_POST['car_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $use_driver = isset($_POST['use_driver']) ? 1 : 0;
    $created_by = $_SESSION['user']['id'] ?? null;

    if (empty($fullname) || empty($kartu_type) || empty($identity_number) || empty($car_id) || empty($start_date) || empty($end_date)) {
        $errorMessage = "All fields are required.";
    } else {;
        $query = "SELECT price, driver_price FROM cars WHERE id = :car_id LIMIT 1";
        $stmt = $database->getConnection()->prepare($query);
        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
        $stmt->execute();

        $carData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($carData) {
            $car_price = $carData['price'];
            $driver_price = $carData['driver_price'];;
            $start_date_obj = new DateTime($start_date);
            $end_date_obj = new DateTime($end_date);
            $interval = $start_date_obj->diff($end_date_obj);
            $days = $interval->days;;
            $total_payment = $car_price * $days;

            if ($use_driver) {
                $total_payment += $driver_price * $days;
            };
            $result = $rent->createRental($fullname, $kartu_type, $identity_number, $address, $phone_number, $car_id, $start_date, $end_date, $use_driver, $created_by, $total_payment);
            if ($result === true) {
                header("Location: rental.php");
                exit;
            } else {
                $errorMessage = $result;
            }
        } else {
            $errorMessage = "Car not found.";
        }
    }
}
?>

<!-- header -->
<div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
    <div class="flex flex-col items-start gap-4 md:flex-row md:items-center md:justify-between border-b-2 pb-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Data Rental</h1>
            <p class="mt-1.5 text-sm text-gray-500">
                Data rental memuat informasi terkait penyewaan barang atau jasa yang diberikan.
            </p>
        </div>
    </div>
    <form id="rental-form" action="" method="post">
        <div class="w-full grid grid-cols-1 gap-2 mt-4">
            <div class="mb-2">
                <label for="fullname" class="block text-base font-medium text-gray-700"> Name Penyewa </label>
                <input type="text" id="fullname" name="fullname" placeholder="John Doe"
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
            </div>
            <div class="mb-2">
                <label for="tipe_kartu" class="block text-base font-medium text-gray-700">Select Kartu</label>
                <select id="tipe_kartu" name="kartu_type"
                    class="border border-gray-800 text-gray-900 text-sm rounded-lg block w-full p-2">
                    <option selected>Choose a kartu</option>
                    <option value="KTP">KTP</option>
                    <option value="SIM">SIM</option>
                </select>
            </div>
            <div class="mb-2">
                <label for="identity_number" class="block text-base font-medium text-gray-700"> Nomor Identitas </label>
                <input type="text" id="identity_number" name="identity_number" placeholder="1234567890"
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
            </div>
            <div class="mb-2">
                <label for="address" class="block text-base font-medium text-gray-700"> Address </label>
                <input type="text" id="address" name="address" placeholder="1234567890"
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
            </div>
            <div class="mb-2">
                <label for="phone_number" class="block text-base font-medium text-gray-700"> phone_number </label>
                <input type="text" id="phone_number" name="phone_number" placeholder="1234567890"
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
            </div>
            <div class="mb-2">
                <label for="car_id" class="block text-base font-medium text-gray-700">Select Mobil</label>
                <select id="car_id" name="car_id"
                    class="border border-gray-800 text-gray-900 text-sm rounded-lg block w-full p-2">
                    <option selected>Choose a car</option>
                    <?php foreach ($cars as $car) : ?>
                        <option value="<?= $car['id']; ?>" data-price="<?= $car['price']; ?>"
                            data-driver-price="<?= $car['driver_price']; ?>">
                            <?= $car['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            </div>
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-x-8">
                <div class="mb-2">
                    <label for="start_date" class="block text-base font-medium text-gray-700"> Start Date </label>
                    <input type="date" id="start_date" name="start_date"
                        class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
                </div>
                <div class="mb-2">
                    <label for="end_date" class="block text-base font-medium text-gray-700"> End Date </label>
                    <input type="date" id="end_date" name="end_date"
                        class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
                </div>
            </div>
            <div class="mb-2">
                <div class="flex items-center">
                    <input id="use_driver" name="use_driver" type="checkbox"
                        class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500" />
                    <label for="use_driver" class="ms-2 text-base font-medium text-gray-900">Gunakan Supir</label>
                </div>
            </div>
            <!-- Display Error Message -->
            <?php if (isset($errorMessage)): ?>
                <div class="mt-4 text-red-500">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <div class="mb-2">
                <label for="total_payment" class="block text-base font-medium text-gray-700"> Total Payment </label>
                <input type="text" id="total_payment" name="total_payment" value="<?= $total_payment ?>" readonly
                    class="mt-1 p-2 w-full rounded-md border border-gray-800 shadow-sm sm:text-sm" />
            </div>

            <div class="mt-6">
                <button type="button" onclick="confirmData()"
                    class="w-full px-4 py-2 tracking-wide text-white transition-colors duration-300 transform bg-green-500 rounded-lg hover:bg-green-400">
                    Submit
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function confirmData() {
        const confirmation = confirm("Are you sure you want to create the data?");
        if (confirmation) {
            ;
            document.getElementById("rental-form").submit();
        }
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const carDropdown = document.getElementById("car_id");
        const startDateInput = document.getElementById("start_date");
        const endDateInput = document.getElementById("end_date");
        const useDriverCheckbox = document.getElementById("use_driver");
        const totalPaymentField = document.getElementById("total_payment");;

        function calculateTotalPayment() {
            const selectedCar = carDropdown.options[carDropdown.selectedIndex];
            const carPrice = parseFloat(selectedCar.getAttribute("data-price")) || 0;
            const driverPrice = parseFloat(selectedCar.getAttribute("data-driver-price")) || 0;

            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            ;
            if (isNaN(startDate.getTime()) || isNaN(endDate.getTime()) || startDate > endDate) {
                totalPaymentField.value = `Rp. ${carPrice}`;
                return;
            }

            ;
            const timeDiff = endDate - startDate;
            const days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

            ;
            let totalPayment = carPrice * days;

            if (useDriverCheckbox.checked) {
                totalPayment += driverPrice * days;
            }

            ;
            totalPaymentField.value = totalPayment.toLocaleString("id-ID", {
                style: "currency",
                currency: "IDR"
            });
        };
        carDropdown.addEventListener("change", calculateTotalPayment);
        startDateInput.addEventListener("change", calculateTotalPayment);
        endDateInput.addEventListener("change", calculateTotalPayment);
        useDriverCheckbox.addEventListener("change", calculateTotalPayment);
    });
</script>


<?php
$content = ob_get_clean();
include 'template/layout.php';
?>