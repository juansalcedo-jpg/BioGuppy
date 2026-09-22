<?php if (!empty($mensaje)): ?>
    <div class="list-group-item small text-muted py-2">
        <i class="bi bi-info-circle me-1"></i> <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php else: ?>
    <?php foreach ($sugerencias as $sugerencia): ?>
        <button type="button" class="list-group-item list-group-item-action py-2 opcion-direccion"
                data-direccion="<?php echo htmlspecialchars($sugerencia['valor']); ?>">
            <div class="d-flex align-items-center">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                <div>
                    <div class="fw-semibold text-dark small"><?php echo htmlspecialchars($sugerencia['via']); ?></div>
                    <div class="text-muted small">
                        <?php echo htmlspecialchars(($sugerencia['barrio'] !== '' ? $sugerencia['barrio'] . ' · ' : '') . $sugerencia['ciudad']); ?>
                    </div>
                </div>
            </div>
        </button>
    <?php endforeach; ?>
<?php endif; ?>
