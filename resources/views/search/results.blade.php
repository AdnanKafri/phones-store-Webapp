@extends('layouts.app')

@section('title', 'البحث الذكي: ' . $query)

@section('content')
<div
    class="container py-5 ai-results-page"
    dir="rtl"
    data-ai-endpoint="{{ route('api.v1.ai.advisor') }}"
    data-search-url="{{ route('search') }}"
    data-product-base-url="{{ url('/products') }}"
>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">البحث الذكي</li>
            </ol>
        </nav>
        <div class="ai-results-hero">
            <div class="ai-results-hero__copy">
                <span class="ai-results-hero__badge">
                    <i class="bi bi-stars"></i>
                    <span>مستشار الهواتف الذكي</span>
                </span>
                <h1 class="fw-bold mb-2">ابحث عن الهاتف المناسب بلغة طبيعية</h1>
                <p class="text-muted mb-0">
                    اكتب ما تحتاجه بالعربية أو بالإنجليزية، وسنحوّل طلبك إلى فلاتر ذكية ثم نعرض أقرب الأجهزة المناسبة.
                </p>
            </div>

            <form id="ai-search-form" class="ai-results-form" action="{{ route('search') }}" method="GET">
                <label for="ai-query" class="form-label fw-semibold">صف الهاتف الذي تبحث عنه</label>
                <div class="ai-results-form__controls">
                    <input
                        id="ai-query"
                        type="search"
                        name="q"
                        class="form-control form-control-lg"
                        dir="auto"
                        value="{{ $query }}"
                        placeholder="مثال: بدي موبايل قوي للألعاب بسعر لا يتجاوز 450$"
                        required
                    >
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <span class="ai-submit-label">حلّل الطلب</span>
                        <span class="spinner-border spinner-border-sm d-none ai-submit-spinner" role="status" aria-hidden="true"></span>
                    </button>
                </div>
                <div class="ai-results-form__examples">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill ai-example-chip" data-query="بدي موبايل قوي للألعاب بسعر لا يتجاوز 450$">ألعاب تحت 450$</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill ai-example-chip" data-query="أريد iPhone مستعمل بكاميرا قوية">iPhone مستعمل بكاميرا قوية</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill ai-example-chip" data-query="هاتف بطارية قوية للاستخدام اليومي">بطارية قوية</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="ai-side-panel">
                <div class="ai-side-panel__section">
                    <h5 class="fw-bold mb-3">كيف فهم الذكاء طلبك</h5>
                    <div id="ai-query-summary" class="ai-query-summary">جاري تحليل طلبك الحالي...</div>
                </div>

                <div class="ai-side-panel__section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">الفلاتر المستخرجة</h6>
                        <span id="ai-match-strategy" class="badge text-bg-light">قيد التحليل</span>
                    </div>
                    <div id="ai-filters" class="d-flex flex-wrap gap-2"></div>
                    <p id="ai-fallback-note" class="small text-muted mt-3 mb-0 d-none"></p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ai-results-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">نتائج البحث الذكي</h4>
                        <p id="ai-results-subtitle" class="text-muted mb-0">جاري الاتصال بمستشار الذكاء الاصطناعي...</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill">تصفح كل المنتجات</a>
                </div>

                <div id="ai-loading-state" class="ai-state ai-state--loading">
                    <div class="spinner-grow text-primary mb-3" role="status" aria-hidden="true"></div>
                    <h5 class="fw-bold">جاري تحليل الطلب</h5>
                    <p class="text-muted mb-0">نحوّل طلبك إلى فلاتر ذكية ثم نبحث في المنتجات المناسبة.</p>
                </div>

                <div id="ai-error-state" class="ai-state d-none"></div>
                <div id="ai-empty-state" class="ai-state d-none"></div>
                <div id="ai-results-grid" class="row g-4 d-none"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.ai-results-page');

    if (!page) {
        return;
    }

    const form = document.getElementById('ai-search-form');
    const queryInput = document.getElementById('ai-query');
    const submitLabel = form.querySelector('.ai-submit-label');
    const submitSpinner = form.querySelector('.ai-submit-spinner');
    const summaryEl = document.getElementById('ai-query-summary');
    const filtersEl = document.getElementById('ai-filters');
    const fallbackNoteEl = document.getElementById('ai-fallback-note');
    const matchStrategyEl = document.getElementById('ai-match-strategy');
    const subtitleEl = document.getElementById('ai-results-subtitle');
    const loadingEl = document.getElementById('ai-loading-state');
    const errorEl = document.getElementById('ai-error-state');
    const emptyEl = document.getElementById('ai-empty-state');
    const gridEl = document.getElementById('ai-results-grid');

    const endpoint = page.dataset.aiEndpoint;
    const searchUrl = page.dataset.searchUrl;
    const productBaseUrl = page.dataset.productBaseUrl;
    const initialQuery = queryInput.value.trim();

    const filterLabels = {
        price_max: 'السعر الأقصى',
        price_min: 'السعر الأدنى',
        condition: 'الحالة',
        brand: 'العلامة',
        performance: 'الأداء',
        use_case: 'الاستخدام',
    };

    const enumLabels = {
        new: 'جديد',
        used: 'مستعمل',
        low: 'منخفض',
        medium: 'متوسط',
        high: 'عالٍ',
        gaming: 'ألعاب',
        camera: 'كاميرا',
        battery: 'بطارية',
        general: 'عام',
        strict: 'مطابقة كاملة',
        without_preferences: 'تخفيف تفضيلات',
        relaxed_budget: 'ميزانية مرنة',
        flexible_condition: 'حالة مرنة',
        brand_or_budget: 'علامة أو ميزانية',
        strict_no_results: 'لا نتائج',
        brand_or_budget_no_results: 'لا نتائج',
        flexible_condition_no_results: 'لا نتائج',
        relaxed_budget_no_results: 'لا نتائج',
        without_preferences_no_results: 'لا نتائج',
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatFilterValue(key, value) {
        if (key === 'price_max' || key === 'price_min') {
            return `$${Number(value).toLocaleString()}`;
        }

        return enumLabels[value] ?? value;
    }

    function setSubmitting(isSubmitting) {
        queryInput.disabled = isSubmitting;
        form.querySelector('button[type="submit"]').disabled = isSubmitting;
        submitSpinner.classList.toggle('d-none', !isSubmitting);
        submitLabel.textContent = isSubmitting ? 'جاري التحليل' : 'حلّل الطلب';
    }

    function resetStates() {
        loadingEl.classList.remove('d-none');
        errorEl.classList.add('d-none');
        emptyEl.classList.add('d-none');
        gridEl.classList.add('d-none');
        gridEl.innerHTML = '';
    }

    function renderFilters(filters, searchMeta) {
        summaryEl.textContent = queryInput.value.trim();
        filtersEl.innerHTML = '';
        fallbackNoteEl.classList.add('d-none');
        fallbackNoteEl.textContent = '';

        const entries = Object.entries(filters || {});

        if (entries.length === 0) {
            filtersEl.innerHTML = '<span class="text-muted small">لم يتم استخراج فلاتر محددة، لذلك سيعتمد البحث على التوصية العامة.</span>';
        } else {
            entries.forEach(([key, value]) => {
                const chip = document.createElement('span');
                chip.className = 'ai-filter-chip';
                chip.innerHTML = `<strong>${escapeHtml(filterLabels[key] ?? key)}:</strong> ${escapeHtml(formatFilterValue(key, value))}`;
                filtersEl.appendChild(chip);
            });
        }

        const strategy = searchMeta?.match_strategy ?? 'strict';
        matchStrategyEl.textContent = enumLabels[strategy] ?? strategy;

        if (searchMeta?.fallback_applied) {
            const relaxed = Array.isArray(searchMeta.relaxed_filters) && searchMeta.relaxed_filters.length > 0
                ? `تم توسيع البحث عبر تخفيف: ${searchMeta.relaxed_filters.map((item) => filterLabels[item] ?? item).join('، ')}.`
                : 'تم توسيع البحث للحصول على نتائج أقرب لطلبك.';

            fallbackNoteEl.textContent = relaxed;
            fallbackNoteEl.classList.remove('d-none');
        }
    }

    function renderProducts(products, searchMeta) {
        gridEl.innerHTML = products.map((product) => {
            const imageUrl = product.primary_image_url;
            const productTitle = `${product.brand ?? ''} ${product.model ?? ''}`.trim();
            const sellerLocation = product.seller?.location || product.location || 'غير محدد';
            const categoryName = product.category?.name || 'هواتف';

            return `
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 ai-product-card">
                        <div class="ai-product-card__media">
                            <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(productTitle)}">
                            <span class="badge ${product.condition === 'new' ? 'text-bg-success' : 'text-bg-warning'} ai-product-card__badge">
                                ${escapeHtml(enumLabels[product.condition] ?? product.condition ?? 'منتج')}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <h5 class="fw-bold mb-1">${escapeHtml(productTitle)}</h5>
                                    <p class="text-muted small mb-0">${escapeHtml(categoryName)}</p>
                                </div>
                                <span class="ai-product-card__price">$${Number(product.price ?? 0).toLocaleString()}</span>
                            </div>
                            <p class="text-muted small mb-3">${escapeHtml(product.description || 'لا يوجد وصف إضافي لهذا الجهاز حالياً.')}</p>
                            <div class="ai-product-card__meta">
                                <span><i class="bi bi-geo-alt"></i>${escapeHtml(sellerLocation)}</span>
                                <span><i class="bi bi-person"></i>${escapeHtml(product.seller?.name || 'البائع')}</span>
                            </div>
                            <div class="mt-auto pt-3 d-flex justify-content-between align-items-center gap-2">
                                <span class="badge text-bg-light">${escapeHtml(product.source === 'inventory' ? 'من المتجر' : 'إعلان مستخدم')}</span>
                                <a href="${escapeHtml(`${productBaseUrl}/${product.id}`)}" class="btn btn-outline-primary rounded-pill px-3">عرض التفاصيل</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        gridEl.classList.remove('d-none');
        subtitleEl.textContent = `تم العثور على ${searchMeta?.result_count ?? products.length} نتيجة مناسبة لطلبك.`;
    }

    function renderEmpty(searchMeta) {
        emptyEl.innerHTML = `
            <i class="bi bi-emoji-neutral ai-state__icon"></i>
            <h5 class="fw-bold">لم نجد نتائج مناسبة بعد</h5>
            <p class="text-muted mb-3">
                جرّب صياغة أبسط أو وسّع الميزانية أو اذكر العلامة التجارية بشكل أقل تحديداً.
            </p>
            ${searchMeta?.fallback_applied ? '<p class="small text-muted mb-0">قمنا بالفعل بمحاولة توسيع البحث تلقائياً قبل عرض هذه الحالة.</p>' : ''}
        `;
        emptyEl.classList.remove('d-none');
        subtitleEl.textContent = 'لم يتم العثور على منتجات مطابقة حالياً.';
    }

    function renderError(message, code) {
        const isOutOfScope = code === 'AI_OUT_OF_SCOPE';

        errorEl.innerHTML = `
            <i class="bi ${isOutOfScope ? 'bi-chat-square-text' : 'bi-exclamation-octagon'} ai-state__icon"></i>
            <h5 class="fw-bold">${isOutOfScope ? 'هذا الطلب خارج نطاق مستشار الهواتف' : 'تعذر إكمال البحث الذكي'}</h5>
            <p class="text-muted mb-3">${escapeHtml(message)}</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <button type="button" class="btn btn-primary rounded-pill ai-retry-button">جرّب مرة أخرى</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill">تصفح المنتجات يدوياً</a>
            </div>
        `;

        errorEl.querySelector('.ai-retry-button').addEventListener('click', () => runSearch(queryInput.value.trim()));
        errorEl.classList.remove('d-none');
        subtitleEl.textContent = isOutOfScope
            ? 'المستشار الذكي مخصص فقط لطلبات شراء الهواتف.'
            : 'حدثت مشكلة أثناء التواصل مع خدمة الذكاء الاصطناعي.';
    }

    async function runSearch(query) {
        if (!query) {
            return;
        }

        resetStates();
        setSubmitting(true);
        summaryEl.textContent = query;
        filtersEl.innerHTML = '';
        matchStrategyEl.textContent = 'قيد التحليل';
        subtitleEl.textContent = 'جاري تحليل طلبك وإيجاد أفضل المطابقات...';

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ query }),
            });

            const payload = await response.json();

            if (!response.ok) {
                throw {
                    message: payload.message || 'حدث خطأ غير متوقع أثناء البحث الذكي.',
                    code: payload.code || 'AI_SEARCH_ERROR',
                };
            }

            const data = payload.data || {};
            const filters = data.filters || {};
            const products = data.products || [];
            const searchMeta = data.search_meta || null;

            renderFilters(filters, searchMeta);
            loadingEl.classList.add('d-none');

            if (products.length === 0) {
                renderEmpty(searchMeta);
            } else {
                renderProducts(products, searchMeta);
            }
        } catch (error) {
            loadingEl.classList.add('d-none');
            renderError(error.message || 'حدث خطأ غير متوقع أثناء البحث الذكي.', error.code || 'AI_SEARCH_ERROR');
        } finally {
            setSubmitting(false);
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const query = queryInput.value.trim();

        if (!query) {
            queryInput.focus();
            return;
        }

        const url = new URL(searchUrl, window.location.origin);
        url.searchParams.set('q', query);
        window.history.replaceState({}, '', url);
        runSearch(query);
    });

    document.querySelectorAll('.ai-example-chip').forEach((button) => {
        button.addEventListener('click', () => {
            queryInput.value = button.dataset.query || '';
            form.requestSubmit();
        });
    });

    if (initialQuery) {
        runSearch(initialQuery);
    }
});
</script>
@endpush
