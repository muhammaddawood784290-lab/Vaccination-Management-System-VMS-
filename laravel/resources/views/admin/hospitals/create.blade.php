@extends('admin.layouts.dashboard')
@section('page-title', 'Add Hospital')
@section('content')
<a href="{{ route('admin.hospitals.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center gap-1">&larr; Back to Hospitals</a>
<div class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">
    <h2 class="text-xl font-semibold text-gray-900 mb-5">Add New Hospital</h2>
    <form method="POST" action="{{ route('admin.hospitals.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Hospital name *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">@error('name')<p class="text-
