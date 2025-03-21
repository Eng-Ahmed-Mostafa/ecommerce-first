@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Category infomation</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.categories') }}">
                        <div class="text-tiny">Categories</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Category</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{ route('admin.category.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Category Name <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" type="text" placeholder="Category name" name="name" id="name"
                        tabindex="0" value="{{ old('name') }}" aria-required="true" required="">
                </fieldset>
                @error('name')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Category Slug <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" type="text" placeholder="Category Slug" name="slug" id="slug"
                        tabindex="0" value="{{ old('slug') }}" aria-required="true" required="">
                </fieldset>
                @error('slug')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <!-- عنصر الصورة (مخفي افتراضيًا) -->
                        <div class="item text-center position-relative" id="imgpreview" style="display: none;">
                            <img id="previewImg" class="effect8 img-fluid rounded shadow" alt="Preview Image">
                        </div>

                        <!-- زر رفع الصورة -->
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">
                                    Drop your images here or select <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')
                    <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        // توليد slug تلقائيًا عند إدخال name
        $("#name").on("input", function () {
            let name = $(this).val();
            let slug = name.toLowerCase().replace(/ /g, "-").replace(/[^\w-]+/g, "");
            $("#slug").val(slug);
        });

        // عرض الصورة المحددة قبل الرفع
        $("#myFile").on("change", function (event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $("#previewImg").attr("src", e.target.result);
                    $("#imgpreview").show();
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush