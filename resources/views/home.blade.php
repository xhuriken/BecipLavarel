@extends('layouts.app')

@section('content')
<div class="container-page min-width">
    @if (auth()->user()->role == 'engineer')
        <a href='{{ route('usermanager') }}' class='btn-return'>Gérer les utilisateurs</a>
    @endif
    @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
        <a href="{{ route('projects.generate', [500, date('Y')]) }}" class="btn-mask">Générer 500 affaires cette année</a>
        <a href="{{ route('projects.generate', [500, date('Y')+1]) }}" class="btn-mask">Générer 500 affaire l'année suivante</a>
        <button id="toggleButton" class="btn-mask">Ajouter une affaire manuellement</button>

        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <label>Entreprise</label>
            <select class="custom-select" name="company_id" required>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">
                        {{$company->name}}
                    </option>
                @endforeach
            </select>

            <label>Ingénieur référent</label>
            <select class="custom-select" name="engineer_id">
                @foreach($engineers as $engineer)
                    <option value="{{ $engineer->id}}">
                        {{$engineer->name}}
                    </option>
                @endforeach
            </select>
            <label>Nom de l'affaire</label>
            <div class="nom-affaire-container">
                <span class="fixed-prefix">B</span>
                <input type="text" id="project_year" maxlength="2" pattern="\d{2}" required placeholder="00">
                <span class="fixed-dot">.</span>
                <input type="text" id="project_number" maxlength="3" pattern="\d{3}" required placeholder="000">
                <input type="hidden" name="project_name" id="project_name">
            </div>

            <label for="search-clients">Clients ayant accès</label>
            <select id="search-clients" name="clients[]" multiple="multiple">
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }} - {{ \App\Models\User::getCompanyName($client->company_id) }}</option>
                @endforeach
            </select>

            <button type="submit">Ajouter</button>
        </form>

    @endif
    <h2>Liste des affaires</h2>

    <table id="project-table" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Entreprise</th>
                <th>Nom de l'affaire</th>
                <th>Actions</th>
                @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                    <th data-label="Delete">
                        <i class="fa-solid fa-trash"></i>
                    </th>
                    <th>
                        <input type="checkbox" id="select-all">
                    </th>
                @endif
            </tr>
        </thead>
        <tbody>
        @forelse($projects as $project)
            <tr>
                <td>
                    @if($project->company_id == null)
                        Aucune
                    @else
                        {{ $project->getCompanyName($project->company_id) }}
                    @endif
                </td>
                <td>{{ $project->name }}</td>
                <td>
                    <a href="{{route('projects.project', $project)}}" class="btn-return">Voir</a>
                    @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                        <span class="responsiveSpan">|</span>
                        <a href="" class="btn-return">Modifier</a>
                    @endif
                </td>

                @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
                    <td class="icon-cell">
                        <a href="{{ route('projects.delete', $project) }}">
                            <i class="fa-solid fa-trash delete-icon"></i>
                        </a>
                    </td>
                    <td>
                        <input type="checkbox" class="delete-checkbox" data-project-id="{{ $project->id }}">
                    </td>
                @endif
            </tr>
        @empty
            <tr><td colspan="5">Aucune affaire trouvée</td></tr>
        @endforelse
        </tbody>
    </table>

    @if (auth()->user()->role == 'engineer' || auth()->user()->role == 'secretary')
        <button id="delete-selected" class="btn-filter" data-route="{{ route('projects.delete-selected') }}">
            Supprimer les affaires sélectionnées
        </button>
        <form action="{{ route('projects.delete-empty') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-filter">Supprimer les affaires vides</button>
        </form>
    @endif
</div>


@endsection
