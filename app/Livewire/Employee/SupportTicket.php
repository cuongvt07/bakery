<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.mobile')]
class SupportTicket extends Component
{
    public $subject = '';
    public $description = '';
    public $category = '';

    public function submit()
    {
        $this->validate([
            'subject' => 'required|min:5',
            'description' => 'required',
            'category' => 'required',
        ], [
            'subject.required' => 'Vui lòng nhập tiêu đề',
            'subject.min' => 'Tiêu đề phải ít nhất 5 ký tự',
            'description.required' => 'Vui lòng mô tả vấn đề',
            'category.required' => 'Vui lòng chọn danh mục',
        ]);

        // TODO: Create support ticket in database
        // For now, just flash success message
        
        session()->flash('message', 'Đã gửi ticket hỗ trợ! Chúng tôi sẽ phản hồi sớm nhất.');
        
        $this->reset(['subject', 'description', 'category']);
    }

    public function render()
    {
        return view('livewire.employee.support-ticket');
    }
}
