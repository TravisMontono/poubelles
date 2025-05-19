    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');

        hamburgerBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // You'll need to adapt your JavaScript to handle multiple bins
        <?php
        if ($result->num_rows > 0) {
            $result->data_seek(0); // Reset the result pointer
            while ($row = $result->fetch_assoc()) {
                ?>
                const alertLevelSlider_<?php echo htmlspecialchars($row['id']); ?> = document.getElementById('alert-level-<?php echo htmlspecialchars($row['id']); ?>');
                const alertLevelValue_<?php echo htmlspecialchars($row['id']); ?> = document.getElementById('alert-level-value-<?php echo htmlspecialchars($row['id']); ?>');

                if (alertLevelSlider_<?php echo htmlspecialchars($row['id']); ?> && alertLevelValue_<?php echo htmlspecialchars($row['id']); ?>) {
                    alertLevelSlider_<?php echo htmlspecialchars($row['id']); ?>.addEventListener('input', () => {
                        alertLevelValue_<?php echo htmlspecialchars($row['id']); ?>.textContent = alertLevelSlider_<?php echo htmlspecialchars($row['id']); ?>.value;
                    });
                }
                <?php
            }
        }
        ?>

        // --- Simulation de la mise à jour des données (à remplacer par des appels API réels) ---
        function updateData() {
            // This function will likely need to be adapted to fetch data for the specific user's bins
            // and update the corresponding HTML elements.
            <?php
            if ($result->num_rows > 0) {
                $result->data_seek(0); // Reset the result pointer again if needed
                while ($row = $result->fetch_assoc()) {
                    ?>
                    const temp_<?php echo htmlspecialchars($row['id']); ?> = Math.floor(Math.random() * 10 + 20); // Simule température entre 20 et 29
                    const humidity_<?php echo htmlspecialchars($row['id']); ?> = Math.floor(Math.random() * 20 + 30); // Simule humidité entre 30 et 49
                    const internalTempSpan_<?php echo htmlspecialchars($row['id']); ?> = document.getElementById('internal-temp-<?php echo htmlspecialchars($row['id']); ?>');
                    const internalHumiditySpan_<?php echo htmlspecialchars($row['id']); ?> = document.getElementById('internal-humidity-<?php echo htmlspecialchars($row['id']); ?>');

                    if (internalTempSpan_<?php echo htmlspecialchars($row['id']); ?> && internalHumiditySpan_<?php echo htmlspecialchars($row['id']); ?>) {
                        internalTempSpan_<?php echo htmlspecialchars($row['id']); ?>.textContent = temp_<?php echo htmlspecialchars($row['id']); ?>;
                        internalHumiditySpan_<?php echo htmlspecialchars($row['id']); ?>.textContent = humidity_<?php echo htmlspecialchars($row['id']); ?>;
                    }
                    <?php
                }
            }
            ?>
        }

        setInterval(updateData, 3000); // Mise à jour toutes les 3 secondes

        function updateBinDisplay(binId, fillLevel, batteryLevel, lastEmptied) {
            const fillIcon = document.getElementById(`fill-icon-${binId}`);
            const fillPercentage = document.getElementById(`fill-percentage-${binId}`);
            const fillStatus = document.getElementById(`fill-status-${binId}`);
            const batteryBar = document.getElementById(`battery-level-${binId}`);
            const batteryPercentage = document.getElementById(`battery-percentage-${binId}`);
            const batteryStatusText = document.getElementById(`battery-status-text-${binId}`);
            const lastEmptiedSpan = document.getElementById(`last-emptied-${binId}`);

            if (fillPercentage && fillStatus && fillIcon) {
                fillPercentage.textContent = fillLevel;
                if (fillLevel < 30) {
                    fillIcon.className = 'fas fa-trash-alt fill-level-icon fill-level-empty';
                    fillStatus.textContent = 'Faible';
                } else if (fillLevel < 70) {
                    fillIcon.className = 'fas fa-trash-alt fill-level-icon';
                    fillStatus.textContent = 'Partiellement pleine';
                } else {
                    fillIcon.className = 'fas fa-trash-alt fill-level-icon';
                    fillStatus.textContent = 'Pleine';
                }
            }

            if (batteryBar && batteryPercentage && batteryStatusText) {
                batteryBar.style.width = `${batteryLevel}%`;
                batteryPercentage.textContent = `${batteryLevel}%`;
                if (batteryLevel < 20) {
                    batteryStatusText.textContent = 'Faible';
                } else if (batteryLevel < 50) {
                    batteryStatusText.textContent = 'Moyenne';
                } else {
                    batteryStatusText.textContent = 'Bonne';
                }
            }
            if (lastEmptiedSpan) {
                lastEmptiedSpan.textContent = lastEmptied;
            }
        }

        // --- Simulation de l'intervalle de mise à jour des données pour chaque poubelle ---
        setInterval(() => {
            <?php
            if ($result->num_rows > 0) {
                $result->data_seek(0); // Réinitialiser le pointeur de résultat
                while ($row = $result->fetch_assoc()) {
                    $bin_id = htmlspecialchars($row['id']);
                    ?>
                    const randomFill_<?php echo $bin_id; ?> = Math.floor(Math.random() * 101);
                    const randomBattery_<?php echo $bin_id; ?> = Math.floor(Math.random() * 101);
                    const lastEmptyDate_<?php echo $bin_id; ?> = new Date(Date.now() - Math.random() * 7 * 24 * 60 * 60 * 1000).toLocaleDateString('fr-FR', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
                    updateBinDisplay('<?php echo $bin_id; ?>', randomFill_<?php echo $bin_id; ?>, randomBattery_<?php echo $bin_id; ?>, lastEmptyDate_<?php echo $bin_id; ?>);
                    <?php
                }
            }
            ?>
        }, 5000);

        // --- Gestion de l'enregistrement des paramètres d'alerte ---
        const enregistrerButtons = document.querySelectorAll('.btn-enregistrer');
        enregistrerButtons.forEach(button => {
            button.addEventListener('click', function() {
                const binId = this.dataset.binId;
                const alertLevel = document.getElementById(`alert-level-${binId}`).value;
                const alertEmail = document.getElementById(`alert-email-${binId}`).checked ? 1 : 0;
                const alertPush = document.getElementById(`alert-push-${binId}`).checked ? 1 : 0;

                // Ici, vous devrez envoyer ces données au serveur (via une requête AJAX par exemple)
                console.log('Enregistrer pour la poubelle ID:', binId, 'Seuil:', alertLevel, 'Email:', alertEmail, 'Push:', alertPush);

                // Exemple d'appel Fetch pour envoyer les données au serveur (à adapter)
                fetch('update_bin_settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `bin_id=${binId}&alert_level=${alertLevel}&alert_email=${alertEmail}&alert_push=${alertPush}`
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Réponse du serveur:', data);
                    // Afficher un message de succès à l'utilisateur
                    alert('Paramètres enregistrés avec succès pour la poubelle ' + binId);
                })
                .catch((error) => {
                    console.error('Erreur lors de l\'enregistrement:', error);
                    // Afficher un message d'erreur à l'utilisateur
                    alert('Erreur lors de l\'enregistrement des paramètres pour la poubelle ' + binId);
                });
            });
        });

        // --- Note concernant la connexion à la poubelle physique ---
        // Comme mentionné précédemment, la connexion réelle nécessitera un backend
        // et des appels API pour récupérer les données de votre poubelle intelligente.
        // Le JavaScript ici simule uniquement la mise à jour des données dans l'interface.
    </script>