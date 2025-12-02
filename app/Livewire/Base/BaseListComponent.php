<?php

namespace App\Livewire\Base;

use Livewire\Component;
use Livewire\WithPagination;

/**
 * Base component cho tất cả danh sách (List components)
 * Chứa logic chung: filter, sort, search, pagination
 */
abstract class BaseListComponent extends Component
{
    use WithPagination;

    // Search
    public $search = '';
    
    // Pagination
    public $perPage = 15;
    
    // Sorting
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Date Range Filter
    public $dateFrom = '';
    public $dateTo = '';
    
    // Query string để lưu trạng thái filter vào URL
    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    /**
     * Reset trang về 1 khi search thay đổi
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset trang về 1 khi perPage thay đổi
     */
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Sắp xếp theo cột
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            // Toggle direction nếu đang sort cùng cột
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Sort mới, mặc định desc
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    /**
     * Reset tất cả filter về mặc định
     */
    public function resetFilters()
    {
        $this->reset([
            'search',
            'perPage',
            'sortField',
            'sortDirection',
            'dateFrom',
            'dateTo'
        ]);
        
        $this->resetPage();
    }

    /**
     * Áp dụng search cho query builder
     */
    protected function applySearch($query, array $searchFields)
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'LIKE', '%' . $this->search . '%');
            }
        });
    }

    /**
     * Áp dụng sort cho query builder
     */
    protected function applySort($query)
    {
        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Áp dụng date range filter
     */
    protected function applyDateFilter($query, $dateField = 'created_at')
    {
        if (!empty($this->dateFrom)) {
            $query->whereDate($dateField, '>=', $this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $query->whereDate($dateField, '<=', $this->dateTo);
        }

        return $query;
    }

    /**
     * Set preset date range
     */
    public function setDateRange($preset)
    {
        $now = now();
        
        switch ($preset) {
            case 'today':
                $this->dateFrom = $now->toDateString();
                $this->dateTo = $now->toDateString();
                break;
            
            case 'yesterday':
                $this->dateFrom = $now->subDay()->toDateString();
                $this->dateTo = $now->toDateString();
                break;
            
            case '7days':
                $this->dateFrom = $now->subDays(6)->toDateString();
                $this->dateTo = now()->toDateString();
                break;
            
            case '30days':
                $this->dateFrom = $now->subDays(29)->toDateString();
                $this->dateTo = now()->toDateString();
                break;
            
            case 'this_month':
                $this->dateFrom = $now->startOfMonth()->toDateString();
                $this->dateTo = now()->toDateString();
                break;
            
            case 'last_month':
                $this->dateFrom = $now->subMonth()->startOfMonth()->toDateString();
                $this->dateTo = $now->subMonth()->endOfMonth()->toDateString();
                break;
        }
        
        $this->resetPage();
    }

    /**
     * Abstract method - phải implement ở child class
     */
    abstract public function render();
}
