<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tasks WHERE user_id = ? ORDER BY deadline ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$habit_sql = "SELECT * FROM habits WHERE user_id = ?";
$habit_stmt = $conn->prepare($habit_sql);
$habit_stmt->bind_param("i", $user_id);
$habit_stmt->execute();
$habit_result = $habit_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="min-h-screen">

    <!-- Navbar -->
    <div id="navbar" class="text-white px-28 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="assets/img/nexdo.png" class="w-14 rounded-full" alt="logo">
            <h1 class="text-xl font-bold">Nexdo</h1>
        </div>
        <div>
            <span class="mr-4">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">Logout</a>
        </div>
    </div>

    <!--img-background-->
    <div class="relative h-[400px] w-full bg-cover bg-center text-white"
        style="background-image: url('assets/img/wow.gif')">
        <!-- Overlay hitam transparan -->
        <div class="absolute inset-0 bg-black bg-opacity-20 flex flex-col justify-center items-center px-4 p-4">
        </div>
    </div>
    <!-- Content -->
    <div id="todolist" class="px-28 py-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">To-Do List</h2>
            <button onclick="toggleModal()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                +
            </button>
        </div>

        <!-- Grid Tugas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div id="card" class="p-4 rounded-xl shadow border border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-300"><?= htmlspecialchars($row['title']) ?></h3>
                    <p class="text-sm text-gray-400"><?= htmlspecialchars($row['description']) ?></p>
                    <p class="text-sm text-gray-500 mt-2">Deadline: <?= htmlspecialchars($row['deadline']) ?></p>
                    <p class="text-sm mt-1 text-gray-200">
                        Status:
                        <?php
                        $statusColor = match ($row['status']) {
                            'done' => 'text-green-600',
                            'in-progress' => 'text-blue-600',
                            default => 'text-yellow-600',
                        };
                        ?>
                        <span class="<?= $statusColor ?>"><?= ucfirst($row['status']) ?></span>
                    </p>

                    <?php if (!empty($row['file_path'])): ?>
                        <p class="text-sm text-blue-600 mt-2">
                            <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="underline">Lihat
                                File</a>
                        </p>
                    <?php endif; ?>

                    <div class="flex gap-1 mt-4 flex-row-reverse">
                        <?php if ($row['status'] === 'pending'): ?>
                            <form method="POST" action="update_task.php">
                                <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="new_status" value="in-progress">
                                <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Mulai</button>
                            </form>
                        <?php elseif ($row['status'] === 'in-progress'): ?>
                            <form method="POST" action="update_task.php">
                                <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="new_status" value="done">
                                <button
                                    class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">in-progress</button>
                            </form>
                        <?php else: ?>
                            <button disabled
                                class="bg-gray-400 text-white px-3 py-1 rounded cursor-not-allowed">Selesai</button>
                        <?php endif; ?>

                        <!-- Edit -->
                        <button onclick="openEditModal(
                            <?= $row['id'] ?>,
                            '<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>',
                            '<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>',
                            '<?= $row['deadline'] ?>',
                            '<?= htmlspecialchars($row['file_path'], ENT_QUOTES) ?>'
                        )" class="text-yellow-300 px-3 py-1 rounded hover:text-yellow-500"><svg
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path
                                    d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                <path
                                    d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                            </svg>
                        </button>

                        <!-- Delete -->
                        <a href="delete_task.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')"
                            class="text-red-500 px-3 py-1 rounded hover:text-red-600"><svg
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd"
                                    d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!--habits track-->
    <div id="habits" class="px-28 py-6">
        <h2 class="text-2xl font-bold mb-4">Habit Tracker</h2>

        <form method="POST" action="add_habit.php" class="flex gap-2 mb-4">
            <input type="text" name="habit_name" required placeholder="Nama habit" class="p-2 border rounded w-full" />
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                +
            </button>
        </form>
        <div class=" overflow-x-auto">
            <table class="w-full bg-gray-800 rounded-xl shadow border text-white border-gray-600">
                <thead>
                    <tr class="bg-gray-900 text-left">
                        <th class="px-4 py-2 border-b">#</th>
                        <th class="px-4 py-2 border-b">Nama Habit</th>
                        <th class="px-4 py-2 border-b">Status Hari Ini</th>
                        <th class="px-4 py-2 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 1;
                    while ($habit = $habit_result->fetch_assoc()):
                        $today = date('Y-m-d');
                        $habit_id = $habit['id'];

                        $check_sql = "SELECT * FROM habit_logs WHERE habit_id = ? AND log_date = ?";
                        $check_stmt = $conn->prepare($check_sql);
                        $check_stmt->bind_param("is", $habit_id, $today);
                        $check_stmt->execute();
                        $log = $check_stmt->get_result()->fetch_assoc();
                        ?>
                        <tr class="hover:bg-gray-900">
                            <td class="px-4 py-2 border-b"><?= $index++ ?></td>
                            <td class="px-4 py-2 border-b"><?= htmlspecialchars($habit['name']) ?></td>
                            <td class="px-4 py-2 border-b">
                                <?php if ($log && $log['status']): ?>
                                    <span class="text-green-600 font-semibold">✔️ Sudah</span>
                                <?php else: ?>
                                    <span class="text-red-500 font-semibold">❌ Belum</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border-b">
                                <?php if (!$log || !$log['status']): ?>
                                    <form method="POST" action="track_habit.php">
                                        <input type="hidden" name="habit_id" value="<?= $habit_id ?>">
                                        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                            Tandai Sudah
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Sudah dicatat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>



    </div>


    <!-- Modal Tambah Tugas -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <form method="POST" action="add_task.php" enctype="multipart/form-data"
            class="bg-white p-6 rounded-xl shadow w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Tambah Tugas Baru</h2>

            <label class="block mb-2">Judul</label>
            <input name="title" required class="w-full mb-4 p-2 border rounded" />

            <label class="block mb-2">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full mb-4 p-2 border rounded"></textarea>

            <label class="block mb-2">Deadline</label>
            <input type="date" name="deadline" required class="w-full mb-4 p-2 border rounded" />

            <label class="block mb-2">Upload Gambar/File</label>
            <input type="file" name="file" class="w-full mb-4" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" />

            <div class="flex justify-between">
                <button type="button" onclick="toggleModal()"
                    class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
                <button type="submit"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Simpan</button>
            </div>
        </form>
    </div>

    <!-- Modal Edit Tugas -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <form method="POST" action="edit_task.php" enctype="multipart/form-data"
            class="bg-white p-6 rounded-xl shadow w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Edit Tugas</h2>

            <input type="hidden" name="task_id" id="edit_task_id" />

            <label class="block mb-2">Judul</label>
            <input name="title" id="edit_title" required class="w-full mb-4 p-2 border rounded" />

            <label class="block mb-2">Deskripsi</label>
            <textarea name="description" id="edit_description" rows="3"
                class="w-full mb-4 p-2 border rounded"></textarea>

            <label class="block mb-2">Deadline</label>
            <input type="date" name="deadline" id="edit_deadline" required class="w-full mb-4 p-2 border rounded" />

            <label class="block mb-2">Upload File (Opsional)</label>
            <div id="edit_file_preview" class="mb-4 text-sm text-blue-600"></div>
            <input type="file" name="file" class="w-full mb-4" />

            <div class="flex justify-between">
                <button type="button" onclick="toggleEditModal()"
                    class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>

    <!-- Script -->
    <script>
        function toggleModal() {
            const modal = document.getElementById("taskModal");
            modal.classList.toggle("hidden");
            modal.classList.toggle("flex");
        }

        function toggleEditModal() {
            const modal = document.getElementById("editModal");
            modal.classList.toggle("hidden");
            modal.classList.toggle("flex");
        }

        function openEditModal(id, title, description, deadline, filePath = '') {
            document.getElementById('edit_task_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_deadline').value = deadline;

            const preview = document.getElementById('edit_file_preview');
            preview.innerHTML = filePath
                ? `<a href="${filePath}" target="_blank" class="underline">Lihat File Lama</a>`
                : '';
            toggleEditModal();
        }
    </script>

</body>

</html>