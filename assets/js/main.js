function loadPage(page) {
    const content = document.getElementById('content');

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

            if (page === 'closet') {
                setupClosetPage();
            } else if (page === 'generate') {
                setupGeneratePage();
            } else if (page === 'profile') {
                setupProfilePage();
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
                        width: { ideal: 720 },
                        height: { ideal: 1280 },
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
        alert("Picture taken! Processing image...");
    });
}

function setupGeneratePage() {
    // pass
}

function setupProfilePage() {
    // pass
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