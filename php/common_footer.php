<?php
// common_footer.php
?>

    <!-- Scripts Comunes -->
    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/menu.js"></script>
    <script src="../js/inactivity_timer.js"></script>
    <script src="../js/modal.js"></script>

    <!-- Modal para ver la imagen de perfil -->
    <div id="image-modal" class="modal">
        <span class="close-modal">&times;</span>
        <img class="modal-content" id="modal-image">
    </div>

    <!-- Nuevo Modal para Conflicto de Sesión -->
    <div id="session-conflict-modal" class="modal">
        <div class="modal-content">
            <h2>Conflicto de Sesión</h2>
            <p id="session-conflict-message">Se ha iniciado sesión en otra ventana o navegador.</p>
            <div class="modal-actions">
                <button id="close-other-session-btn" class="btn btn-primary">Cerrar sesión anterior</button>
                <button id="keep-this-session-btn" class="btn btn-secondary">Mantener esta sesión</button>
            </div>
        </div>
    </div>

</body>
</html>
