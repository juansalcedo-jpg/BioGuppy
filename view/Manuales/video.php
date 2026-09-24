<div class="container-fluid px-1 py-1" style="max-width: 1000px;">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">

            <h2 class="h4 fw-bold mb-1">
                <i class="bi bi-play-circle text-primary me-2"></i>
                <?php echo htmlspecialchars($manual['titulo'] ?? 'Manual de uso'); ?>
            </h2>
            <p class="text-muted small mb-4">
                Video explicativo para el rol: <strong><?php echo htmlspecialchars($rol); ?></strong>
            </p>

            <?php if($videoUrl): ?>
                <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm bg-black">
                    <video controls preload="metadata">
                        <source src="<?php echo htmlspecialchars($videoUrl); ?>" type="video/mp4">
                        Tu navegador no puede reproducir este video.
                    </video>
                </div>
            <?php else: ?>
                <div class="alert alert-warning border-0 rounded-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Aún no hay un video disponible para tu rol.
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
