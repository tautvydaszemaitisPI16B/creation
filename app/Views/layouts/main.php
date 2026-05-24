<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

<div class="app-shell">
    <header>
        <div class="logo">
            <span class="logo-mark">Creation</span>
            <span class="logo-tag">v1.0</span>
        </div>
        <span class="header-meta"><?= implode(' · ', $supportedFormats) ?></span>
    </header>

    <main>

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <div>
                <div class="sidebar-section-label">Įkelti failą</div>
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 15V3m0 0L8 7m4-4l4 4M2 17l.621 2.485A2 2 0 004.561 21h14.878a2 2 0 001.94-1.515L22 17"/></svg>
                    </div>
                    <div class="upload-title">Pasirinkite failą</div>
                    <div class="upload-hint">arba nuvilkite čia</div>
                    <div class="format-chips">
                        <?php foreach ($supportedFormats as $format): ?>
                            <span class="chip chip-<?= strtolower($format) ?>">
                                .<?= strtolower($format) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <input type="file" style="position:absolute;inset:0;opacity:0;cursor:pointer;" accept=".csv,.xml,.json" id="fileInput">
                </div>
            </div>

            <!-- ERROR (shown on validation fail) -->
            <div class="error-box" id="errorBox" style="display:none;">
                <svg class="error-icon" viewBox="0 0 24 24" fill="none" stroke="#8b3a2a" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <div class="error-text" style="font-weight:500;margin-bottom:2px;" id="errorTitle">Netinkamas failas</div>
                    <div class="error-text" id="errorMsg">Palaikomi formatai: <?= implode(' , ', $supportedFormats) ?>. Patikrinkite failo plėtinį ir struktūrą.</div>
                </div>
            </div>

        </aside>

        <!-- CONTENT -->
        <section class="content">

            <div class="content-header">
                <div>
                    <div class="content-title" id="tableTitle">Pasirinkite failą</div>
                    <div class="content-subtitle" id="tableSubtitle">Įkelkite <?= implode(' , ', $supportedFormats) ?> failą kairėje</div>
                </div>
                <div class="toolbar" id="toolbar" style="display:none;">

                </div>
            </div>

            <!-- SEARCH -->
            <div class="search-bar" id="searchBar" style="display:none;">

            </div>

            <!-- TABLE or EMPTY -->
            <div id="emptyState">
                <div class="empty-state">
                    <div class="empty-glyph">∅</div>
                    <div class="empty-title">Duomenų nėra</div>
                    <div class="empty-hint">Įkelkite <?= implode(' , ', $supportedFormats) ?> failą, kad būtų atvaizduoti duomenys.</div>
                </div>
            </div>

            <div id="tableWrap" style="display:none;">
                <div class="table-wrap">
                    <table id="dataTable">
                        <thead id="tableHead"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>

                </div>
            </div>

        </section>

    </main>

</div>

<script>
    let table_data = '';
    let filename = 'duomenys.csv';
    function renderTable(data) {
        const datatable = document.getElementById('dataTable');
        datatable.innerHTML = data;
    }

    function showDemo() {
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('tableWrap').style.display = 'block';
        document.getElementById('toolbar').style.display = 'flex';
        document.getElementById('searchBar').style.display = 'block';
        document.getElementById('tableTitle').textContent = filename;
        document.getElementById('tableSubtitle').textContent = '';
        renderTable(table_data);
    }

    async function uploadFile(f) {
        const formData = new FormData();
        formData.append('file', f);

        try {
            const response = await axios.post('/upload', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: function (progressEvent) {
                    const percent = Math.round(
                        (progressEvent.loaded * 100) / progressEvent.total
                    );
                    console.log('Upload:', percent + '%');
                }
            });

            console.log('Server response:', response.data);
            table_data = response.data.table;
            filename = response.data.filename;
            console.log(response.data.table);

            showDemo();

        } catch (err) {
            console.error(err);

            document.getElementById('errorBox').style.display = 'flex';
        }
    }

    document.getElementById('fileInput').addEventListener('change', function(e) {
        if (e.target.files.length) {
            const f = e.target.files[0];
            const ext = f.name.split('.').pop().toLowerCase();
            const supportedFormats = <?= json_encode($supportedFormats) ?>;

            const valid = supportedFormats.includes(ext);

            if (!valid) {
                document.getElementById('errorBox').style.display = 'flex';
            } else {
                uploadFile(f);
                document.getElementById('errorBox').style.display = 'none';
                showDemo();
            }
        }
    });

    document.getElementById('uploadZone').addEventListener('dragover', e => {
        e.preventDefault();
        document.getElementById('uploadZone').style.background = 'var(--accent-light)';
        document.getElementById('uploadZone').style.borderColor = 'var(--accent-mid)';
    });

    document.getElementById('uploadZone').addEventListener('dragleave', () => {
        document.getElementById('uploadZone').style.background = 'var(--paper)';
        document.getElementById('uploadZone').style.borderColor = 'var(--paper-3)';
    });
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;1,9..144,300&family=DM+Sans:wght@300;400;500&display=swap');

    * { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --ink: #1a1916;
        --ink-2: #4a4944;
        --ink-3: #8a8880;
        --paper: #f7f5f0;
        --paper-2: #eeece6;
        --paper-3: #e4e1d8;
        --accent: #2d5a3d;
        --accent-light: #e8f2eb;
        --accent-mid: #4a8a5e;
        --warn: #8b3a2a;
        --warn-light: #f5ede9;
        --radius: 4px;
        --radius-lg: 8px;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--paper);
        color: var(--ink);
        min-height: 100vh;
        padding: 0;
    }

    .app-shell {
        display: grid;
        grid-template-rows: auto 1fr;
        min-height: 100vh;
    }

    /* HEADER */
    header {
        background: var(--ink);
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 56px;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .logo {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .logo-mark {
        font-family: 'Fraunces', serif;
        font-size: 20px;
        font-weight: 500;
        color: #f7f5f0;
        letter-spacing: -0.5px;
    }

    .logo-tag {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--accent-mid);
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .header-meta {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: #666;
        letter-spacing: 1px;
    }

    /* MAIN */
    main {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 0;
        min-height: calc(100vh - 56px);
    }

    /* SIDEBAR */
    .sidebar {
        background: var(--paper-2);
        border-right: 1px solid var(--paper-3);
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .sidebar-section-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--ink-3);
        margin-bottom: 12px;
    }

    /* UPLOAD ZONE */
    .upload-zone {
        border: 1.5px dashed var(--paper-3);
        border-radius: var(--radius-lg);
        padding: 2rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--paper);
        position: relative;
    }

    .upload-zone:hover {
        border-color: var(--accent-mid);
        background: var(--accent-light);
    }

    .upload-icon {
        width: 40px;
        height: 40px;
        margin: 0 auto 12px;
        background: var(--paper-3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-icon svg {
        width: 20px;
        height: 20px;
        stroke: var(--ink-2);
        fill: none;
        stroke-width: 1.5;
    }

    .upload-title {
        font-size: 13px;
        font-weight: 500;
        color: var(--ink);
        margin-bottom: 4px;
    }

    .upload-hint {
        font-size: 11px;
        color: var(--ink-3);
        line-height: 1.5;
    }

    .format-chips {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 12px;
        justify-content: center;
    }

    .chip {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 2px;
        border: 1px solid;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .chip-csv { background: #e8f2eb; border-color: #4a8a5e; color: #2d5a3d; }
    .chip-xml { background: #fdf0e8; border-color: #c87941; color: #8b5228; }
    .chip-json { background: #e8edf8; border-color: #4a6ab5; color: #2d4580; }

    /* ERROR BOX */
    .error-box {
        background: var(--warn-light);
        border: 1px solid #e8b5aa;
        border-radius: var(--radius);
        padding: 12px;
        display: flex;
        gap: 8px;
        align-items: flex-start;
    }

    .error-icon {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .error-text {
        font-size: 12px;
        color: var(--warn);
        line-height: 1.5;
    }

    /* FILE INFO CARD */
    .file-card {
        background: var(--paper);
        border: 1px solid var(--paper-3);
        border-radius: var(--radius);
        padding: 12px;
    }

    .file-card-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        border-bottom: 1px solid var(--paper-2);
    }

    .file-card-row:last-child { border-bottom: none; }

    .file-label {
        font-size: 11px;
        color: var(--ink-3);
        font-family: 'DM Mono', monospace;
        letter-spacing: 0.5px;
    }

    .file-value {
        font-size: 12px;
        font-weight: 500;
        color: var(--ink);
    }

    .file-value.mono { font-family: 'DM Mono', monospace; }

    .status-badge {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 2px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .status-ok { background: var(--accent-light); color: var(--accent); border: 1px solid #b5d9be; }
    .status-err { background: var(--warn-light); color: var(--warn); border: 1px solid #e8b5aa; }

    /* CONTENT AREA */
    .content {
        padding: 2rem;
        overflow: auto;
    }

    .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--paper-3);
    }

    .content-title {
        font-family: 'Fraunces', serif;
        font-size: 22px;
        font-weight: 300;
        color: var(--ink);
        letter-spacing: -0.5px;
    }

    .content-subtitle {
        font-size: 13px;
        color: var(--ink-3);
        margin-top: 2px;
    }

    .toolbar {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn {
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        font-weight: 500;
        padding: 7px 14px;
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.15s;
        border: 1px solid;
        letter-spacing: 0.2px;
    }

    .btn-ghost {
        background: transparent;
        border-color: var(--paper-3);
        color: var(--ink-2);
    }

    .btn-ghost:hover {
        background: var(--paper-2);
        border-color: var(--ink-3);
    }

    .btn-primary {
        background: var(--ink);
        border-color: var(--ink);
        color: var(--paper);
    }

    .btn-primary:hover { background: #333; }

    /* SEARCH BAR */
    .search-bar {
        position: relative;
        margin-bottom: 1rem;
    }

    .search-input {
        width: 100%;
        padding: 9px 12px 9px 36px;
        border: 1px solid var(--paper-3);
        border-radius: var(--radius);
        background: var(--paper);
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        color: var(--ink);
        outline: none;
        transition: border-color 0.15s;
    }

    .search-input:focus { border-color: var(--ink-3); }

    .search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        width: 14px;
        height: 14px;
        stroke: var(--ink-3);
        fill: none;
        stroke-width: 2;
    }

    /* TABLE */
    .table-wrap {
        border: 1px solid var(--paper-3);
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: white;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    thead {
        background: var(--ink);
        position: sticky;
        top: 0;
    }

    thead th {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #aaa;
        padding: 11px 16px;
        text-align: left;
        font-weight: 400;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
    }

    thead th:hover { color: #fff; }

    .th-inner {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sort-arrow {
        width: 8px;
        height: 8px;
        opacity: 0.4;
    }

    tbody tr {
        border-bottom: 1px solid var(--paper-2);
        transition: background 0.1s;
    }

    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: var(--paper); }

    tbody td {
        padding: 11px 16px;
        color: var(--ink);
        vertical-align: middle;
    }

    .td-mono {
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        color: var(--ink-2);
    }

    .row-num {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--ink-3);
        user-select: none;
    }

    /* PAGINATION */
    .pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-top: 1px solid var(--paper-2);
        background: var(--paper);
    }

    .page-info {
        font-size: 12px;
        color: var(--ink-3);
        font-family: 'DM Mono', monospace;
    }

    .page-nav {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .page-btn {
        width: 30px;
        height: 30px;
        border-radius: var(--radius);
        border: 1px solid var(--paper-3);
        background: white;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ink-2);
        transition: all 0.1s;
        font-family: 'DM Mono', monospace;
    }

    .page-btn:hover { background: var(--paper-2); }
    .page-btn.active { background: var(--ink); color: white; border-color: var(--ink); }

    /* EMPTY STATE */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 5rem 2rem;
        text-align: center;
        color: var(--ink-3);
    }

    .empty-glyph {
        font-family: 'Fraunces', serif;
        font-size: 64px;
        font-weight: 300;
        opacity: 0.15;
        line-height: 1;
        margin-bottom: 1rem;
        color: var(--ink);
    }

    .empty-title {
        font-family: 'Fraunces', serif;
        font-size: 18px;
        font-weight: 300;
        color: var(--ink-2);
        margin-bottom: 8px;
    }

    .empty-hint {
        font-size: 13px;
        color: var(--ink-3);
        max-width: 280px;
        line-height: 1.6;
    }

    /* RESPONSIVE */
    @media (max-width: 640px) {
        main {
            grid-template-columns: 1fr;
            grid-template-rows: auto 1fr;
        }

        .sidebar {
            border-right: none;
            border-bottom: 1px solid var(--paper-3);
            padding: 1.25rem;
        }

        .content { padding: 1.25rem; }

        header {
            padding: 0 1.25rem;
        }

        .content-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>

<!--<main>-->
<!--    --><?php //= $content ?>
<!--</main>-->