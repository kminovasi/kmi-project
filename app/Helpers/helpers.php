<?php

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status)
    {
        $statusColors = [
            'not finish' => 'bg-secondary',
            'upload full paper' => 'bg-primary',
            'accepted paper by facilitator' => 'bg-success',
            'rejected paper by facilitator' => 'bg-danger',
            'upload benefit' => 'bg-warning',
            'accepted benefit by facilitator' => 'bg-success',
            'rejected benefit by facilitator' => 'bg-danger',
            'accepted benefit by general manager' => 'bg-success',
            'rejected benefit by general manager' => 'bg-danger',
            'accepted by innovation admin' => 'bg-success',
            'rejected by innovation admin' => 'bg-danger',
            'replicate' => 'bg-info',
            'not complete' => 'bg-secondary',
            'rollback paper' => 'bg-dark',
            'rollback benefit' => 'bg-dark',
        ];

        return $statusColors[$status] ?? 'bg-secondary';
    }

    if (!function_exists('getAssessmentStatusBadgeClass')) {
        function getAssessmentStatusBadgeClass($status)
        {
            $statusColors = [
                'On Desk' => 'bg-primary',
                'Presentation' => 'bg-info',
                'tidak lolos On Desk' => 'bg-danger',
                'tidak lolos Presentation' => 'bg-danger',
                'Lolos Presentation' => 'bg-success',
                'Tidak lolos Caucus' => 'bg-danger',
                'Caucus' => 'bg-warning',
                'Presentation BOD' => 'bg-dark',
                'Juara' => 'bg-success',
            ];

            return $statusColors[$status] ?? 'bg-secondary';
        }
    }
}

// Vite asset helper
if (!function_exists('vite_assets')) {
    function vite_assets($path)
    {
        $manifestPath = public_path('build/manifest.json');
        if(!file_exists($manifestPath)) {
            return asset($path);
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (isset($manifest[$path])) {
            throw new Exception("Vite asset for [$path] not found in manifest.");
        }

        return asset('build/' . $manifest[$path]['file']);
    }
}