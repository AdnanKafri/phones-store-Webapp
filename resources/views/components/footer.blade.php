<footer class="footer bg-white border-top pt-5 pb-3">
    <div class="container">
        <div class="row text-center text-md-end">
            <div class="col-md-4 mb-4">
                <a href="{{ url('/') }}" class="footer-brand d-inline-block mb-3 text-decoration-none">
                    <h4 class="fw-bold text-primary"><i class="bi bi-phone"></i> PhoneMarket</h4>
                </a>
                <p class="text-muted">
                    المنصة الرائدة لبيع وشراء الجوالات المستعملة والجديدة.
                    نوفر بيئة آمنة للبائعين والمشترين مع خيارات دفع متنوعة وضمان للجودة.
                </p>
                <div class="d-flex gap-3 justify-content-center justify-content-md-start">
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            
            <div class="col-md-2 mb-4">
                <h5 class="fw-bold mb-3">روابط سريعة</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-decoration-none text-muted">الرئيسية</a></li>
                    <li class="mb-2"><a href="{{ route('products.index') }}" class="text-decoration-none text-muted">كل المنتجات</a></li>
                    <li class="mb-2"><a href="{{ route('categories.index') }}" class="text-decoration-none text-muted">التصنيفات</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">من نحن</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">اتصل بنا</a></li>
                </ul>
            </div>
            
            <div class="col-md-2 mb-4">
                <h5 class="fw-bold mb-3">أقسام مختارة</h5>
                <ul class="list-unstyled">
                    @php
                        $footerCategories = \App\Models\Category::take(5)->get();
                    @endphp
                    @foreach($footerCategories as $category)
                        <li class="mb-2"><a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none text-muted">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">تواصل معنا</h5>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i> 123 شارع التقنية، دمشق، سوريا</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> support@phonemarket.com</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> +963 999 123 456</li>
                </ul>
                <div class="mt-4">
                    <p class="mb-2 small fw-bold">اشترك في النشرة البريدية</p>
                    <div class="input-group" dir="ltr">
                        <button class="btn btn-primary px-3 rounded-start-pill" type="button">اشتراك</button>
                        <input type="email" class="form-control rounded-end-pill border-start-0 text-end" placeholder="بريدك الإلكتروني">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4 pt-4 border-top">
            <div class="col-12 text-center text-muted small">
                &copy; {{ date('Y') }} PhoneMarket. جميع الحقوق محفوظة. تم التطوير بحب <i class="bi bi-heart-fill text-danger mx-1"></i> لأجلكم.
            </div>
        </div>
    </div>
</footer>
