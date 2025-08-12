@extends('layouts.student')

@section('title', 'سجل التقدم')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">سجل التقدم في حفظ القرآن</h1>
    </div>
</div>

<div class="row mb-4">
    <!-- Progress Summary Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-gradient-warning">
                <h5 class="mb-0">ملخص التقدم</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <canvas id="progressChart" width="200" height="200"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h2 class="mb-0" id="progressPercentage">0%</h2>
                                <small class="text-muted">تم الإنجاز</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <h5>آخر ما تم حفظه</h5>
                                    @if($progressLogs->count() > 0)
                                        <p class="mb-1"><strong>السورة:</strong> {{ $progressLogs->first()->surah_name }}</p>
                                        <p class="mb-1"><strong>الآيات:</strong> {{ $progressLogs->first()->start_verse }} إلى {{ $progressLogs->first()->end_verse }}</p>
                                        <p class="mb-1"><strong>التاريخ:</strong> {{ $progressLogs->first()->date->format('Y-m-d') }}</p>
                                        <p class="mb-0"><strong>المعلم:</strong> {{ $progressLogs->first()->teacher->name }}</p>
                                    @else
                                        <p class="text-muted">لا توجد سجلات تقدم</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <h5>إحصائيات الحفظ</h5>
                                    <p class="mb-1"><strong>عدد السور المحفوظة:</strong> <span id="completedSurahs">0</span>/114</p>
                                    <p class="mb-1"><strong>عدد الأجزاء المحفوظة:</strong> <span id="completedJuz">0</span>/30</p>
                                    <p class="mb-1"><strong>عدد الصفحات المحفوظة:</strong> <span id="completedPages">0</span>/604</p>
                                    <p class="mb-0"><strong>عدد الآيات المحفوظة:</strong> <span id="completedVerses">0</span>/6236</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h5>معدل الحفظ الشهري</h5>
                            <canvas id="monthlyProgressChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">سجل التقدم الكامل</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>السورة</th>
                                <th>من آية</th>
                                <th>إلى آية</th>
                                <th>المعلم</th>
                                <th>ملاحظات</th>
                                <th>تقييم الحفظ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($progressLogs as $log)
                                <tr>
                                    <td>{{ $log->date->format('Y-m-d') }}</td>
                                    <td>{{ $log->surah_name }}</td>
                                    <td>{{ $log->start_verse }}</td>
                                    <td>{{ $log->end_verse }}</td>
                                    <td>{{ $log->teacher->name }}</td>
                                    <td>
                                        @if($log->notes)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $log->notes }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->evaluation_score)
                                            @if($log->evaluation_score >= 9)
                                                <span class="badge grade-badge grade-excellent">{{ $log->evaluation_score }}/10</span>
                                            @elseif($log->evaluation_score >= 7)
                                                <span class="badge grade-badge grade-good">{{ $log->evaluation_score }}/10</span>
                                            @elseif($log->evaluation_score >= 5)
                                                <span class="badge grade-badge grade-average">{{ $log->evaluation_score }}/10</span>
                                            @else
                                                <span class="badge grade-badge grade-poor">{{ $log->evaluation_score }}/10</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد سجلات تقدم</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $progressLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quran Progress Visualization -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">خريطة الحفظ</h5>
            </div>
            <div class="card-body">
                <div class="quran-map">
                    <!-- This will be populated by JavaScript -->
                    <div id="quranMap" class="d-flex flex-wrap justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .quran-map .surah-box {
        width: 50px;
        height: 50px;
        margin: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.2s;
    }
    
    .quran-map .surah-box.completed {
        background-color: #1cc88a;
        color: white;
    }
    
    .quran-map .surah-box.partial {
        background-color: #f6c23e;
        color: white;
    }
    
    .quran-map .surah-box.not-started {
        background-color: #e0e0e0;
        color: #666;
    }
    
    .quran-map .surah-box:hover {
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .quran-map .surah-tooltip {
        position: absolute;
        background-color: #343a40;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        z-index: 1000;
        display: none;
    }
</style>
@endsection

@section('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Progress Doughnut Chart
    const ctx = document.getElementById('progressChart').getContext('2d');
    
    // This would normally be calculated from the database
    // For demo purposes, we'll set a random percentage between 5-30%
    const progressPercentage = Math.floor(Math.random() * 25) + 5;
    document.getElementById('progressPercentage').textContent = progressPercentage + '%';
    
    // Also set random values for the statistics
    document.getElementById('completedSurahs').textContent = Math.floor(Math.random() * 20) + 5;
    document.getElementById('completedJuz').textContent = Math.floor(Math.random() * 5) + 1;
    document.getElementById('completedPages').textContent = Math.floor(Math.random() * 100) + 20;
    document.getElementById('completedVerses').textContent = Math.floor(Math.random() * 1000) + 200;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['تم الحفظ', 'متبقي'],
            datasets: [{
                data: [progressPercentage, 100 - progressPercentage],
                backgroundColor: ['#f6c23e', '#e0e0e0'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '75%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
    
    // Monthly Progress Chart
    const monthlyCtx = document.getElementById('monthlyProgressChart').getContext('2d');
    
    // Generate random data for the last 6 months
    const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
    const monthlyData = months.map(month => ({
        month: month,
        pages: Math.floor(Math.random() * 20) + 5
    }));
    
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'عدد الصفحات المحفوظة',
                data: monthlyData.map(item => item.pages),
                backgroundColor: '#f6c23e',
                borderWidth: 0,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // Quran Map Visualization
    const quranMap = document.getElementById('quranMap');
    const surahs = [
        "الفاتحة", "البقرة", "آل عمران", "النساء", "المائدة", "الأنعام", "الأعراف", "الأنفال", "التوبة", "يونس",
        "هود", "يوسف", "الرعد", "إبراهيم", "الحجر", "النحل", "الإسراء", "الكهف", "مريم", "طه",
        "الأنبياء", "الحج", "المؤمنون", "النور", "الفرقان", "الشعراء", "النمل", "القصص", "العنكبوت", "الروم",
        "لقمان", "السجدة", "الأحزاب", "سبأ", "فاطر", "يس", "الصافات", "ص", "الزمر", "غافر",
        "فصلت", "الشورى", "الزخرف", "الدخان", "الجاثية", "الأحقاف", "محمد", "الفتح", "الحجرات", "ق",
        "الذاريات", "الطور", "النجم", "القمر", "الرحمن", "الواقعة", "الحديد", "المجادلة", "الحشر", "الممتحنة",
        "الصف", "الجمعة", "المنافقون", "التغابن", "الطلاق", "التحريم", "الملك", "القلم", "الحاقة", "المعارج",
        "نوح", "الجن", "المزمل", "المدثر", "القيامة", "الإنسان", "المرسلات", "النبأ", "النازعات", "عبس",
        "التكوير", "الانفطار", "المطففين", "الانشقاق", "البروج", "الطارق", "الأعلى", "الغاشية", "الفجر", "البلد",
        "الشمس", "الليل", "الضحى", "الشرح", "التين", "العلق", "القدر", "البينة", "الزلزلة", "العاديات",
        "القارعة", "التكاثر", "العصر", "الهمزة", "الفيل", "قريش", "الماعون", "الكوثر", "الكافرون", "النصر",
        "المسد", "الإخلاص", "الفلق", "الناس"
    ];
    
    // Create a surah box for each surah
    surahs.forEach((surah, index) => {
        const surahNumber = index + 1;
        
        // Randomly assign a status (for demo purposes)
        let status;
        const random = Math.random();
        if (random < 0.1) {
            status = 'completed';
        } else if (random < 0.2) {
            status = 'partial';
        } else {
            status = 'not-started';
        }
        
        const surahBox = document.createElement('div');
        surahBox.className = `surah-box ${status}`;
        surahBox.textContent = surahNumber;
        surahBox.setAttribute('data-surah', surah);
        surahBox.setAttribute('data-number', surahNumber);
        
        // Add tooltip functionality
        surahBox.addEventListener('mouseover', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'surah-tooltip';
            tooltip.textContent = `${surahNumber}. ${surah}`;
            tooltip.style.top = (e.pageY - 30) + 'px';
            tooltip.style.left = (e.pageX + 10) + 'px';
            tooltip.id = 'surah-tooltip';
            document.body.appendChild(tooltip);
            tooltip.style.display = 'block';
        });
        
        surahBox.addEventListener('mousemove', function(e) {
            const tooltip = document.getElementById('surah-tooltip');
            if (tooltip) {
                tooltip.style.top = (e.pageY - 30) + 'px';
                tooltip.style.left = (e.pageX + 10) + 'px';
            }
        });
        
        surahBox.addEventListener('mouseout', function() {
            const tooltip = document.getElementById('surah-tooltip');
            if (tooltip) {
                document.body.removeChild(tooltip);
            }
        });
        
        quranMap.appendChild(surahBox);
    });
</script>
@endsection
