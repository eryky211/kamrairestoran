<?php 
require_once 'auth.php'; 
check_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Announcements</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php require_once 'sidebar.php'; ?>

    <div class="main-content">

        <header>
            <h1>Manage Announcements</h1>
        </header>

        <main>
            <div class="admin-form-container" style="max-width: 800px;">
                <p>Edit the text that appears in the floating boxes on the homepage.</p>
                
                <form id="announcements-form" class="admin-form">
                    <label for="announcement-box1">Top-Left Box (Announcement)</label>
                    <textarea id="announcement-box1" name="box1" rows="4" required></textarea>
                    
                    <label for="announcement-box2">Top-Right Box (Daily Special)</label>
                    <textarea id="announcement-box2" name="box2" rows="4" required></textarea>
                    
                    <button type="submit">Save Changes</button>
                    <p id="form-message" style="margin-top: 1rem; font-weight: bold;"></p>
                </form>
            </div>
        </main>

    </div> <!-- End .main-content -->

    <script>
        // Function to load existing announcement data
        async function loadAnnouncements() {
            try {
                const response = await fetch('api/get_announcements.php');
                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.box1) {
                    document.getElementById('announcement-box1').value = data.box1;
                }
                if (data.box2) {
                    document.getElementById('announcement-box2').value = data.box2;
                }

            } catch (error) {
                console.error('Error loading announcements:', error);
                document.getElementById('form-message').textContent = 'Error loading data.';
                document.getElementById('form-message').style.color = 'red';
            }
        }

        // Function to handle saving the form
        document.getElementById('announcements-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const msgEl = document.getElementById('form-message');
            
            msgEl.textContent = 'Saving...';
            msgEl.style.color = '#8D5B4C'; // Main brown

            try {
                const response = await fetch('api/update_announcements.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    msgEl.textContent = 'Changes saved successfully!';
                    msgEl.style.color = 'green';
                } else {
                    msgEl.textContent = 'Error: ' + result.error;
                    msgEl.style.color = 'red';
                }
            } catch (error) {
                console.error('Error:', error);
                msgEl.textContent = 'An unknown error occurred.';
                msgEl.style.color = 'red';
            }
        });

        // Load the data when the page opens
        document.addEventListener('DOMContentLoaded', loadAnnouncements);
    </script>
</body>
</html>

