<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Patients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0092C5;
            --primary-light: #00AAD8;
            --primary-dark: #007AAB;
            --light-bg: #F8FBFE;
        }

        body {
            background-color: var(--light-bg);
            color: #2D3748;
        }
        .hidden {
            display: none; !important
        }
        .container {
            padding: 2rem;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 146, 197, 0.1);
        }

        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-control, .form-select {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 146, 197, 0.1);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--light-bg);
            color: var(--primary);
            font-weight: 600;
            border-bottom: 2px solid #E2E8F0;
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: #718096;
        }
        .btn-success {
            background: #10B981;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-success:hover {
            background: #059669;
        }
        .btn-success:hover,
        .btn-primary:hover,
        .btn-secondary:hover {
            transform: translateY(-1px);
        }
   
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2 class="m-0">Liste des Patients</h2>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('patients_list.display') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="uid" class="form-label">UID</label>
                        <input type="text" id="uid" name="uid" class="form-control" value="{{ request('uid') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="sex" class="form-label">Sexe</label>
                        <select id="sex" name="sex" class="form-select">
                            <option value="">Tous</option>
                            <option value="M" {{ request('sex') === 'M' ? 'selected' : '' }}>Homme</option>
                            <option value="F" {{ request('sex') === 'F' ? 'selected' : '' }}>Femme</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Date de Début</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Date de Fin</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('patients_list.display') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Réinitialiser
                        </a>
                     
                        <a href="{{ route('patients.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Exporter en Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>UID</th>
                        <th>Sexe</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date de Naissance</th>
                        <th>Téléphone</th>
                        <th>Groupe Sanguin</th>
                        <th>CIN</th>
                        <th>Couverture</th>
                        <th>Type de Couverture</th>
                        <th>Numéro de Couverture</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $patient->uid }}</td>
                            <td>{{ $patient->sex }}</td>
                            <td>{{ $patient->name }}</td>
                            <td>{{ $patient->surname }}</td>
                            <td>{{ $patient->date_of_birth }}</td>
                            <td>{{ $patient->phone }}</td>
                            <td>{{ $patient->blood_type }}</td>
                            <td>{{ $patient->CIN }}</td>
                            <td>{{ $patient->coverage ? 'Oui' : 'Non' }}</td>
                            <td>{{ $patient->coverage_type ?? 'N/A' }}</td>
                            <td>{{ $patient->coverage_number }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="empty-state">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="m-0">Aucun patient trouvé.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $patients->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
