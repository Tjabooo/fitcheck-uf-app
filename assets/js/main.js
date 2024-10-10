function loadPage(page) {
    const content = document.getElementById('content');
    const settingsLink = document.getElementById('settings-link');

    fetch(`../../templates/${page}.html`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Unable to load ${page}.html`);
            }
            return response.text();
        })
        .then(html => {
            content.innerHTML = html;
            history.pushState({ page }, null, `#${page}`);

            switch (page) {
                case 'closet':
                    setupClosetPage();
                    break;
                case 'generate':
                    setupGeneratePage();
                    break;
                case 'profile':
                    setupProfilePage();
                    break;
                case 'settings':
                    setupSettingsPage();
                default:
                    break;
            }

            if (page === 'profile') {
                settingsLink.style.display = 'block';
            } else {
                settingsLink.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading page:', error);
            content.innerHTML = '<p>Sorry, an error occurred while loading the page.</p>';
        });
}

function setupClosetPage() {
    const addNewClothesButton = document.getElementById('add-new-clothes');
    const addClothesScreen = document.getElementById('add-clothes-screen');
    const closeAddClothesButton = document.getElementById('close-add-clothes');
    const video = document.getElementById('video');
    const addClothingButton = document.getElementById('add-clothing');

    addClothingButton.disabled = true;

    addNewClothesButton.addEventListener('click', () => {
        addClothesScreen.classList.add('active');
        if (addClothingButton.disabled) {
            alert("Please allow camera access to take a picture.");
        }
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia(
                {
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: "environment"
                    }
                })
                .then(stream => {
                    video.srcObject = stream;
                    addClothingButton.disabled = false;
                })
                .catch(err => {
                    console.error("Error accessing camera: ", err);
                    alert("Unable to access camera.");
                    addClothingButton.disabled = true;
                });
        } else {
            alert("Camera not supported on this device.");
            addClothingButton.disabled = true;
        }
    });

    closeAddClothesButton.addEventListener('click', () => {
        addClothesScreen.classList.remove('active');
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
            video.srcObject = null;
        }
        addClothingButton.disabled = true;
    });

    addClothingButton.addEventListener('click', () => {
        // do stuff
    });
}

function setupGeneratePage() {
    // pass
}

function setupProfilePage() {
    // Fetch and display user profile information
    fetch('../../includes/get_profile_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('username').textContent = data.username;
                document.getElementById('email').textContent = data.email;

                // Set profile picture
                if (data.profile_picture) {
                    document.getElementById('profile-picture').src = data.profile_picture + `?t=${new Date().getTime()}`; // Prevent caching
                } else {
                    document.getElementById('profile-picture').src = 'assets/profile-pictures/default.png';
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching profile:', error);
            alert('Failed to load profile information.');
        });

    // Handle profile picture edit icon click
    const editButton = document.getElementById('edit-profile-picture');
    const fileInput = document.getElementById('profile-picture-input');
    const applyButton = document.getElementById('apply-profile-picture');
    let selectedFile = null;

    editButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            selectedFile = file;
            // Show preview of selected image
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('profile-picture').src = e.target.result;
                applyButton.style.display = 'block'; // Show apply button
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle apply profile picture click
    applyButton.addEventListener('click', () => {
        if (selectedFile) {
            const formData = new FormData();
            formData.append('profile_picture', selectedFile);

            fetch('../../includes/upload_profile_picture.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const profilePicElement = document.getElementById('profile-picture');
                        const newProfilePicturePath = `${data.profile_picture}?t=${new Date().getTime()}`;  // Ensure cache busting
                        profilePicElement.src = newProfilePicturePath;
                        applyButton.style.display = 'none'; // Hide apply button
                        document.getElementById('upload-status').textContent = 'Profile picture updated successfully.';
                        document.getElementById('upload-status').style.display = 'block';
                    } else {
                        document.getElementById('upload-error').textContent = `Error: ${data.error}`;
                        document.getElementById('upload-error').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error uploading profile picture:', error);
                    document.getElementById('upload-error').textContent = 'An error occurred while uploading the profile picture.';
                    document.getElementById('upload-error').style.display = 'block';
                });
        }
    });
}

function setupSettingsPage() {
    const deleteButton = document.getElementById('delete-account-button');
    const deleteStatus = document.getElementById('delete-status');
    const deleteError = document.getElementById('delete-error');

    deleteButton.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            // Reset messages
            deleteStatus.style.display = 'none';
            deleteError.style.display = 'none';

            fetch('../../includes/delete_account.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ confirm: true })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        deleteStatus.textContent = 'Your account has been deleted.';
                        deleteStatus.style.display = 'block';
                        // Redirect to homepage or logout after a short delay
                        setTimeout(() => {
                            window.location.href = 'logout.php';
                        }, 2000);
                    } else {
                        deleteError.textContent = `Error: ${data.message}`;
                        deleteError.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error deleting account:', error);
                    deleteError.textContent = 'An error occurred while deleting your account.';
                    deleteError.style.display = 'block';
                });
        }
    });
}

document.querySelectorAll('nav button').forEach(button => {
    button.addEventListener('click', () => {
        const target = button.getAttribute('data-target');
        loadPage(target);
    });
});

window.addEventListener('popstate', (event) => {
    const page = event.state ? event.state.page : 'closet';
    loadPage(page);
});

const initialPage = window.location.hash.replace('#', '') || 'closet';
loadPage(initialPage);