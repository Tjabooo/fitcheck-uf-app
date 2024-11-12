<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

<div id="cameraModal" class="modal" style="display: none;">
    <div class="modal-content">
        <button class="exit-button" onclick="closeCameraModal()">X</button>
        <video id="cameraView" autoplay playsinline></video>
        <button id="snapButton" onclick="#">Ta bild</button>
        <ul class="instructions">
            <li>1. Lägg ner plagget på en platt yta och sträck ut den så att hela plagget syns.</li>
            <li>2. Se till att ljuset är bra och starkt.</li>
            <li>3. Ta bort alla andra objekt i bildramen.</li>
        </ul>
    </div>
</div>

<script src="{{ asset('js/main.js') }}"></script>
