@extends('layouts.app')

@section('title', 'Etkinlik Takibi - GMaps CRM')
@section('page-title', 'Etkinlik Takibi')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    <!-- Companies List -->
    <div class="lg:w-1/3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Firmalar
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Etkinliklerini görmek istediğiniz firmayı seçin</p>
            </div>
            
            <!-- Search -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="companySearch" placeholder="Firma ara..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark:text-white text-sm">
                </div>
            </div>
            
            <!-- Companies -->
            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[600px] overflow-y-auto" id="companiesList">
                @foreach($companies as $company)
                <a href="{{ route('activities.index', ['company_id' => $company->id]) }}" 
                   class="company-item flex items-center p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
                          {{ $selectedCompany && $selectedCompany->id === $company->id ? 'bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-600' : '' }}"
                   data-name="{{ strtolower($company->name) }}">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-sm"
                         style="background-color: {{ $company->status->color ?? '#6B7280' }}">
                        {{ $company->initials }}
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="font-medium text-gray-800 dark:text-white truncate">{{ $company->name }}</p>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  style="background-color: {{ $company->status->color ?? '#6B7280' }}20; color: {{ $company->status->color ?? '#6B7280' }}">
                                {{ $company->status->name ?? 'Bilinmiyor' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $company->activities_count }} etkinlik</span>
                        </div>
                    </div>
                    @if($selectedCompany && $selectedCompany->id === $company->id)
                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Activity Details -->
    <div class="lg:w-2/3">
        @if($selectedCompany)
        <div class="space-y-6">
            <!-- Company Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-xl"
                             style="background-color: {{ $selectedCompany->status->color ?? '#6B7280' }}">
                            {{ $selectedCompany->initials }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $selectedCompany->name }}</h2>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="px-2 py-0.5 text-sm rounded-full"
                                      style="background-color: {{ $selectedCompany->status->color ?? '#6B7280' }}20; color: {{ $selectedCompany->status->color ?? '#6B7280' }}">
                                    {{ $selectedCompany->status->name ?? 'Bilinmiyor' }}
                                </span>
                                @if($selectedCompany->phone)
                                <span class="text-sm text-gray-500">📞 {{ $selectedCompany->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('companies.show', $selectedCompany) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800">
                        Tam Detay →
                    </a>
                </div>

                @if($selectedCompany->address)
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        📍 {{ $selectedCompany->address }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Add New Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">+ Etkinlik Ekle</h3>
                <form action="{{ route('activities.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tip</label>
                            <select name="type" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500">
                                @foreach($activityTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Başlık</label>
                            <input type="text" name="title" required
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                                   placeholder="Etkinlik başlığı">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Açıklama (Opsiyonel)</label>
                        <textarea name="description" rows="2"
                                  class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-blue-500"
                                  placeholder="Detaylar..."></textarea>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Etkinlik Ekle
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Etkinlik Geçmişi</h3>
                    <span class="text-sm text-gray-500">{{ $activities->total() }} etkinlik</span>
                </div>
                
                <div class="p-4">
                    @forelse($activities as $activity)
                    <div class="flex items-start space-x-4 mb-4 last:mb-0">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        @if($activity->type === 'search') bg-blue-100 dark:bg-blue-900/50
                                        @elseif($activity->type === 'call') bg-green-100 dark:bg-green-900/50
                                        @elseif($activity->type === 'meeting') bg-purple-100 dark:bg-purple-900/50
                                        @elseif($activity->type === 'proposal') bg-amber-100 dark:bg-amber-900/50
                                        @elseif($activity->type === 'email') bg-indigo-100 dark:bg-indigo-900/50
                                        @else bg-gray-100 dark:bg-gray-700 @endif">
                                @if($activity->type === 'search')
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                @elseif($activity->type === 'call')
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                @elseif($activity->type === 'meeting')
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                @elseif($activity->type === 'proposal')
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif($activity->type === 'email')
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                @endif
                            </div>
                            @if(!$loop->last)
                            <div class="absolute top-10 left-1/2 -translate-x-1/2 w-0.5 h-full bg-gray-200 dark:bg-gray-700"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $activity->title }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $activity->typeInfo['label'] }} • {{ $activity->created_at->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                                @if($activity->status === 'pending')
                                <form action="{{ route('activities.complete', $activity) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs px-3 py-1 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition">
                                        Tamamla
                                    </button>
                                </form>
                                @else
                                <span class="text-xs px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">
                                    Tamamlandı
                                </span>
                                @endif
                            </div>
                            @if($activity->description)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                                {{ $activity->description }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Bu firma için henüz etkinlik yok.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Yukarıdan yeni bir etkinlik ekleyin.</p>
                    </div>
                    @endforelse
                </div>

                @if($activities->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $activities->appends(['company_id' => $selectedCompany->id])->links() }}
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Firma Seçin</h3>
            <p class="text-gray-500 dark:text-gray-400">Etkinliklerini görmek ve yeni etkinlik eklemek için soldaki listeden bir firma seçin.</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Company search filter
    document.getElementById('companySearch')?.addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('.company-item').forEach(function(item) {
            const name = item.dataset.name;
            if (name.includes(search)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection
