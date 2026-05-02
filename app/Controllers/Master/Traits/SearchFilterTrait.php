<?php

namespace App\Controllers\Master\Traits;

trait SearchFilterTrait
{
    /**
     * Get filter params from query string
     */
    protected function getFilterParams(array $filterFields): array
    {
        $params = [];
        $search = $this->request->getGet('q');
        if (!empty($search)) {
            $params['q'] = $search;
        }

        foreach ($filterFields as $field) {
            $value = $this->request->getGet($field);
            if ($value !== null && $value !== '') {
                $params[$field] = $value;
            }
        }

        $isActive = $this->request->getGet('is_active');
        if ($isActive !== null && $isActive !== '') {
            $params['is_active'] = (int)$isActive;
        }

        return $params;
    }

    /**
     * Build query string for pagination
     */
    protected function buildSearchQuery(array $params): string
    {
        if (empty($params)) {
            return '';
        }
        $queryParts = [];
        foreach ($params as $key => $value) {
            $queryParts[] = urlencode($key) . '=' . urlencode($value);
        }
        return '&' . implode('&', $queryParts);
    }

    /**
     * Apply search and filters to query builder
     */
    protected function applySearchFilters($builder, array $searchFields, array $params): void
    {
        if (!empty($params['q'])) {
            $builder->groupStart();
            foreach ($searchFields as $field) {
                $builder->orLike($field, $params['q'], 'both');
            }
            $builder->groupEnd();
        }
    }

    /**
     * Get filter options data for view (e.g., unit kerja list for dropdown)
     */
    protected function getFilterOptions(): array
    {
        return [];
    }

    /**
     * Render search/filter bar HTML
     */
    protected function renderSearchBar(array $config, array $params): string
    {
        $filterOptions = $this->getFilterOptions();
        $actionUrl = $config['base_url'] ?? base_url();
        $searchFields = $config['search_fields'] ?? [];
        $filterFields = $config['filter_fields'] ?? [];
        $placeholder = $config['placeholder'] ?? 'Cari...';

        $html = '<form method="get" action="' . $actionUrl . '" class="search-filter-form mb-3">';
        $html .= '<div class="row g-2 align-items-end">';

        // Search input
        $html .= '<div class="col-md-4">';
        $html .= '<input type="text" name="q" class="form-control" ';
        $html .= 'value="' . esc($params['q'] ?? '') . '" ';
        $html .= 'placeholder="' . esc($placeholder) . '">';
        $html .= '</div>';

        // Filter dropdowns
        foreach ($filterFields as $filter) {
            $html .= '<div class="col-md-2">';
            $html .= '<select name="' . esc($filter['name']) . '" class="form-select">';
            $html .= '<option value="">' . esc($filter['label']) . '</option>';

            $options = $filterOptions[$filter['options_key']] ?? [];
            foreach ($options as $opt) {
                $selected = ($params[$filter['name']] ?? '') == $opt['value'] ? 'selected' : '';
                $html .= '<option value="' . esc($opt['value']) . '" ' . $selected . '>' . esc($opt['label']) . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';
        }

        // Status filter
        if ($config['show_status_filter'] ?? false) {
            $html .= '<div class="col-md-2">';
            $html .= '<select name="is_active" class="form-select">';
            $html .= '<option value="">Status</option>';
            $selectedActive = ($params['is_active'] ?? '') === '1' ? 'selected' : '';
            $selectedInactive = ($params['is_active'] ?? '') === '0' ? 'selected' : '';
            $html .= '<option value="1" ' . $selectedActive . '>Aktif</option>';
            $html .= '<option value="0" ' . $selectedInactive . '>Tidak Aktif</option>';
            $html .= '</select>';
            $html .= '</div>';
        }

        // Buttons
        $html .= '<div class="col-md-auto">';
        $html .= '<button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>';
        if (!empty($params)) {
            $html .= ' <a href="' . $actionUrl . '" class="btn btn-secondary">Reset</a>';
        }
        $html .= '</div>';

        $html .= '</div></form>';

        return $html;
    }
}