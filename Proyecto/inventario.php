<?php
require_once 'session.php';
require_once 'database.php';

$db   = new Database();
$conn = $db->conn;

// ── Operaciones CRUD via POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $stmt = $conn->prepare("
            INSERT INTO productos (nombre, precio, stock, pais_origen)
            VALUES (:nombre, :precio, :stock, :pais)
        ");
        $stmt->execute([
            ':nombre' => trim($_POST['nombre']),
            ':precio' => (float)$_POST['precio'],
            ':stock'  => (int)$_POST['stock'],
            ':pais'   => (int)$_POST['pais_origen'],
        ]);
    }

    if ($accion === 'editar') {
        $stmt = $conn->prepare("
            UPDATE productos
            SET nombre=:nombre, precio=:precio, stock=:stock, pais_origen=:pais
            WHERE id=:id
        ");
        $stmt->execute([
            ':nombre' => trim($_POST['nombre']),
            ':precio' => (float)$_POST['precio'],
            ':stock'  => (int)$_POST['stock'],
            ':pais'   => (int)$_POST['pais_origen'],
            ':id'     => (int)$_POST['id'],
        ]);
    }

    if ($accion === 'eliminar') {
        $id_producto = (int)$_POST['id'];

        // 1. Copiar a la papelera (sin columna codigo)
        $stmtCopia = $conn->prepare("
            INSERT INTO productos_eliminados (producto_id, nombre, precio, stock, pais_origen, eliminado_por)
            SELECT id, nombre, precio, stock, pais_origen, :usuario_id
            FROM productos
            WHERE id = :id
        ");
        $stmtCopia->execute([
            ':usuario_id' => $_SESSION['usuario_id'],
            ':id'         => $id_producto,
        ]);

        // 2. Borrar de la tabla principal
        $stmtBorra = $conn->prepare("DELETE FROM productos WHERE id = :id");
        $stmtBorra->execute([':id' => $id_producto]);
    }

    $qs = http_build_query([
        'buscar' => $_POST['buscar_actual'] ?? '',
        'pais'   => $_POST['pais_actual']   ?? '',
        'pagina' => $_POST['pagina_actual'] ?? 1,
    ]);
    header("Location: inventario.php?$qs");
    exit();
}

// ── Parámetros de búsqueda / filtro / paginación ──────────────────────────
$buscar    = trim($_GET['buscar'] ?? '');
$paisFiltro = (int)($_GET['pais'] ?? 0);   // ahora es el ID del país
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 10;
$offset    = ($pagina - 1) * $porPagina;

// ── WHERE dinámico ────────────────────────────────────────────────────────
$where  = "WHERE 1=1";
$params = [];

if ($buscar !== '') {
    $where .= " AND (p.nombre LIKE :buscar OR p.id = :buscar_id)";
    $params[':buscar']    = "%$buscar%";
    $params[':buscar_id'] = is_numeric($buscar) ? (int)$buscar : -1;
}
if ($paisFiltro > 0) {
    $where .= " AND p.pais_origen = :pais";
    $params[':pais'] = $paisFiltro;
}

// ── Total de registros ────────────────────────────────────────────────────
$stmtTotal = $conn->prepare("SELECT COUNT(*) FROM productos p $where");
$stmtTotal->execute($params);
$total        = (int)$stmtTotal->fetchColumn();
$totalPaginas = max(1, ceil($total / $porPagina));

// ── Productos con JOIN a paises ───────────────────────────────────────────
$stmtProd = $conn->prepare("
    SELECT p.id, p.nombre, p.precio, p.stock,
           p.pais_origen AS pais_id,
           pa.nombre     AS pais_nombre
    FROM productos p
    INNER JOIN paises pa ON pa.id = p.pais_origen
    $where
    ORDER BY p.id ASC
    LIMIT :limit OFFSET :offset
");
foreach ($params as $k => $v) {
    $stmtProd->bindValue($k, $v);
}
$stmtProd->bindValue(':limit',  $porPagina, PDO::PARAM_INT);
$stmtProd->bindValue(':offset', $offset,    PDO::PARAM_INT);
$stmtProd->execute();
$productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

// ── Lista completa de países (para filtro y modales) ──────────────────────
$todosLosPaises = $conn->query("SELECT id, nombre FROM paises ORDER BY nombre ASC")
                       ->fetchAll(PDO::FETCH_ASSOC);

// ── Países que tienen al menos un producto (para el filtro) ───────────────
$paisesConProductos = $conn->query("
    SELECT DISTINCT pa.id, pa.nombre
    FROM paises pa
    INNER JOIN productos p ON p.pais_origen = pa.id
    ORDER BY pa.nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Stats resumen ─────────────────────────────────────────────────────────
$stats = $conn->query("
    SELECT
        COUNT(*)                                     AS total_productos,
        COALESCE(SUM(stock), 0)                      AS stock_total,
        COALESCE(SUM(precio * stock), 0)             AS valor_total,
        SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) AS stock_bajo
    FROM productos
")->fetch(PDO::FETCH_ASSOC);

// ── Helper: badge de stock ────────────────────────────────────────────────
function badgeStock(int $stock): string {
    if ($stock <= 10) return 'red';
    if ($stock <= 30) return 'yellow';
    return 'green';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Brangus · Inventario</title>
  <link rel="stylesheet" href="inventario.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body>

  <!-- ═══════════════════════════════════════════════════════════════════════
       MODAL — CREAR PRODUCTO
  ════════════════════════════════════════════════════════════════════════ -->
  <div class="modal-overlay" id="modalCrear">
    <div class="modal">
      <div class="modal-header">
        <h2 class="modal-title"><i class="fa-solid fa-plus"></i> Nuevo producto</h2>
        <button type="button" class="modal-close" data-close="modalCrear"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form method="POST" action="inventario.php">
        <input type="hidden" name="accion"        value="crear" />
        <input type="hidden" name="buscar_actual" value="<?php echo htmlspecialchars($buscar); ?>" />
        <input type="hidden" name="pais_actual"   value="<?php echo $paisFiltro; ?>" />
        <input type="hidden" name="pagina_actual" value="<?php echo $pagina; ?>" />
        <div class="modal-body">
          <div class="modal-grid">
            <div class="field modal-full">
              <label>Nombre del producto</label>
              <input type="text" name="nombre" placeholder="Ej: Carne de res premium" required />
            </div>
            <div class="field">
              <label>Precio</label>
              <input type="number" name="precio" placeholder="45000" min="0" step="0.01" required />
            </div>
            <div class="field">
              <label>Stock</label>
              <input type="number" name="stock" placeholder="100" min="0" required />
            </div>
            <div class="field modal-full">
              <label>País de origen</label>
              <!-- Select nativo oculto -->
              <select name="pais_origen" id="crearPaisNativo" style="display:none" required>
                <option value="">Selecciona un país...</option>
                <?php foreach ($todosLosPaises as $pa): ?>
                  <option value="<?php echo $pa['id']; ?>"><?php echo htmlspecialchars($pa['nombre']); ?></option>
                <?php endforeach; ?>
              </select>
              <!-- Dropdown custom -->
              <div class="cs-wrap" id="crearPaisCustom">
                <div class="cs-trigger">
                  <img class="cs-flag" src="" alt="" style="display:none"/>
                  <span class="cs-label">Selecciona un país...</span>
                  <i class="fa-solid fa-chevron-down cs-arrow"></i>
                </div>
                <div class="cs-dropdown cs-searchable">
                  <div class="cs-search-wrap">
                    <input type="text" class="cs-search" placeholder="Buscar país..." />
                  </div>
                  <div class="cs-options-list">
                    <?php foreach ($todosLosPaises as $pa): ?>
                      <div class="cs-option"
                           data-value="<?php echo $pa['id']; ?>"
                           data-nombre="<?php echo htmlspecialchars($pa['nombre']); ?>"
                           data-flag="">
                        <img class="cs-opt-flag" src="" alt="<?php echo htmlspecialchars($pa['nombre']); ?>" />
                        <span class="cs-opt-label"><?php echo htmlspecialchars($pa['nombre']); ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-modal-cancel" data-close="modalCrear">Cancelar</button>
          <button type="submit" class="btn-modal-confirm crear">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════════
       MODAL — EDITAR PRODUCTO
  ════════════════════════════════════════════════════════════════════════ -->
  <div class="modal-overlay" id="modalEditar">
    <div class="modal">
      <div class="modal-header">
        <h2 class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Editar producto</h2>
        <button type="button" class="modal-close" data-close="modalEditar"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form method="POST" action="inventario.php">
        <input type="hidden" name="accion"        value="editar" />
        <input type="hidden" name="id"            id="edit-id" />
        <input type="hidden" name="buscar_actual" value="<?php echo htmlspecialchars($buscar); ?>" />
        <input type="hidden" name="pais_actual"   value="<?php echo $paisFiltro; ?>" />
        <input type="hidden" name="pagina_actual" value="<?php echo $pagina; ?>" />
        <div class="modal-body">
          <div class="modal-grid">
            <div class="field modal-full">
              <label>Nombre del producto</label>
              <input type="text" name="nombre" id="edit-nombre" required />
            </div>
            <div class="field">
              <label>Precio</label>
              <input type="number" name="precio" id="edit-precio" min="0" step="0.01" required />
            </div>
            <div class="field">
              <label>Stock</label>
              <input type="number" name="stock" id="edit-stock" min="0" required />
            </div>
            <div class="field modal-full">
              <label>País de origen</label>
              <!-- Select nativo oculto -->
              <select name="pais_origen" id="editPaisNativo" style="display:none" required>
                <option value="">Selecciona un país...</option>
                <?php foreach ($todosLosPaises as $pa): ?>
                  <option value="<?php echo $pa['id']; ?>"><?php echo htmlspecialchars($pa['nombre']); ?></option>
                <?php endforeach; ?>
              </select>
              <!-- Dropdown custom -->
              <div class="cs-wrap" id="editPaisCustom">
                <div class="cs-trigger">
                  <img class="cs-flag" src="" alt="" style="display:none"/>
                  <span class="cs-label">Selecciona un país...</span>
                  <i class="fa-solid fa-chevron-down cs-arrow"></i>
                </div>
                <div class="cs-dropdown cs-searchable">
                  <div class="cs-search-wrap">
                    <input type="text" class="cs-search" placeholder="Buscar país..." />
                  </div>
                  <div class="cs-options-list">
                    <?php foreach ($todosLosPaises as $pa): ?>
                      <div class="cs-option"
                           data-value="<?php echo $pa['id']; ?>"
                           data-nombre="<?php echo htmlspecialchars($pa['nombre']); ?>"
                           data-flag="">
                        <img class="cs-opt-flag" src="" alt="<?php echo htmlspecialchars($pa['nombre']); ?>" />
                        <span class="cs-opt-label"><?php echo htmlspecialchars($pa['nombre']); ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-modal-cancel" data-close="modalEditar">Cancelar</button>
          <button type="submit" class="btn-modal-confirm editar">
            <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════════
       MODAL — ELIMINAR PRODUCTO
  ════════════════════════════════════════════════════════════════════════ -->
  <div class="modal-overlay" id="modalEliminar">
    <div class="modal modal-sm">
      <div class="modal-header">
        <h2 class="modal-title danger"><i class="fa-solid fa-triangle-exclamation"></i> Eliminar producto</h2>
        <button type="button" class="modal-close" data-close="modalEliminar"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form method="POST" action="inventario.php">
        <input type="hidden" name="accion"        value="eliminar" />
        <input type="hidden" name="id"            id="del-id" />
        <input type="hidden" name="buscar_actual" value="<?php echo htmlspecialchars($buscar); ?>" />
        <input type="hidden" name="pais_actual"   value="<?php echo $paisFiltro; ?>" />
        <input type="hidden" name="pagina_actual" value="<?php echo $pagina; ?>" />
        <div class="modal-body">
          <p class="modal-confirm-text">
            ¿Estás seguro de que deseas eliminar
            <strong id="del-nombre"></strong>?
            <br><span class="modal-confirm-sub">El producto pasará a la papelera de eliminados.</span>
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-modal-cancel" data-close="modalEliminar">Cancelar</button>
          <button type="submit" class="btn-modal-confirm eliminar">
            <i class="fa-solid fa-trash"></i> Sí, eliminar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════════
       SIDEBAR
  ════════════════════════════════════════════════════════════════════════ -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <img src="./LOGO.png" alt="Brangus" />
    </div>
    <nav class="sidebar-nav">
      <a href="#" class="nav-item active">
        <i class="fa-solid fa-boxes-stacked nav-icon"></i>Inventario
      </a>
      <a href="#" class="nav-item">
        <i class="fa-solid fa-tag nav-icon"></i>Productos
      </a>
      <a href="#" class="nav-item">
        <i class="fa-solid fa-chart-bar nav-icon"></i>Reportes
      </a>
      <a href="#" class="nav-item">
        <i class="fa-solid fa-gear nav-icon"></i>Configuración
      </a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar">
          <?php echo strtoupper(substr($_SESSION['usuario_nombre'], 0, 1)); ?>
        </div>
        <div>
          <p class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
          <p class="user-email"><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
        </div>
      </div>
      <a href="logout.php" class="btn-logout" title="Cerrar sesión">
        <i class="fa-solid fa-right-from-bracket"></i>
      </a>
    </div>
  </aside>

  <!-- ═══════════════════════════════════════════════════════════════════════
       CONTENIDO PRINCIPAL
  ════════════════════════════════════════════════════════════════════════ -->
  <main class="main">

    <header class="top-bar">
      <div>
        <h1 class="page-title">Inventario de productos</h1>
        <p class="page-sub">Gestiona, busca y administra tus productos</p>
      </div>
      <button type="button" class="btn-new" data-open="modalCrear">
        <i class="fa-solid fa-plus"></i> Nuevo producto
      </button>
    </header>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <p class="stat-label">Total productos</p>
        <p class="stat-num"><?php echo number_format($stats['total_productos']); ?></p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Stock disponible</p>
        <p class="stat-num"><?php echo number_format($stats['stock_total']); ?></p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Valor total</p>
        <p class="stat-num">$<?php echo number_format($stats['valor_total'], 0, ',', '.'); ?></p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Stock bajo</p>
        <p class="stat-num red"><?php echo $stats['stock_bajo']; ?></p>
      </div>
    </div>

    <!-- Toolbar -->
    <form method="GET" action="inventario.php" class="toolbar" id="toolbarForm">
      <input
        class="search-input"
        type="text"
        name="buscar"
        placeholder="Buscar por nombre o ID..."
        value="<?php echo htmlspecialchars($buscar); ?>"
      />

      <!-- Select nativo oculto — se envía con el form -->
      <select name="pais" id="filterPaisNativo" style="display:none">
        <option value="0">Todos los países</option>
        <?php foreach ($paisesConProductos as $pa): ?>
          <option value="<?php echo $pa['id']; ?>"
            <?php echo ($paisFiltro === (int)$pa['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($pa['nombre']); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <!-- Dropdown custom con banderas -->
      <div class="cs-wrap" id="filterPaisCustom">
        <div class="cs-trigger">
          <img class="cs-flag" src="" alt="" />
          <span class="cs-label">Todos los países</span>
          <i class="fa-solid fa-chevron-down cs-arrow"></i>
        </div>
        <div class="cs-dropdown">
          <div class="cs-option" data-value="0" data-nombre="Todos los países" data-flag="">
            <span class="cs-opt-label">Todos los países</span>
          </div>
          <?php foreach ($paisesConProductos as $pa): ?>
            <div class="cs-option"
                 data-value="<?php echo $pa['id']; ?>"
                 data-nombre="<?php echo htmlspecialchars($pa['nombre']); ?>"
                 data-flag="">
              <img class="cs-opt-flag" src="" alt="<?php echo htmlspecialchars($pa['nombre']); ?>" />
              <span class="cs-opt-label"><?php echo htmlspecialchars($pa['nombre']); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i></button>
      <?php if ($buscar || $paisFiltro > 0): ?>
        <a href="inventario.php" class="btn-clear"><i class="fa-solid fa-xmark"></i></a>
      <?php endif; ?>
    </form>

    <!-- Tabla -->
    <div class="table-wrap">
      <table class="product-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>País de origen</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($productos)): ?>
            <tr>
              <td colspan="6" style="text-align:center;padding:32px;color:#aaa;">
                No se encontraron productos.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($productos as $p): ?>
              <tr>
                <td class="code"><?php echo $p['id']; ?></td>
                <td class="name"><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td>$<?php echo number_format($p['precio'], 0, ',', '.'); ?></td>
                <td>
                  <span class="badge <?php echo badgeStock((int)$p['stock']); ?>">
                    <?php echo $p['stock']; ?>
                  </span>
                </td>
                <td class="pais-cell">
                  <img
                    class="flag flag-loading"
                    src=""
                    alt="<?php echo htmlspecialchars($p['pais_nombre']); ?>"
                    data-pais="<?php echo htmlspecialchars($p['pais_nombre']); ?>"
                  /><?php echo htmlspecialchars($p['pais_nombre']); ?>
                </td>
                <td class="actions">
                  <button
                    type="button"
                    class="btn-edit"
                    title="Editar"
                    data-open="modalEditar"
                    data-id="<?php echo $p['id']; ?>"
                    data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>"
                    data-precio="<?php echo $p['precio']; ?>"
                    data-stock="<?php echo $p['stock']; ?>"
                    data-pais="<?php echo $p['pais_id']; ?>"
                  ><i class="fa-solid fa-pen-to-square"></i></button>

                  <button
                    type="button"
                    class="btn-delete"
                    title="Eliminar"
                    data-open="modalEliminar"
                    data-id="<?php echo $p['id']; ?>"
                    data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>"
                  ><i class="fa-solid fa-trash"></i></button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div class="pagination">
      <span class="pag-info">
        Mostrando <?php echo $total === 0 ? 0 : $offset + 1; ?>–<?php echo min($offset + $porPagina, $total); ?>
        de <?php echo $total; ?> productos
      </span>
      <div class="pag-btns">
        <?php
        $qAnterior  = http_build_query(['buscar' => $buscar, 'pais' => $paisFiltro, 'pagina' => $pagina - 1]);
        $qSiguiente = http_build_query(['buscar' => $buscar, 'pais' => $paisFiltro, 'pagina' => $pagina + 1]);
        ?>
        <a href="?<?php echo $qAnterior; ?>"
           class="pag-btn <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
          <i class="fa-solid fa-chevron-left"></i>
        </a>

        <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
          <a href="?<?php echo http_build_query(['buscar' => $buscar, 'pais' => $paisFiltro, 'pagina' => $i]); ?>"
             class="pag-btn <?php echo $i === $pagina ? 'active' : ''; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>

        <a href="?<?php echo $qSiguiente; ?>"
           class="pag-btn <?php echo $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
          <i class="fa-solid fa-chevron-right"></i>
        </a>
      </div>
    </div>

  </main>

  <script>
  // ── Datos de países desde PHP ─────────────────────────────────────────────
  const PAISES_FILTRO = <?php echo json_encode($paisesConProductos, JSON_UNESCAPED_UNICODE); ?>;
  const PAISES_TODOS  = <?php echo json_encode($todosLosPaises,      JSON_UNESCAPED_UNICODE); ?>;
  const PAIS_FILTRO_ACTUAL = <?php echo $paisFiltro; ?>;

  // ── Cache global de banderas { "Colombia": "https://..." } ───────────────
  const flagCache = {};

  async function fetchFlags(nombres) {
    const nuevos = nombres.filter(n => n && !(n in flagCache));
    if (!nuevos.length) return;
    await Promise.all(nuevos.map(async nombre => {
      try {
        const res  = await fetch(`https://restcountries.com/v3.1/name/${encodeURIComponent(nombre)}?fields=flags,name`);
        if (!res.ok) throw new Error();
        const data = await res.json();
        flagCache[nombre] = data[0]?.flags?.png ?? '';
      } catch {
        flagCache[nombre] = '';
      }
    }));
  }

  // Crear elemento fallback (icono FA) para cuando no hay bandera
  function makeFlagFallback(cls) {
    const span = document.createElement('span');
    span.className = cls;
    span.innerHTML = '<i class="fa-solid fa-flag"></i>';
    return span;
  }

  // ── Aplicar banderas a <img data-pais> de la tabla ───────────────────────
  async function applyTableFlags() {
    const imgs = document.querySelectorAll('img.flag[data-pais]');
    if (!imgs.length) return;
    const nombres = [...new Set([...imgs].map(i => i.dataset.pais))];
    await fetchFlags(nombres);
    imgs.forEach(img => {
      const url = flagCache[img.dataset.pais];
      if (url) {
        img.src = url;
        img.classList.remove('flag-loading');
        // fallback si la imagen falla al cargar (onerror)
        img.onerror = () => {
          const fb = makeFlagFallback('flag-fallback-table');
          img.replaceWith(fb);
        };
      } else {
        const fb = makeFlagFallback('flag-fallback-table');
        img.replaceWith(fb);
      }
    });
  }

  // ── Custom Select ─────────────────────────────────────────────────────────
  // Aplica banderas a todas las opciones de un cs-wrap
  async function applyFlagsToCustomSelect(wrap) {
    const opts  = wrap.querySelectorAll('.cs-option[data-nombre]');
    const names = [...new Set([...opts].map(o => o.dataset.nombre))];
    await fetchFlags(names);
    opts.forEach(opt => {
      const img = opt.querySelector('.cs-opt-flag');
      if (!img) return;
      const url = flagCache[opt.dataset.nombre] ?? '';
      if (url) {
        img.src = url;
        img.style.display = 'inline-block';
        img.onerror = () => {
          const fb = makeFlagFallback('flag-fallback');
          img.replaceWith(fb);
        };
      } else {
        // Sin URL — reemplazar img por icono FA
        const fb = makeFlagFallback('flag-fallback');
        img.replaceWith(fb);
      }
      opt.dataset.flag = url;
    });
  }

  // Seleccionar una opción en un custom select
  function csSelect(wrap, nativo, value, nombre, flagUrl) {
    const triggerFlag  = wrap.querySelector('.cs-trigger .cs-flag');
    const triggerLabel = wrap.querySelector('.cs-trigger .cs-label');

    // Limpiar icono fallback previo si existía
    const prevFb = wrap.querySelector('.cs-trigger .flag-fallback');
    if (prevFb) prevFb.remove();

    if (flagUrl) {
      triggerFlag.src = flagUrl;
      triggerFlag.style.display = 'inline-block';
      triggerFlag.onerror = () => {
        triggerFlag.style.display = 'none';
        const fb = makeFlagFallback('flag-fallback');
        triggerFlag.insertAdjacentElement('afterend', fb);
      };
    } else if (value && value !== '0') {
      triggerFlag.style.display = 'none';
      const fb = makeFlagFallback('flag-fallback');
      triggerFlag.insertAdjacentElement('afterend', fb);
    } else {
      triggerFlag.style.display = 'none';
    }

    triggerLabel.textContent = nombre;
    wrap.querySelectorAll('.cs-option').forEach(o => o.classList.remove('cs-active'));
    const active = wrap.querySelector(`.cs-option[data-value="${value}"]`);
    if (active) active.classList.add('cs-active');
    if (nativo) nativo.value = value;
    wrap.classList.remove('cs-open');
  }

  // Inicializar un custom select
  async function initCustomSelect(wrapId, nativoId, initialValue) {
    const wrap   = document.getElementById(wrapId);
    const nativo = nativoId ? document.getElementById(nativoId) : null;
    if (!wrap) return;

    // Cargar banderas
    await applyFlagsToCustomSelect(wrap);

    // Selección inicial
    if (initialValue) {
      const opt = wrap.querySelector(`.cs-option[data-value="${initialValue}"]`);
      if (opt) csSelect(wrap, nativo, initialValue, opt.dataset.nombre, opt.dataset.flag);
    }

    // Abrir/cerrar al hacer clic en el trigger
    wrap.querySelector('.cs-trigger').addEventListener('click', e => {
      e.stopPropagation();
      const isOpen = wrap.classList.toggle('cs-open');
      if (isOpen) {
        // Posicionar dropdown fijo bajo el trigger (necesario dentro de modales)
        const rect = wrap.querySelector('.cs-trigger').getBoundingClientRect();
        const dd   = wrap.querySelector('.cs-dropdown');
        dd.style.top   = (rect.bottom + 4) + 'px';
        dd.style.left  = rect.left + 'px';
        dd.style.width = rect.width + 'px';
        const search = wrap.querySelector('.cs-search');
        if (search) { search.value = ''; filterOptions(wrap, ''); search.focus(); }
      }
    });

    // Seleccionar opción
    wrap.addEventListener('click', e => {
      const opt = e.target.closest('.cs-option');
      if (!opt) return;
      csSelect(wrap, nativo, opt.dataset.value, opt.dataset.nombre, opt.dataset.flag);
      // Si es el filtro de toolbar, enviar el form
      if (wrapId === 'filterPaisCustom') {
        document.getElementById('toolbarForm').submit();
      }
    });

    // Búsqueda dentro del dropdown
    const search = wrap.querySelector('.cs-search');
    if (search) {
      search.addEventListener('input', e => {
        e.stopPropagation();
        filterOptions(wrap, e.target.value);
      });
      search.addEventListener('click', e => e.stopPropagation());
    }
  }

  function filterOptions(wrap, query) {
    const q = query.toLowerCase();
    wrap.querySelectorAll('.cs-option').forEach(opt => {
      const match = opt.dataset.nombre.toLowerCase().includes(q);
      opt.style.display = match ? '' : 'none';
    });
  }

  // Cerrar todos los dropdowns al hacer clic fuera
  document.addEventListener('click', () => {
    document.querySelectorAll('.cs-wrap.cs-open').forEach(w => w.classList.remove('cs-open'));
  });

  // ── Modales ───────────────────────────────────────────────────────────────
  function openModal(id)  { document.getElementById(id).classList.add('active'); }
  function closeModal(id) { document.getElementById(id).classList.remove('active'); }

  document.addEventListener('click', e => {
    const openTarget = e.target.closest('[data-open]');
    if (openTarget) {
      const modalId = openTarget.dataset.open;
      openModal(modalId);

      if (modalId === 'modalEditar') {
        const d = openTarget.dataset;
        document.getElementById('edit-id').value     = d.id;
        document.getElementById('edit-nombre').value = d.nombre;
        document.getElementById('edit-precio').value = d.precio;
        document.getElementById('edit-stock').value  = d.stock;
        // Sincronizar custom select de editar
        const wrap = document.getElementById('editPaisCustom');
        const opt  = wrap?.querySelector(`.cs-option[data-value="${d.pais}"]`);
        if (opt) csSelect(wrap, document.getElementById('editPaisNativo'), d.pais, opt.dataset.nombre, opt.dataset.flag);
      }

      if (modalId === 'modalEliminar') {
        document.getElementById('del-id').value = openTarget.dataset.id;
        document.getElementById('del-nombre').textContent = openTarget.dataset.nombre;
      }
    }

    const closeTarget = e.target.closest('[data-close]');
    if (closeTarget) closeModal(closeTarget.dataset.close);

    if (e.target.classList.contains('modal-overlay')) {
      e.target.classList.remove('active');
    }
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
      document.querySelectorAll('.cs-wrap.cs-open').forEach(w => w.classList.remove('cs-open'));
    }
  });

  // ── Inicialización ────────────────────────────────────────────────────────
  (async () => {
    // Banderas de la tabla
    await applyTableFlags();

    // Custom selects (en paralelo)
    await Promise.all([
      initCustomSelect('filterPaisCustom', 'filterPaisNativo', PAIS_FILTRO_ACTUAL || null),
      initCustomSelect('crearPaisCustom',  'crearPaisNativo',  null),
      initCustomSelect('editPaisCustom',   'editPaisNativo',   null),
    ]);
  })();
  </script>

</body>
</html>
