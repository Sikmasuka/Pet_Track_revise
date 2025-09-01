<?php
// Debug: Log data to console for verification
echo '<script>';
if (isset($vet)) {
    echo 'console.log("Debug: $vet data in edit-profile.php", ' . json_encode($vet) . ');';
} elseif (isset($currentAdmin)) {
    echo 'console.log("Debug: $currentAdmin data in edit-profile.php", ' . json_encode($currentAdmin) . ');';
} else {
    echo 'console.error("Error: Neither $vet nor $currentAdmin is defined in edit-profile.php");';
}
echo '</script>';
?>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[85vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="editProfileModalContent">
        <!-- Modal Header -->
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Edit Profile</h3>
                <button onclick="if (typeof toggleModal === 'undefined') { console.error('toggleModal not defined'); } else { toggleModal('editProfileModal'); }" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-4">
            <form method="POST" action="functions/profile-handler.php" class="space-y-4" id="editProfileForm">
                <!-- Hidden field to indicate user type -->
                <input type="hidden" name="user_type" value="<?php echo isset($currentAdmin) ? 'admin' : 'vet'; ?>">

                <!-- Name Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text"
                        name="name"
                        value="<?php echo htmlspecialchars($currentAdmin['admin_name'] ?? $vet['vet_name'] ?? ''); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        required
                        placeholder="Enter your full name">
                </div>

                <!-- Contact Number Field (only for vets) -->
                <?php if (isset($vet)): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="tel"
                            name="contact_number"
                            value="<?php echo htmlspecialchars($vet['vet_contact_number'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            required
                            placeholder="Enter your contact number">
                    </div>
                <?php endif; ?>

                <!-- Username Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text"
                        name="username"
                        value="<?php echo htmlspecialchars($currentAdmin['admin_username'] ?? $vet['vet_username'] ?? ''); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        required
                        placeholder="Enter your username">
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <div class="relative">
                        <input type="password"
                            name="password"
                            id="vetPassword"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Leave blank to keep current">
                        <button type="button"
                            onclick="if (typeof toggleModalPassword === 'undefined') { console.error('toggleModalPassword not defined'); } else { toggleModalPassword(); }"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye text-sm" id="modalPasswordToggle"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Only fill this to change your password</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-4">
                    <button type="button"
                        onclick="if (typeof resetForm === 'undefined') { console.error('resetForm not defined'); } else { resetForm(); }"
                        class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition-colors text-sm">
                        Reset
                    </button>
                    <button type="button"
                        onclick="if (typeof toggleModal === 'undefined') { console.error('toggleModal not defined'); } else { toggleModal('editProfileModal'); }"
                        class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>