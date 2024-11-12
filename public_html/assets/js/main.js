// Profile page scripts
const editButton = document.getElementById('edit-profile-picture');
const applyButton = document.getElementById('apply-profile-picture');
const fileInput = document.getElementById('profile-picture-input');
let selectedFile = null;

editButton.addEventListener('click', () => {
    fileInput.click();
})

fileInput.addEventListener('change', (event) => {
    const file = event.target.files[0];

    if (file) {
        selectedFile = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('profile-picture').src = e.target.result;
            applyButton.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
})

applyButton.addEventListener('click', () => {
    if (selectedFile) {
        const formData = new FormData();
        formData.append('profile_picture', selectedFile);

        fetch('/spegel/bild', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'url': '/spegel/bild',
                "X-CSRF-Token": document.querySelector('input[name=_token]').value
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    const profilePicElement = document.getElementById('profile-picture');
                    const newProfilePicturePath = `${data.profile_picture}?t=${new Date().getTime()}`;
                    profilePicElement.src = newProfilePicturePath;
                    applyButton.style.display = 'none';
                    document.getElementById('upload-status').textContent = 'Din spegelbild är uppdaterad!';
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


// Open the camera modal
function openCameraModal() {
    document.getElementById('cameraModal').style.display = 'block';
    startCamera();
}

// Close the camera modal
function closeCameraModal() {
    document.getElementById('cameraModal').style.display = 'none';
    stopCamera();
}

// Start the camera
function startCamera() {
    const video = document.getElementById('cameraView');
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(error => console.error("Camera error:", error));
    } else {
        alert("Camera not supported on this device");
    }
}

// Stop the camera
function stopCamera() {
    const video = document.getElementById('cameraView');
    const stream = video.srcObject;
    if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
    }
}
