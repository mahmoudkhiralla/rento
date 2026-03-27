<style>
    :root {
        --primary: #4A6CF7;
        --secondary: #6c757d;
        --background: #f4f6f9;
        --card-bg: #ffffff;
        --radius: 14px;
        --shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        --transition: .25s ease;
    }

    /* Global */
    body {
        background: var(--background);
        font-family: "Cairo", sans-serif;
    }

    .card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        margin-bottom: 20px;
        transition: var(--transition);
    }

    .card:hover {
        transform: translateY(-3px);
    }

    /* Buttons */
    .btn-primary-custom {
        background: var(--primary);
        border: none;
        color: white;
        padding: 10px 18px;
        border-radius: 10px;
        transition: var(--transition);
    }

    .btn-primary-custom:hover {
        background: #3d5ae0;
    }

    /* Table */
    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-custom thead th {
        background: var(--primary);
        color: #fff;
        padding: 12px;
        border-radius: 8px;
    }

    .table-custom tbody tr {
        background: #fff;
        box-shadow: var(--shadow);
    }

    .table-custom tbody td {
        padding: 15px;
    }

    /* Form */
    .form-control-custom {
        border: 1px solid #ddd;
        padding: 12px;
        border-radius: 10px;
        transition: var(--transition);
    }

    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.2);
        outline: none;
    }

</style>
