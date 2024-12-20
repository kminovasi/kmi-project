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
}
