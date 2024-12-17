<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db/connect.php';
require_once 'api/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginIdentifier = $_POST['email'];
    $password = $_POST['password'];

    try {
        $database = new Database();
        $user = new User($database);

        $loggedInUser = $user->login($loginIdentifier, $password);

        $_SESSION['user'] = [
            'id' => $loggedInUser["id"],
            'username' => $loggedInUser["username"]
        ];

        if (isset($_SESSION['user'])) {
            echo "Sesi tersimpan: ";
            echo "ID: " . $_SESSION['user']['id'] . ", Username: " . $_SESSION['user']['username'];
        } else {
            echo "Sesi gagal disimpan.";
        }

        header("Location: dashboard.php");
        exit;
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<!-- HTML Form Login -->
<div class="bg-white">
    <div class="flex justify-center h-screen">
        <div class="hidden bg-cover lg:block lg:w-2/3"
            style="background-image: url(./assets/home.png)">
            <div class="flex items-center h-full px-20 bg-gray-800 bg-opacity-40">
                <div>
                    <h2 class="text-xl font-bold text-white sm:text-6xl">Bendi Car Rental</h2>
                    <p class="max-w-xl mt-3 text-2xl text-gray-100">
                        Rental aman dan terpercaya
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center w-full max-w-md px-6 mx-auto lg:w-2/6">
            <div class="flex-1">
                <div class="text-center">
                    <div class="flex justify-center mx-auto">
                        <!-- <img class="w-auto h-7 sm:h-8" src="https://merakiui.com/images/logo.svg" alt=""> -->
                        <div class="w-16 h-16 flex justify-center items-center border border-green-800 rounded-full">
                            <p class="text-green-800 font-black text-2xl">BEN</p>
                        </div>
                    </div>

                    <p class="mt-3 text-gray-500">Sign in to access your account</p>
                </div>

                <div class="mt-8">
                    <!-- Login Form -->
                    <form method="POST" action="">
                        <!-- Email or Username -->
                        <div>
                            <label for="email" class="block mb-2 text-sm text-gray-600 ">Username</label>
                            <input type="text" name="email" id="email" placeholder="username"
                                class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40"
                                required />
                        </div>

                        <!-- Password -->
                        <div class="mt-6">
                            <div class="flex justify-between mb-2">
                                <label for="password" class="text-sm text-gray-600">Password</label>
                                <a href="#"
                                    class="text-sm text-gray-400 focus:text-blue-500 hover:text-blue-500 hover:underline">Forgot
                                    password?</a>
                            </div>

                            <input type="password" name="password" id="password" placeholder="Your Password"
                                class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40"
                                required />
                        </div>

                        <!-- Display Error Message -->
                        <?php if (isset($errorMessage)): ?>
                            <div class="mt-4 text-red-500">
                                <?php echo $errorMessage; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Sign In Button -->
                        <div class="mt-6">
                            <button type="submit"
                                class="w-full px-4 py-2 tracking-wide text-white transition-colors duration-300 transform bg-green-500 rounded-lg hover:bg-green-400 focus:outline-none focus:bg-blue-400 focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                                Sign in
                            </button>
                        </div>

                    </form>

                    <p class="mt-6 text-sm text-center text-gray-400">Don&#x27;t have an account yet? <a href="#"
                            class="text-green-500 focus:outline-none focus:underline hover:underline">Sign up</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>